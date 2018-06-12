<?php

$f = fopen('/var/www/wpae/google_taxonomies.txt','r');

$response = [];
$max = 0;

function parseTaxonomies($element) {

    return parseTaxonomies($element[0]);
}


while($line = fgets($f)) {
    $lineParts = explode('-', $line);
    $id = $lineParts[0];
    $lineParts = explode('>', $lineParts[1]);
    
    if(count($lineParts) == 1) {
        $response[trim($lineParts[0])] = ['id' => $id];
    }
}

rewind($f);

while($line = fgets($f)) {
    $lineParts = explode('-', $line);
    $id = $lineParts[0];
    $lineParts = explode('>', $lineParts[1]);

    if(count($lineParts) == 2) {
        $response[trim($lineParts[0])]['children'][trim($lineParts[1])] = ['id' => $id];
    }
}

rewind($f);

while($line = fgets($f)) {
    $lineParts = explode('-', $line);
    $id = $lineParts[0];
    $lineParts = explode('>', $lineParts[1]);

    if(count($lineParts) == 3) {
        $response[trim($lineParts[0])]
        ['children']
        [trim($lineParts[1])]
        ['children']
        [trim($lineParts[2])]
            = ['id' => $id];
    }
}

rewind($f);

while($line = fgets($f)) {
    $lineParts = explode('-', $line);
    $id = $lineParts[0];
    $lineParts = explode('>', $lineParts[1]);

    if(count($lineParts) == 4) {
        $response[trim($lineParts[0])]
        ['children']
        [trim($lineParts[1])]
        ['children']
        [trim($lineParts[2])]
        ['children']
        [trim($lineParts[3])]
            = ['id' => $id];
    }
}

rewind($f);

while($line = fgets($f)) {
    $lineParts = explode('-', $line);
    $id = $lineParts[0];
    $lineParts = explode('>', $lineParts[1]);

    if(count($lineParts) == 5) {
        $response[trim($lineParts[0])]
        ['children']
        [trim($lineParts[1])]
        ['children']
        [trim($lineParts[2])]
        ['children']
        [trim($lineParts[3])]
        ['children']
        [trim($lineParts[4])]
            = ['id' => $id];
    }
}


rewind($f);

while($line = fgets($f)) {
    $lineParts = explode('-', $line);
    $id = $lineParts[0];
    $lineParts = explode('>', $lineParts[1]);

    if(count($lineParts) == 6) {
        $response[trim($lineParts[0])]
        ['children']
        [trim($lineParts[1])]
        ['children']
        [trim($lineParts[2])]
        ['children']
        [trim($lineParts[3])]
        ['children']
        [trim($lineParts[4])]
        ['children']
        [trim($lineParts[5])]
            = ['id' => $id];
    }
}

while($line = fgets($f)) {
    $lineParts = explode('-', $line);
    $id = $lineParts[0];
    $lineParts = explode('>', $lineParts[1]);

    if(count($lineParts) == 7) {
        $response[trim($lineParts[0])]
        ['children']
        [trim($lineParts[1])]
        ['children']
        [trim($lineParts[2])]
        ['children']
        [trim($lineParts[3])]
        ['children']
        [trim($lineParts[4])]
        ['children']
        [trim($lineParts[5])]
        ['children']
        [trim($lineParts[6])]
            = ['id' => $id];
    }
}

while($line = fgets($f)) {
    $lineParts = explode('-', $line);
    $id = $lineParts[0];
    $lineParts = explode('>', $lineParts[1]);

    if(count($lineParts) == 8) {
        die('Errors');
    }
}


echo json_encode($response);
fclose($f);