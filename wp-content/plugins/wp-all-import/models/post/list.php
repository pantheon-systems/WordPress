<?php

class PMXI_Post_List extends PMXI_Model_List {
	protected $primary = array('post_id');
	
	public function __construct() {
		parent::__construct();
		$this->setTable(PMXI_Plugin::getInstance()->getTablePrefix() . 'posts');
	}
}