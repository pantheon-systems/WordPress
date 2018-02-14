<?php

header("Access-Control-Allow-Origin: *");

$taxonomies = file_get_contents(__DIR__.'/frontend/taxonomies/taxonomies_multilevel.json');

$taxonomies = json_decode($taxonomies, true);


$link = mysqli_connect("127.0.0.1", "root", "", "pressmatic");

if (!$link) {
    echo "Error: Unable to connect to MySQL." . PHP_EOL;
    echo "Debugging errno: " . mysqli_connect_errno() . PHP_EOL;
    echo "Debugging error: " . mysqli_connect_error() . PHP_EOL;
    exit;
}



function parseTaxonomies($taxonomies, $level, $parentId, $parentName) {

    global $link;

    foreach($taxonomies as $name => $taxonomy) {

        mysqli_query($link, "INSERT INTO `wp_pmxe_google_cats` VALUES ('$taxonomy[id]','$name','$parentId','$parentName', '$level')");

        if(isset($taxonomy['children'])){
            parseTaxonomies($taxonomy['children'], $level+1, $taxonomy['id'], $name);
        }
    }
}

parseTaxonomies($taxonomies, 0, 0, '');