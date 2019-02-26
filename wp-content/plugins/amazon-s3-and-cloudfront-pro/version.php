<?php
$version = '2.0.2';

// We set versions for both slugs to avoid undefined index errors for free slug
$GLOBALS['aws_meta']['amazon-s3-and-cloudfront-pro']['version'] = $version;
$GLOBALS['aws_meta']['amazon-s3-and-cloudfront']['version']     = $version;

$GLOBALS['aws_meta']['amazon-s3-and-cloudfront-pro']['supported_addon_versions'] = array(
	'amazon-s3-and-cloudfront-assets-pull' => '1.1.1',
);
