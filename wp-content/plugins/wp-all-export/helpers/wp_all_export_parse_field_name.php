<?php

function wp_all_export_parse_field_name($name){

    if (strpos($name, "[") === 0 && strpos($name, "]") === strlen($name) - 1){
        $snippet = str_replace(array("[", "]"), "", $name);
        $name = eval("return " . $snippet . ";");
    }

    return $name;
}