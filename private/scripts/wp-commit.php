<?php

/**
 * Autocommit plugins.
 */

$name = $_POST['user_fullname'];
$email = $_POST['user_email'];

$payload = json_encode([
    'type' => 'commit_and_push_on_server_changes',
    'environment' => $_ENV['PANTHEON_ENVIRONMENT'],
    'params' => [
        'message' => 'Add site plugins',
        'committer_name' => $_POST['user_fullname'],
        'committer_email' => $_POST['user_email'],
    ]
]);

$req = pantheon_curl('https://api.live.getpantheon.com/sites/self/environments/self/workflows', $payload, 8443, 'POST');
$meta = json_decode($req['body'], true);

echo "Workflow...\n";
print_r($meta);
