<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // disable direct access
}

/**
 * Adds MetaSlider widget.
 */
class MetaSlider_Widget extends WP_Widget {

	/**
	 * Register widget with WordPress.
	 */
	public function __construct() {
		parent::__construct(
			'metaslider_widget', // Base ID
			'MetaSlider', // Name
			array( 'description' => __( 'MetaSlider', 'ml-slider' ) ) // Args
		);
	}

	/**
	 * Front-end display of widget.
	 *
	 * @see WP_Widget::widget()
	 *
	 * @param array $args     Widget arguments.
	 * @param array $instance Saved values from database.
	 */
	public function widget( $args, $instance ) {
		extract( $args );

		if ( isset( $instance['slider_id'] ) ) {
			$slider_id = $instance['slider_id'];

			$title = apply_filters( 'widget_title', $instance['title'] );

			echo $before_widget;
			if ( ! empty( $title ) )
				echo $before_title . $title . $after_title;

			echo do_shortcode( "[metaslider id={$slider_id}]" );
			echo $after_widget;
		}
	}

	/**
	 * Sanitize widget form values as they are saved.
	 *
	 * @see WP_Widget::update()
	 *
	 * @param array $new_instance Values just sent to be saved.
	 * @param array $old_instance Previously saved values from database.
	 *
	 * @return array Updated safe values to be saved.
	 */
	public function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['slider_id'] = strip_tags( $new_instance['slider_id'] );
		$instance['title'] = strip_tags( $new_instance['title'] );

		return $instance;
	}

	/**
	 * Back-end widget form.
	 *
	 * @see WP_Widget::form()
	 *
	 * @param array $instance Previously saved values from database.
	 */
	public function form( $instance ) {
		$selected_slider = 0;
		$title = "";
		$sliders = false;

		if ( isset( $instance['slider_id'] ) ) {
			$selected_slider = $instance['slider_id'];
		}

		if ( isset( $instance['title'] ) ) {
			$title = $instance['title'];
		}


		$posts = get_posts( array(
				'post_type' => 'ml-slider',
				'post_status' => 'publish',
				'orderby' => 'date',
				'order' => 'ASC',
				'posts_per_page' => -1
			) );

		foreach ( $posts as $post ) {
			$active = $selected_slider == $post->ID ? true : false;

			$sliders[] = array(
				'active' => $active,
				'title' => $post->post_title,
				'id' => $post->ID
			);
		}

?>
		<p>
			<?php if ( $sliders ) { ?>
				<p>
					<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:', 'ml-slider' ); ?></label>
					<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
				</p>
				<label for="<?php echo $this->get_field_id( 'slider_id' ); ?>"><?php _e( 'Select Slider:', 'ml-slider' ); ?></label>
				<select id="<?php echo $this->get_field_id( 'slider_id' ); ?>" name="<?php echo $this->get_field_name( 'slider_id' ); ?>">
					<?php
			foreach ( $sliders as $slider ) {
				$selected = $slider['active'] ? 'selected=selected' : '';
				echo "<option value='{$slider['id']}' {$selected}>{$slider['title']}</option>";
			}
?>
				</select>
			<?php } else {
			_e( 'No slideshows found', 'ml-slider' );
		} ?>
		</p>
		<?php
	}
}