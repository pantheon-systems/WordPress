<?php

class PMXI_File_List extends PMXI_Model_List {
	public function __construct() {
		parent::__construct();
		$this->setTable(PMXI_Plugin::getInstance()->getTablePrefix() . 'files');
	}
	
	/**
	 * Sweep history files in accordance with plugin settings
	 * @return PMXI_File_List
	 * @chainable
	 */
	public function sweepHistory() {
		$age = PMXI_Plugin::getInstance()->getOption('history_file_age');
		if ($age > 0) {
			$date = new DateTime(); $date->modify('-' . $age . ' day');
			foreach ($this->getBy('registered_on <', $date->format('Y-m-d'))->convertRecords() as $f) {
				$f->delete();
			}
		}
		$count = PMXI_Plugin::getInstance()->getOption('history_file_count');
		if ($count > 0) {
			$count_actual = $this->countBy();
			if ($count_actual > $count) foreach ($this->getBy(NULL, 'registered_on', 1, $count_actual - $count)->convertRecords() as $f) {
				$f->delete();
			}
		}
		
		return $this;
	}
}