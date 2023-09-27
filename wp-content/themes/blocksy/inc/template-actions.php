<?php

add_action(
	'blocksy:template:before',
	function () {
		if (function_exists('blc_output_read_progress_bar')) {
			/**
			 * Note to code reviewers: This line doesn't need to be escaped.
			 * Function blc_output_read_progress_bar() used here escapes the value properly.
			 */
			echo blc_output_read_progress_bar();
		}
	}
);

