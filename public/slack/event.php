<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Dotenv\Dotenv;

$dotenv = Dotenv::create(__DIR__ . '/../');
$dotenv->load();

$body = file_get_contents('php://input');
error_log(print_r($body, true));

// mention message
//{
//  "token": "vDhLPAaHITeq1GFnRZeJHoPz",
//	"team_id": "T0454LWHB",
//	"api_app_id": "ALAHMAZ1C",
//	"event": {
//    "client_msg_id": "8eca2e08-355b-4672-a506-02bf2596b3f3",
//		"type": "app_mention",
//		"text": "<@UKX2KDL91> hello",
//		"user": "U3Q2NNAUF",
//		"ts": "1562657462.002000",
//		"team": "T0454LWHB",
//		"channel": "CKX1SB24A",
//		"event_ts": "1562657462.002000"
//	},
//	"type": "event_callback",
//	"event_id": "EvLAARKPQF",
//	"event_time": 1562657462,
//	"authed_users": [
//    "UKX2KDL91"
//]
//}

// mention message in thread （parent_user_idがつく）
//{
//  "token": "vDhLPAaHITeq1GFnRZeJHoPz",
//	"team_id": "T0454LWHB",
//	"api_app_id": "ALAHMAZ1C",
//	"event": {
//    "client_msg_id": "aa4a7562-a5ed-44a4-9e7f-d3223714d9ff",
//		"type": "app_mention",
//		"text": "<@UKX2KDL91> hello2",
//		"user": "U3Q2NNAUF",
//		"ts": "1562658521.002100",
//		"team": "T0454LWHB",
//		"thread_ts": "1562657454.001800",
//		"parent_user_id": "U3Q2NNAUF",
//		"channel": "CKX1SB24A",
//		"event_ts": "1562658521.002100"
//	},
//	"type": "event_callback",
//	"event_id": "EvL25ATEKT",
//	"event_time": 1562658521,
//	"authed_users": [
//    "UKX2KDL91"
//]
//}