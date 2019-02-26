<?php

if ( !defined( 'ABSPATH') ) {
	exit('Direct script access denied.');
}



class Nectar_Popular_Posts extends WP_Widget {


	function __construct() {

		$widget_ops = array(
			'classname'   => 'nectar_popular_posts_widget',
			'description' => esc_html__('Display your most popular posts.', 'salient' ),
		);

		parent::__construct( 'nectar_popular_posts', esc_html__('Nectar Popular Posts', 'salient'), $widget_ops );

	}



	function widget($args, $instance) {

		extract( $args );

		$title       = apply_filters( 'widget_title', isset( $instance['title'] ) ? $instance['title'] : '' );
		$number_of_posts = intval($instance['number_of_posts']);
		$timeline = $instance['timeline'];
		$post_style = $instance['style'];
		if(!empty($post_style)) $post_style = strtolower(preg_replace('/[\s-]+/', '-',$post_style));
		$orderby = $instance['orderby'];

		if($timeline == 'All Time') {
			$date_query_ar = array();
		} else if($timeline == 'Posts Published Within 7 Days') {
			$date_query_ar = array(
	            'after' => '1 week ago'
	        );
		} else if($timeline == 'Posts Published Within 30 Days') {
			$date_query_ar = array(
	            'after' => '1 month ago'
	        );
		} else if($timeline == 'Posts Published Within 1 Year') {
			$date_query_ar = array(
	            'after' => '1 year ago'
	        );
		}

		
		echo $before_widget; // WPCS: XSS ok.

		if ( $title ) {
			echo $before_title . $title . $after_title; // WPCS: XSS ok.
		}
		
		if($orderby == 'Highest Views') {
			//post views
			$query_args = array(
			    'post_type' => 'post',
			    'date_query' => array(
			        $date_query_ar
			    ),
			    'meta_key' => 'nectar_blog_post_view_count',
			    'orderby' => 'meta_value_num',
			    'ignore_sticky_posts' => 1,
			    'posts_per_page' => $number_of_posts,
			);
		} else {
			//comment count
			$query_args = array(
			    'post_type' => 'post',
			    'date_query' => array(
			        $date_query_ar
			    ),
			    'orderby' => 'comment_count',
			    'ignore_sticky_posts' => 1,
			    'posts_per_page' => $number_of_posts,
			);
		}
		

		$popular_post_query = new WP_Query( $query_args );

		echo '<ul class="nectar_blog_posts_popular nectar_widget" data-style="'. esc_attr( $post_style ) .'">';
		if ( $popular_post_query->have_posts() ) :
		    while ( $popular_post_query->have_posts() ) : $popular_post_query->the_post();

				global $post;
				$post_featured_img_class = (has_post_thumbnail() && $post_style != 'minimal-counter') ? 'class="has-img"' : '';
				
				$post_featured_img = null;
				if(has_post_thumbnail()) {
					if($post_style == 'hover-featured-image' || $post_style == 'hover-featured-image-gradient-and-counter') {

						$post_featured_img = '<div class="popular-featured-img" style="background-image: url(' . get_the_post_thumbnail_url($post->ID, 'small', array('title' => '')) . ');"></div>';
					
					} else if($post_style == 'featured-image-left') {

						$post_featured_img = '<div class="popular-featured-img">'. get_the_post_thumbnail($post->ID, 'portfolio-widget', array('title' => '')) . '</div>';

					}
				} else if($post_style == 'hover-featured-image-gradient-and-counter') {
					$post_featured_img = '<span class="popular-featured-img"></span>';
				}
				
				
				$post_border_circle = ($post_style == 'minimal-counter') ? '<div class="arrow-circle"> <svg width="38" height="38"> <circle class="path" fill="none" stroke-width="6" stroke-linecap="round" cx="19" cy="19" r="18"></circle> </svg>  </div>' : null;
				echo '<li '.$post_featured_img_class.'><a href="'. esc_url(get_permalink()) .'"> '.$post_featured_img. $post_border_circle. '<span class="meta-wrap"><span class="post-title">' . get_the_title() . '</span> <span class="post-date">' . get_the_date() . '</span></span></a></li>'; // WPCS: XSS ok.

		    endwhile;
		endif;
		echo '</ul>';

		wp_reset_postdata();

		

		echo $after_widget; // WPCS: XSS ok.

	}




