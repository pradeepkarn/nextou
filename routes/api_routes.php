<?php
$api_routes = [
    "/api/v1/account/create/{ug}" => 'Users_api@create_account@name.createAccountApi',
    "/api/v1/account/update/{ug}" => 'Users_api@update_account@name.updateAccountApi',
    "/api/v1/account/login/{ug}" => 'Users_api@login@name.loginAccountApi',
    "/api/v1/account/login-via-token/{ug}" => 'Users_api@login_via_token@name.loginAccountViaTokenApi',
    
    "/api/v1/qr/scan" => 'QR_api@scan_data@name.scanQrCodeApi',
    "/api/v1/qr/scanned-data" => 'QR_api@get_scanned_data@name.scanneaCodeApi',

    "/api/v1/event/list" => 'Event_api@list@name.eventListApi',
    "/api/v1/event/employees" => 'Event_api@get_event_employees@name.eventEmployessApi',

    "/api/v1/event/generate-report" => 'Event_api@event_report_generate@name.generateReportApi',
];