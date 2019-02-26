<?php

interface IWPML_Upgrade_Command {

	public function run_admin();

	public function run_ajax();

	public function run_frontend();

	public function get_results();
}