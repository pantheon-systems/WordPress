<?php

class PMXE_Post_List extends PMXE_Model_List {
	protected $primary = array('post_id');
	
	public function __construct() {
		parent::__construct();
		$this->setTable(PMXE_Plugin::getInstance()->getTablePrefix() . 'posts');
	}
}