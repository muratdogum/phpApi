<?php

class ConstructionStages
{
	private $db;

	public function __construct()
	{
		$this->db = Api::getDb();
	}
	public function response($data, $code, $message)
	{
		$list[] = [
			"data" => $data,
			"code" => $code,
			"message" => $message

		];
		return $list;
	}
	
	public function getAll()
	{
		$stmt = $this->db->prepare("
			SELECT
				ID as id,
				name, 
				strftime('%Y-%m-%dT%H:%M:%SZ', start_date) as startDate,
				strftime('%Y-%m-%dT%H:%M:%SZ', end_date) as endDate,
				duration,
				durationUnit,
				color,
				externalId,
				status
			FROM construction_stages
		");
		$stmt->execute();
		return $stmt->fetchAll(PDO::FETCH_ASSOC);
	}
	public function updateTable($id, $keys, $params)
	{
		$query = "Update construction_stages set ";
		for ($i = 0; $i < count($keys); $i++) {
			if ($params[$keys[$i]]) {
				if (count($keys) == ($i + 1)) {
					$query .=	$keys[$i] . "=:" . $keys[$i];
				} else {
					$query .=	$keys[$i] . "=:" . $keys[$i] . ",";
				}
			}
		}
		$query .= " where `id`='" . $id . "'";
		$stmt = $this->db->prepare($query);
		for ($i = 0; $i < count($keys); $i++) {
			if ($params[$keys[$i]]) {
				$stmt->bindParam(":" . $keys[$i], $params[$keys[$i]], PDO::PARAM_STR);
			}
		}
		$stmt->execute();
		$response = ConstructionStages::response(
			ConstructionStages::getSingle($id),
			"200",
			"request successful"
		);
		return  $response;
	}
	public function setConstructionStagesById(ConstructionStagesUpdate $params, $id)
	{
		if ($id) {
			if (!ConstructionStages::getSingle($id)) {
			return $response = ConstructionStages::response(
				null,
				"404",
				"Not Found"
			);
		}
		if (!($params->status == "NEW" || $params->status == "DELETED" || $params->status == "PLANNED")) {
			return $response = ConstructionStages::response(
				null,
				"400",
				"Invalid request, please send status correctly(NEW,DELETED,PLANNED)"
			);
		}
		if (strlen($params->name) > 255) {
			return $response = ConstructionStages::response(
				null,
				"400",
				"Name is too long. Name must be a maximum of 255 characters."
			);
		}
		if (strlen($params->externalId) > 255) {
			return $response = ConstructionStages::response(
				null,
				"400",
				"externalId is too long. externalId must be a maximum of 255 characters."
			);
		}
		if (!ConstructionStages::isValidISO8601DateTime($params->start_date)) {
			return $response = ConstructionStages::response(
				null,
				"400",
				"start_date is not a valid ISO8601 date and time format."
			);
		}
		if ($params->end_date === null) {
			$params->duration = null;
		} else {
			$start_datetime = new DateTime($params->start_date);
			$end_datetime = new DateTime($params->end_date);

			if ($end_datetime > $start_datetime) {
				if (!ConstructionStages::isValidDurationUnit($params->durationUnit)) {
					return $response = ConstructionStages::response(
						null,
						"400",
						"durationUnit must be a valid unit (HOURS, DAYS, WEEKS)."
					);	
				}
					$params->duration = ConstructionStages::calculateDuration($start_datetime, $end_datetime, $params->durationUnit ? $params->durationUnit : null);
			} else {
				return $response = ConstructionStages::response(
					null,
					"400",
					"Error: end_date must be later than start_date."
				);
			}
		}
		if (!($params->color === null || ConstructionStages::isValidHexColor($params->color))) {
			return $response = ConstructionStages::response(
				null,
				"400",
				"Color is not null and not a valid HEX color."
			);
		}
		$array = get_object_vars($params);
		$properties = array_keys($array);
			return ConstructionStages::updateTable($id, $properties, $array);
		} else {
			return $response = ConstructionStages::response(
				null,
				"400",
				"bad request"
			);
		}
	}
	public function isValidISO8601DateTime($dateString)
	{
		$dateTime = DateTime::createFromFormat(DateTime::ATOM, $dateString);
		$errors = DateTime::getLastErrors();
		return $dateTime && $errors['warning_count'] === 0 && $errors['error_count'] === 0;
	}
	public  function calculateDuration($startDateTime, $endDateTime, $durationUnit = "DAYS")
	{
		$interval = $startDateTime->diff($endDateTime);

		switch ($durationUnit) {
			case "HOURS":
				return $interval->h;
			case "DAYS":
				return $interval->days;
			case "WEEKS":
				return floor($interval->days / 7);
			default:
				return null;
		}
	}
	
	public  function isValidHexColor($color) {
			$pattern = '/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/';
			return preg_match($pattern, $color) === 1;
	}
	public  function isValidDurationUnit($durationUnit)
	{
		$validUnits = array("HOURS", "DAYS", "WEEKS");
		return in_array($durationUnit, $validUnits);
	}

	public function delConstructionStagesById($id)
	{
		if (ConstructionStages::getSingle($id)) {
			$query = "Update construction_stages set status='DELETED' where `id`='" . $id . "'";
			$stmt = $this->db->prepare($query);
			$stmt->execute();
			$response = ConstructionStages::response(
				ConstructionStages::getSingle($id),
				"200",
				"request successful"
			);
			return  $response;
		} else {
			return $response = ConstructionStages::response(
				null,
				"404",
				"Not Found"
			);
		}
	}
	public function getSingle($id)
	{
		$stmt = $this->db->prepare("
			SELECT
				ID as id,
				name, 
				strftime('%Y-%m-%dT%H:%M:%SZ', start_date) as startDate,
				strftime('%Y-%m-%dT%H:%M:%SZ', end_date) as endDate,
				duration,
				durationUnit,
				color,
				externalId,
				status
			FROM construction_stages
			WHERE ID = :id
		");
		$stmt->execute(['id' => $id]);
		return $stmt->fetchAll(PDO::FETCH_ASSOC);
	}

	public function post(ConstructionStagesCreate $data)
	{
		$stmt = $this->db->prepare("
			INSERT INTO construction_stages
			    (name, start_date, end_date, duration, durationUnit, color, externalId, status)
			    VALUES (:name, :start_date, :end_date, :duration, :durationUnit, :color, :externalId, :status)
			");
		$stmt->execute([
			'name' => $data->name,
			'start_date' => $data->startDate,
			'end_date' => $data->endDate,
			'duration' => $data->duration,
			'durationUnit' => $data->durationUnit,
			'color' => $data->color,
			'externalId' => $data->externalId,
			'status' => $data->status,
		]);
		return $this->getSingle($this->db->lastInsertId());
	}
}
