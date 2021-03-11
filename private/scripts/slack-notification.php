<?php

// Get Slack API key.
$secrets = json_decode(file_get_contents('https://dev-dunder-mifflin-legacy-site.pantheonsite.io/quicksilver.php?name=qs'), 1);
$slack_url = $secrets['slack_url'];

/**
 * Send a notification to slack
 */
$post = [
  "Site" => $_ENV['PANTHEON_SITE_NAME'],
  "User" => $_POST['user_email'],
  "Dashboard Link" => 'https://dashboard.pantheon.io/sites/' . $_ENV['PANTHEON_SITE'] . '#' . $_ENV['PANTHEON_ENVIRONMENT'] . '/deploys',
  "Environment" => $_ENV['PANTHEON_ENVIRONMENT'],
  "Stage" => ucfirst($_POST['stage']) . ' ' . str_replace('_', ' ',  $_POST['wf_type']),
  "Workflow" => $_POST['wf_description'],
  "Description" => $_POST['qs_description']
];

// Initiate request
$payload = json_encode($post);
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $slack_url);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_TIMEOUT, 5);
curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);

// Watch for messages with `terminus workflows watch --site=SITENAME`
print("\n==== Posting to Slack ====\n");
$result = curl_exec($ch);
print("RESULT: $result");
// $payload_pretty = json_encode($post,JSON_PRETTY_PRINT); // Uncomment to debug JSON
// print("JSON: $payload_pretty"); // Uncomment to Debug JSON
print("\n===== Post Complete! =====\n");
curl_close($ch);
