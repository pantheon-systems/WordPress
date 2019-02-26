<?php

interface IWPML_TM_Word_Count_Queue_Items {

	/**
	 * @return array|null a tuple containing the element id and type or null if queue is empty
	 */
	public function get_next();

	/**
	 * @param int    $id
	 * @param string $type
	 */
	public function remove( $id, $type );

	/** @return bool */
	public function is_completed();

	public function save();

}
