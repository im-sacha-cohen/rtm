<?php

$headers = getallheaders();
$input = file_get_contents('php://input');

$sig_check = 'sha1=' . hash_hmac('sha1', $input, 'jHy0cbk097nKZWtvYv6yX59Ttw8r7Sk2F9Wwaqs7J1M5nTSk0J');

if ($sig_check === $headers['X-Hub-Signature']) {
    shell_exec('git pull');
    shell_exec('/usr/bin/php8.0-cli bin/console cache:clear');
    echo 'Pull successfully';
    http_response_code(200);
} else {
    http_response_code(401);
}