	function update( $new_instance, $old_instance ) {

		$instance = $old_instance;

		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['number_of_posts'] = (int) $new_instance['number_of_posts'];
		$instance['timeline'] = strip_tags( $new_instance['timeline'] );
		$instance['orderby'] = strip_tags( $new_instance['orderby'] );
		$instance['style'] = strip_tags( $new_instance['style'] );

		return $instance;

	}





	function form( $instance ) {

		$defaults = array(
			'number_of_posts' => 5,
			'timeline' => 'All Time',
			'title' => '',
			'style' => 'Hover Featured Image',
			'orderby' => esc_attr__( 'Highest Views', 'salient' ),
		);

		$instance = wp_parse_args( (array) $instance, $defaults ); ?>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_attr_e( 'Title:', 'salient' ); ?></label>
			<input type="text" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" class="widefat" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" value="<?php echo esc_attr( $instance['title'] ); ?>" />
		</p>

		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'number_of_posts' ) ); ?>"><?php esc_attr_e( 'Number Of Posts:', 'salient' ); ?></label>
			<input type="text" style="width: 33px;" id="<?php echo esc_attr( $this->get_field_id( 'number_of_posts' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'number_of_posts' ) ); ?>" value="<?php echo esc_attr( $instance['number_of_posts'] ); ?>" />
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'style' ) ); ?>"><?php esc_attr_e( 'Style:', 'salient' ); ?></label>
			<select id="<?php echo esc_attr( $this->get_field_id( 'style' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'style' ) ); ?>" class="widefat" style="width:100%;">	
				<option <?php if ( esc_attr__( 'Hover Featured Image', 'salient' ) == $instance['style'] ) { echo 'selected="selected"'; } ?>><?php esc_attr_e( 'Hover Featured Image', 'salient' ); ?></option>
				<option <?php if ( esc_attr__( 'Hover Featured Image Gradient And Counter', 'salient' ) == $instance['style'] ) { echo 'selected="selected"'; } ?>><?php esc_attr_e( 'Hover Featured Image Gradient And Counter', 'salient' ); ?></option>
				<option <?php if ( esc_attr__( 'Minimal Counter', 'salient' ) == $instance['style'] ) { echo 'selected="selected"'; } ?>><?php esc_attr_e( 'Minimal Counter', 'salient' ); ?></option>
				<option <?php if ( esc_attr__( 'Featured Image Left', 'salient' ) == $instance['style'] ) { echo 'selected="selected"'; } ?>><?php esc_attr_e( 'Featured Image Left', 'salient' ); ?></option>
			</select>
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'timeline' ) ); ?>"><?php esc_attr_e( 'Timeline:', 'salient' ); ?></label>
			<select id="<?php echo esc_attr( $this->get_field_id( 'timeline' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'timeline' ) ); ?>" class="widefat" style="width:100%;">	
				<option <?php if ( esc_attr__( 'All Time', 'salient' ) == $instance['timeline'] ) { echo 'selected="selected"'; } ?>><?php esc_attr_e( 'All Time', 'salient' ); ?></option>
				<option <?php if ( esc_attr__( 'Posts Published Within 7 Days', 'salient' ) == $instance['timeline'] ) { echo 'selected="selected"'; } ?>><?php esc_attr_e( 'Posts Published Within 7 Days', 'salient' ); ?></option>
				<option <?php if ( esc_attr__( 'Posts Published Within 30 Days', 'salient' ) == $instance['timeline'] ) { echo 'selected="selected"'; } ?>><?php esc_attr_e( 'Posts Published Within 30 Days', 'salient' ); ?></option>
				<option <?php if ( esc_attr__( 'Posts Published Within 1 Year', 'salient' ) == $instance['timeline'] ) { echo 'selected="selected"'; } ?>><?php esc_attr_e( 'Posts Published Within 1 Year', 'salient' ); ?></option>
			</select>
		</p>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'orderby' ) ); ?>"><?php esc_attr_e( 'Popular Posts Order By:', 'salient' ); ?></label>
			<select id="<?php echo esc_attr( $this->get_field_id( 'orderby' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'orderby' ) ); ?>" class="widefat" style="width:100%;">	
				<option <?php if ( esc_attr__( 'Highest Views', 'salient' ) == $instance['orderby'] ) { echo 'selected="selected"'; } ?>><?php esc_attr_e( 'Highest Views', 'salient' ); ?></option>
				<option <?php if ( esc_attr__( 'Highest Comments', 'salient' ) == $instance['orderby'] ) { echo 'selected="selected"'; } ?>><?php esc_attr_e( 'Highest Comments', 'salient' ); ?></option>
			</select>
		</p>
		

	<?php
	}
}

