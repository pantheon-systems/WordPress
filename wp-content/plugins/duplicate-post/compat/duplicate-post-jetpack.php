<?php
add_action( 'admin_init', 'duplicate_post_jetpack_init' );


function duplicate_post_jetpack_init() {
	add_filter('duplicate_post_blacklist_filter', 'duplicate_post_jetpack_add_to_blacklist', 10, 1 );
	
	if (class_exists('WPCom_Markdown')){
		add_action('duplicate_post_pre_copy', 'duplicate_post_jetpack_disable_markdown', 10);
		add_action('duplicate_post_post_copy', 'duplicate_post_jetpack_enable_markdown', 10);
	}	
}

function duplicate_post_jetpack_add_to_blacklist($meta_blacklist) {
	$meta_blacklist[] = '_wpas*'; //Jetpack Publicize
	$meta_blacklist[] = '_publicize*'; //Jetpack Publicize
	
	$meta_blacklist[] = '_jetpack*'; //Jetpack Subscriptions etc.
	
	return $meta_blacklist;
}

// Markdown
function duplicate_post_jetpack_disable_markdown(){
	WPCom_Markdown::get_instance()->unload_markdown_for_posts();
}

function duplicate_post_jetpack_enable_markdown(){
	WPCom_Markdown::get_instance()->load_markdown_for_posts();
}