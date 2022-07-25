<?php

// Get paths for imports
$path  = $_SERVER['DOCUMENT_ROOT'] . '/private/data';

// Import data into WordPress
echo "Importing default content...\n";
passthru("wp import $path/sample-data.xml --authors=skip");
echo "Import complete.\n";
