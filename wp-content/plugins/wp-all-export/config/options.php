<?php
/**
 * List of plugin options, contains only default values, actual values are stored in database
 * and can be changed by corresponding WordPress function calls
 */
$config = array(
    "info_api_url" => "http://www.wpallimport.com",
    "dismiss" => 0,
	"dismiss_manage_top" => 0,
	"dismiss_manage_bottom" => 0,
	"cron_job_key"	=> wp_all_export_url_title(wp_all_export_rand_char(12)),
	"max_input_time" => -1,
	"max_execution_time" => 0,
	"secure" => 1,
	"license" => "",
	"license_status" => "",
	"scheduling_license" => "",
	"scheduling_license_status" => "",
	"zapier_api_key" => wp_all_export_rand_char(32),
	"zapier_invitation_url" => "",
	"zapier_invitation_url_received" => ""
);