<?php

function blc_get_user_choices() {
	global $wpdb;

	$wp_user_search = $wpdb->get_results(
		"SELECT ID, display_name FROM $wpdb->users ORDER BY ID"
	);

	$result = [];

	foreach ($wp_user_search as $userid) {
		$user_id = (int) $userid->ID;
		$result[$user_id] = $userid->display_name;
	}

	return $result;
}
