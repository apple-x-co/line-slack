<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Dotenv\Dotenv;

$dotenv = Dotenv::create(__DIR__ . '/../');
$dotenv->load();

$body = file_get_contents('php://input');

$result = new \Slack\CommandResult($body);

if ($result->getToken() !== getenv('SLACK_VERIFICATION_TOKEN')) {
    error_log('token error');
    error_log($result->getToken());
    exit(1);
}

error_log($result->getCommand());
error_log($result->getText());

echo 'command accepted.';