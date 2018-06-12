<?php
	
function pmxe_init()
{
	if(!empty($_GET['check_connection'])) {
	    exit(json_encode(array('success' => true)));
    }
}