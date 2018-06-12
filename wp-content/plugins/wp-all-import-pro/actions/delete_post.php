<?php 

function pmxi_delete_post($post_id) {
	if (is_numeric($post_id)){
		$post = new PMXI_Post_Record();
		$post->getBy('post_id', $post_id)->isEmpty() or $post->delete();
	}
}