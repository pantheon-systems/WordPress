<?php
if( ! defined("SS_VERSION") ) {
	header( 'Status: 403 Forbidden' );
	header( 'HTTP/1.1 403 Forbidden' );
	exit;
}

function ss_get_options(){	
	static $options;

	if( ! $options ) {
		$defaults = array(
			'twitter_username' => "",
			'auto_add_post_types' => array( 'post' ),
			'social_options'=>array('facebook','twitter','googleplus','linkedin','pinterest','xing'),
			'load_static'=>array('load_css','load_js'),
			'facebook_text'=>"Share on Facebook",
			'twitter_text'=>"Share on Twitter",
			'googleplus_text'=>"Share on Google+",
			'linkedin_text'=>"Share on Linkedin",
			'pinterest_text'=>"Share on Pinterest",
			'pinterest_image'=>"",
			'xing_text'=>"Share on Xing",
			'before_button_text'=>'',
			'text_position' => 'left',
			'show_icons'		=>	'0'
		);

		$db_option = get_option( 'wp_social_sharing', array());
		if(!isset($db_option['load_static'])){
			$db_option['load_static']=array();
		}
		if(!isset($db_option['social_options'])){
			$db_option['social_options']=array();
		}
		if(!isset($db_option['auto_add_post_types'])){
			$db_option['auto_add_post_types']=array();
		}
	
		if( ! $db_option ) {
			update_option( 'wp_social_sharing', $defaults );
		}
		
		$options = wp_parse_args( $db_option, $defaults );
	}
	return $options;
}

add_action('admin_footer','include_icon_order_script');
function include_icon_order_script(){
	wp_enqueue_script( 'jquery-ui-sortable' );
?>
	<script type="text/javascript">
		jQuery(document).ready(function($){
			$('.dndicon').sortable({
				stop:function(event,ui){
					var new_order='';
					$('.dndicon > div').each(function(e,i){
						new_order += $(i).attr('id')+',';
					});
					new_order = new_order.slice(0,new_order.length-1);
					var ajax_data={'action':'wss_update_icon_order','new_order':new_order};
					$.post(ajaxurl,ajax_data,function(response){});
				}	
			});
		});
	</script>
<?php 	
}

add_action('wp_ajax_wss_update_icon_order','include_icon_order_action');
function include_icon_order_action(){
	update_option('wss_wp_social_sharing', rtrim($_POST['new_order'],','));
	die;
}