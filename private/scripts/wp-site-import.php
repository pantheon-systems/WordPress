<?php

// Import data into WordPress
echo "Importing default content...\n";
passthru("wp import ../data/sample-data.xml --authors=skip");
echo "Import complete.\n";
