<?php

// Get Slack API key.
$secrets = json_decode(file_get_contents('https://dev-dunder-mifflin-legacy-site.pantheonsite.io/quicksilver.php?name=qs'), 1);
$slack_url = $secrets['slack_url'];

// Customize the message based on the workflow type.  Note that slack_notification.php
// must appear in your pantheon.yml for each workflow type you wish to send notifications on.

switch ($_POST['wf_type']) {
  case 'deploy':
    // Find out what tag we are on and get the annotation.
    $deploy_tag = `git describe --tags`;
    $deploy_message = !empty($_POST['deploy_message']) ? $_POST['deploy_message'] : "";

    // Prepare the slack payload as per:
    // https://api.slack.com/incoming-webhooks
    $text = 'Deploy to the ' . $_ENV['PANTHEON_ENVIRONMENT'];
    $text .= ' environment of ' . $_ENV['PANTHEON_SITE_NAME'] . ' by ' . $_POST['user_email'] . ' complete!' . PHP_EOL;
    $text .= PHP_EOL . $deploy_message;

    break;

  case 'sync_code':
    // Get the committer, hash, and message for the most recent commit.
    $committer = `git log -1 --pretty=%cn`;
    $email = `git log -1 --pretty=%ce`;
    $message = `git log -1 --pretty=%B`;
    $hash = `git log -1 --pretty=%h`;

    // Prepare the slack payload as per:
    // https://api.slack.com/incoming-webhooks
    $text = 'Code sync to the ' . $_ENV['PANTHEON_ENVIRONMENT'] . ' environment of ' . $_ENV['PANTHEON_SITE_NAME'] . ' by ' . $_POST['user_email'] . "!\n";
    $text .= 'Most recent commit: ' . rtrim($hash) . ' by ' . rtrim($committer) . ': ' . $message . PHP_EOL;
    $text .= $message;
    break;

  case 'clear_cache':
    $fields[] = array(
      'title' => 'Cleared caches',
      'value' => 'Cleared caches on the ' . $_ENV['PANTHEON_ENVIRONMENT'] . ' environment of ' . $_ENV['PANTHEON_SITE_NAME'] . "!\n",
      'short' => 'false'
    );
    $pretext = 'Caches cleared :construction:';
    break;

  case 'deploy_product':
    // Get Pantheon metadata
    $req = pantheon_curl('https://api.live.getpantheon.com/sites/self/attributes', NULL, 8443);
    $meta = json_decode($req['body'], true);
    $title = $meta['label'];
    $email = $_POST['user_email'];
    $text = "\n" . ':ship: Created a new site: ' . $_ENV['PANTHEON_SITE_NAME'] . "\n";
    $resetLink = 'https://' . $_ENV['PANTHEON_ENVIRONMENT'] . '-' . $_ENV['PANTHEON_SITE_NAME'] . '.pantheonsite.io/wp-login.php?action=lostpassword&user_login=' . $email;
    $text .= 'Reset Password Link: ' . $resetLink . "\n";

    break;

  default:
    $text = $_POST['qs_description'];
    break;
}

/**
 * Send a notification to slack
 */
$post = [
  "Site" => $_ENV['PANTHEON_SITE_NAME'],
  "User" => $_POST['user_email'],
  "Dashboard Link" => 'https://dashboard.pantheon.io/sites/' . $_ENV['PANTHEON_SITE'] . '#' . $_ENV['PANTHEON_ENVIRONMENT'] . '/deploys',
  "Site Link" => "https://{$_ENV['PANTHEON_ENVIRONMENT']}-{$_ENV['PANTHEON_SITE_NAME']}.pantheonsite.io",
  "Environment" => $_ENV['PANTHEON_ENVIRONMENT'],
  "Stage" => ucfirst($_POST['stage']) . ' ' . str_replace('_', ' ',  $_POST['wf_type']),
  "Workflow" => $_POST['wf_description'],
  "Description" => $_POST['qs_description'],
  "Activity" => $text,
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