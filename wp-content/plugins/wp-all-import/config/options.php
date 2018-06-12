<?php
/**
 * List of plugin optins, contains only default values, actual values are stored in database
 * and can be changed by corresponding wordpress function calls
 */
$config = array(		
	"history_file_count" => 10000,
	"history_file_age" => 365,
	"highlight_limit" => 10000,
	"upload_max_filesize" => 2048,
	"post_max_size" => 2048,
	"max_input_time" => -1,
	"max_execution_time" => -1,
	"dismiss" => 0,
	"dismiss_speed_up" => 0,
	"dismiss_manage_top" => 0,
	"dismiss_manage_bottom" => 0,
	"html_entities" => 0,
	"utf8_decode" => 0,
	"cron_job_key" => wp_all_import_url_title(wp_all_import_rand_char(12)),
	"chunk_size" => 32,
	"pingbacks" => 1,
	"legacy_special_character_handling" => 1,
	"case_sensitive" => 1,
	"session_mode" => 'default',
	"enable_ftp_import" => 0,
	"large_feed_limit" => 1000,
	//"enable_cron_processing_time_limit" => 0,
	"cron_processing_time_limit" => 120,
	"secure" => 1,
	"log_storage" => 5,
	"cron_sleep" => "",
	"port" => "",
	"force_stream_reader" => 0
);
