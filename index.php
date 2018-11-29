<?php
// Slack app credentials
define("SIGNING_SECRET", "0d5b309cb4c07a5922ea8154186fcaf9");

// Slack API settings
define("API_URL", "https://slack.com/api/");
define("API_METHOD", "users.profile.set");

// Slack Authentication settings
define("AUTH_TYPE", "Bearer");
define("AUTH_PREFIX", "xoxp");
define("AUTH_TOKEN", "33584152470-238146475475-491173017538-ab45b16cf4769b6fc4d56a8e9bf83489");

// Get request body
$req_body = file_get_contents('php://input');
$req_timestamp = $_SERVER['HTTP_X_SLACK_REQUEST_TIMESTAMP'];
$req_signature = $_SERVER['HTTP_X_SLACK_SIGNATURE'];

// Check if request is older than 5 minutes
$cur_time = new DateTime();
$req_time = (new DateTime())->setTimestamp($req_timestamp);
$time_int = $cur_time->diff($req_time);
if ($time_int->format('%i') >= 5) exit;

// Test if request is authenticated
$version_number = "v0";
$base_string = implode(":", [$version_number, $req_timestamp, $req_body]);
$hash_string = hash_hmac("sha256", $base_string, SIGNING_SECRET);
$calc_signature = sprintf("%s=%s", $version_number, $hash_string);
if ($calc_signature !== $req_signature) exit;

// Configure data to be sent during API call
$emojis = [
    ":pancakes:",
    ":cut_of_meat:",
    ":hamburger:",
    ":pizza:",
    ":stuffed_flatbread:",
    ":shallow_pan_of_food:",
    ":stew:",
    ":green_salad:",
    ":curry:",
    ":ramen:",
    ":spaghetti:",
];
$data = [
    "profile" => [
        "status_text" => "I'm having lunch!",
        "status_emoji" => $emojis[array_rand($emojis)],
    ],
];
$data_string = json_encode($data);

// Configure Slack API call
$ch = curl_init();
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_URL => API_URL . API_METHOD,
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => $data_string,
    CURLOPT_HTTPHEADER => [
        "Content-Type: " . "application/json; charset=utf-8",
        "Authorization: " . sprintf("%s %s-%s", AUTH_TYPE, AUTH_PREFIX, AUTH_TOKEN),
        "Content-Length: " . strlen($data_string),
    ],
]);

// Send API call and store result
$result = curl_exec($ch);
curl_close($ch);
