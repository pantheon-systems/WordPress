<?php

interface IWPML_TM_Count {

	public function get_total_words();

	public function get_words_to_translate( $lang );

}
