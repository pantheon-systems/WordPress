<?php

interface IWPML_TM_Word_Calculator_Post {

	public function count_words( WPML_Post_Element $post_element, $lang = null );

}
