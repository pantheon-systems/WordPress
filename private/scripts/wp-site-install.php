<?php

$req = pantheon_curl('https://api.live.getpantheon.com/sites/self/attributes', NULL, 8443);
$meta = json_decode($req['body'], true);

// Install from profile.
echo "Installing WordPress core...\n";
$title = $meta['label'];
$email = $_POST['user_email'];
passthru("wp core install --title='$title' --admin_user='superuser' --admin_email='$email'");
echo "Installation complete.\n";
