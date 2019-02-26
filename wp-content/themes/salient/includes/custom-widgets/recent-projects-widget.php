<?php

if ( !defined( 'ABSPATH') ) {
	exit('Direct script access denied.');
}


function pn_get_attachment_id_from_url( $attachment_url = '' ) {
 
	global $wpdb;
	$attachment_id = false;
 
	// If there is no url, return.
	if ( '' == $attachment_url )
		return;
 
	// Get the upload directory paths
	$upload_dir_paths = wp_upload_dir();
 
	// Make sure the upload path base directory exists in the attachment URL, to verify that we're working with a media library image
	if ( false !== strpos( $attachment_url, $upload_dir_paths['baseurl'] ) ) {
 
		// If this is the URL of an auto-generated thumbnail, get the URL of the original image
		$attachment_url = preg_replace( '/-\d+x\d+(?=\.(jpg|jpeg|png|gif)$)/i', '', $attachment_url );
 
		// Remove the upload path base directory from the attachment URL
		$attachment_url = str_replace( $upload_dir_paths['baseurl'] . '/', '', $attachment_url );
 
		// Finally, run a custom database query to get the attachment ID from the modified attachment URL
		$attachment_id = $wpdb->get_var( $wpdb->prepare( "SELECT wposts.ID FROM $wpdb->posts wposts, $wpdb->postmeta wpostmeta WHERE wposts.ID = wpostmeta.post_id AND wpostmeta.meta_key = '_wp_attached_file' AND wpostmeta.meta_value = '%s' AND wposts.post_type = 'attachment'", $attachment_url ) );
 
	}
 
	return $attachment_id;
}


function Recent_Projects_init() {
	register_widget('Recent_Projects_Widget');
}

add_action('widgets_init', 'Recent_Projects_init');


class Recent_Projects_Widget extends WP_Widget {

	function __construct() {
		$widget_ops = array('classname' => 'recent_projects_widget', 'description' => __( "The most recent projects on your site.",'salient'));
		parent::__construct('recent-projects', esc_html__('Recent Projects','salient'), $widget_ops);
		$this->alt_option_name = 'recent_projects_widget';

		add_action( 'save_post', array(&$this, 'flush_widget_cache') );
		add_action( 'deleted_post', array(&$this, 'flush_widget_cache') );
		add_action( 'switch_theme', array(&$this, 'flush_widget_cache') );
	}

	function widget($args, $instance) {

		
		$cache = wp_cache_get('recent_projects_widget', 'widget');

		if ( !is_array($cache) )
			$cache = array();

		if ( isset($cache[$args['widget_id']]) ) {
			echo $cache[$args['widget_id']]; // WPCS: XSS ok.
			return;
		}

		ob_start();
		extract($args);

		$title = apply_filters('widget_title', empty($instance['title']) ? esc_html__('Recent Projects','salient') : $instance['title']);
		if ( !$number = (int) $instance['number'] )
			$number = 6;
		else if ( $number < 1 )
			$number = 1;
		else if ( $number > 9 )
			$number = 9;

		$r = new WP_Query(array('post_type' => 'portfolio', 'posts_per_page' => $number));
		if ($r->have_posts()) :
?>
		<?php echo $before_widget; // WPCS: XSS ok. ?>
		<?php if ( $title ) echo $before_title . $title . $after_title; // WPCS: XSS ok. ?>
		<div>
			<?php  while ($r->have_posts()) : $r->the_post(); 
			
			global $post;
			
			$custom_project_link = get_post_meta($post->ID, '_nectar_external_project_url', true);
			$the_project_link = (!empty($custom_project_link)) ? $custom_project_link : esc_url(get_permalink());  
			$custom_content_project = get_post_meta($post->ID, '_nectar_portfolio_custom_grid_item', true); ?>	
				
			<a href="<?php echo esc_url( $the_project_link ); ?>" data-custom-grid-item="<?php echo esc_attr( $custom_content_project ); ?>" title="<?php echo esc_attr(get_the_title() ? get_the_title() : get_the_ID()); ?>">
				<?php 

				//custom thumbnail
				$custom_thumbnail = get_post_meta($post->ID, '_nectar_portfolio_custom_thumbnail', true); 
				
				if ( has_post_thumbnail() ) {
					the_post_thumbnail('portfolio-widget'); 
				} 
				else if(!empty($custom_thumbnail) ){
					$attachment_id = pn_get_attachment_id_from_url($custom_thumbnail);
					echo wp_get_attachment_image($attachment_id,'portfolio-widget');
				}
					else {
					echo '<img src="'.get_template_directory_uri().'/img/no-portfolio-item-tiny.jpg" alt="no image added yet." />';
				} ?>
				
			</a> 
			<?php endwhile; ?>
		</div>
		<?php echo $after_widget; // WPCS: XSS ok. ?>
<?php
			wp_reset_query();  // Restore global post data stomped by the_post().
		endif;

		$cache[$args['widget_id']] = ob_get_flush();
		wp_cache_add('recent_projects_widget', $cache, 'widget');
	}

	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['number'] = (int) $new_instance['number'];
		$this->flush_widget_cache();

		$alloptions = wp_cache_get( 'alloptions', 'options' );
		if ( isset($alloptions['recent_projects_widget']) )
			delete_option('recent_projects_widget');

		return $instance;
	}

	function flush_widget_cache() {
		wp_cache_delete('recent_projects_widget', 'widget');
	}

	function form( $instance ) {
		$title = isset($instance['title']) ? esc_attr($instance['title']) : '';
		if ( !isset($instance['number']) || !$number = (int) $instance['number'] )
			$number = 6;
		else if ( $number < 1 )
			$number = 1;
		else if ( $number > 9 )
			$number = 9;
?>
		<p><label for="<?php echo wp_kses_post( $this->get_field_id('title') ); ?>"><?php _e('Title:', 'salient'); ?></label>
		<input class="widefat" id="<?php echo wp_kses_post( $this->get_field_id('title') ); ?>" name="<?php echo wp_kses_post( $this->get_field_name('title') ); ?>" type="text" value="<?php echo wp_kses_post( $title ); ?>" /></p>

		<p><label for="<?php echo esc_attr( $this->get_field_id('number') ); ?>"><?php _e('Number of projects to show:', 'salient'); ?></label>
		<input id="<?php echo esc_attr( $this->get_field_id('number') ); ?>" name="<?php echo esc_attr( $this->get_field_name('number') ); ?>" type="text" value="<?php echo esc_attr( $number ); ?>" size="2" /><br />
		<small><?php _e('Change in increments of 3 (at most 9)', 'salient'); ?></small></p>
<?php
	}
}


?>