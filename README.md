# phpApi
I have written the requested operations by creating two endpoints:
1.	Update Function:
post setConstructionStagesById/(:id) 
2.	Delete Function:
put  delConstructionStagesById/(:id)




The first endpoint checks the necessary parameters for an update, converts them to the desired format and structure for tasks 2 and 3, and performs the update. Otherwise, it returns an error code.
http://localhost/phpApi/index.php/setConstructionStagesById/1
1.	post setConstructionStagesById/(:id)->
{
"id": "1",
"name": "Pension Mühlbachtal 36",
"start_date": "2022-12-31T14:59:00Z",
"end_date": "2023-12-31T14:59:00Z",
"durationUnit": "DAYS",
"color": "#111",
"externalId": "166",
"status": "NEW"
}

Response:
[
    {
        "data": [
            {
                "id": "1",
                "name": "Pension Mühlbachtal 36",
                "startDate": "2022-12-31T14:59:00Z",
                "endDate": "2023-12-31T14:59:00Z",
                "duration": "365.0",
                "durationUnit": "DAYS",
                "color": "#111",
                "externalId": "166",
                "status": "NEW"
            }
        ],
        "code": "200",
        "message": "request successful"
    }
]

Weeks duration exam

{
"id": "1",
"name": "Pension Mühlbachtal 36",
"start_date": "2022-12-31T14:59:00Z",
"end_date": "2023-12-31T14:59:00Z",
"durationUnit": "WEEKS",
"color": "#111",
"externalId": "166",
"status": "NEW"
}

Response:
[
    {
        "data": [
            {
                "id": "1",
                "name": "Pension Mühlbachtal 36",
                "startDate": "2022-12-31T14:59:00Z",
                "endDate": "2023-12-31T14:59:00Z",
                "duration": "52.0",
                "durationUnit": "WEEKS",
                "color": "#111",
                "externalId": "166",
                "status": "NEW"
            }
        ],
        "code": "200",
        "message": "request successful"
    }
]

Error code 
1.	  Date Control 

{
"id": "1",
"name": "Pension Mühlbachtal 36",
"start_date": "2022",
"end_date": "2023-12-31T14:59:00Z",
"durationUnit": "WEEKS",
"color": "#111",
"externalId": "166",
"status": "NEW"
}

RESPONSE
[
    {
        "data": null,
        "code": "400",
        "message": "start_date is not a valid ISO8601 date and time format."
    }
]

2.	Status Control

{
"id": "1",
"name": "Pension Mühlbachtal 36",
"start_date": "2022-12-31T14:59:00Z",
"end_date": "2023-12-31T14:59:00Z",
"durationUnit": "WEEKS",
"color": "#111",
"externalId": "166",
"status": "Other"
}

RESPONSE
[
    {
        "data": null,
        "code": "400",
        "message": "Invalid request, please send status correctly(NEW,DELETED,PLANNED)"
    }
]

3.	durationUnit Control

{
"id": "1",
"name": "Pension Mühlbachtal 36",
"start_date": "2022-12-31T14:59:00Z",
"end_date": "2023-12-31T14:59:00Z",
"durationUnit": "Year",
"color": "#111",
"externalId": "166",
"status": "NEW"
}

RESPONSE
[
    {
        "data": null,
        "code": "400",
        "message": "durationUnit must be a valid unit (HOURS, DAYS, WEEKS)."
    }
]

4.	color Control

{
"id": "1",
"name": "Pension Mühlbachtal 36",
"start_date": "2022-12-31T14:59:00Z",
"end_date": "2023-12-31T14:59:00Z",
"durationUnit": "DAYS",
"color": "red",
"externalId": "166",
"status": "NEW"
}

RESPONSE
[
    {
        "data": null,
        "code": "400",
        "message": "Color is not null and not a valid HEX color."
    }
]

http://localhost/phpApi/index.php/ delConstructionStagesById/1
2.	put  delConstructionStagesById/(:id)->response
[
    {
        "data": [
            {
                "id": "1",
                "name": "Pension Mühlbachtal 36",
                "startDate": "2022-12-31T14:59:00Z",
                "endDate": "2023-12-31T14:59:00Z",
                "duration": "52.0",
                "durationUnit": "WEEKS",
                "color": "#111",
                "externalId": "166",
                "status": "DELETED"
            }
        ],
        "code": "200",
        "message": "request successful"
    }
]

http://localhost/phpApi/index.php/ delConstructionStagesById/1000
if not this error is returned
[
    {
        "data": null,
        "code": "404",
        "message": "Not Found"
    }
]




