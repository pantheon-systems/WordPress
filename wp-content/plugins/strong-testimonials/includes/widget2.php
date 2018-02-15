<?php
/**
 * Strong Testimonials - View widget
 *
 * @since 1.21.0
 */

class Strong_Testimonials_View_Widget extends WP_Widget {

	function __construct() {

		$widget_ops = array(
			'classname'   => 'strong-testimonials-view-widget',
			'description' => _x( 'Add one of your testimonial views.', 'widget description', 'strong-testimonials' )
		);
		$control_ops = array(
			'id_base' => 'strong-testimonials-view-widget',
		);
		parent::__construct(
			'strong-testimonials-view-widget',
			_x( 'Strong Testimonials View', 'widget label', 'strong-testimonials' ),
			$widget_ops,
			$control_ops
		);

		$this->defaults = array(
			'title'   => _x( 'Testimonials', 'widget title', 'strong-testimonials' ),
			'text'    => '',
			'filter'  => 0,
			'view'    => '0',
		);

	}

	function widget( $args, $instance ) {
		$data  = array_merge( $args, $instance );
		$title = apply_filters( 'widget_title', empty( $data['title'] ) ? '' : $data['title'], $instance, $this->id_base );

		$widget_text = ! empty( $data['text'] ) ? $data['text'] : '';
		$text = apply_filters( 'widget_text', $widget_text, $data, $this );

		echo $data['before_widget'];

		if ( ! empty( $title ) )
			echo $data['before_title'] . $title . $data['after_title'];

		if ( ! empty( $text ) ) {
			?>
			<div class="textwidget"><?php echo !empty( $data['filter'] ) ? wpautop( $text ) : $text; ?></div>
			<?php
		}

		/**
		 * Catch undefined view to avoid error in the Customizer.
		 */
		if ( isset( $data['view'] ) && $data['view'] ) {
			/**
			 * Intermediate filter because `the_content` filters are not run on widget text.
			 * @since 2.11.9
			 */
			ob_start();
			strong_testimonials_view( $instance['view'] );
			echo apply_filters( 'wpmtst_widget_text', ob_get_clean() );
		}

		echo $data['after_widget'];
	}

	function form( $instance ) {
		$instance = wp_parse_args( (array) $instance, $this->defaults );
		$filter = isset( $instance['filter'] ) ? $instance['filter'] : 0;
		$title = sanitize_text_field( $instance['title'] );
		$views = wpmtst_get_views();
		?>
		<div class="wpmtst-widget-form">
			<p>
				<label for="<?php echo $this->get_field_id( 'title' ); ?>">
					<?php _e( 'Title:' ); ?>
				</label>
				<input class="widefat" type="text" id="<?php echo $this->get_field_id( 'title' ); ?>"
				       name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo esc_attr( $title ); ?>">
			</p>
			<p>
				<label for="<?php echo $this->get_field_id( 'text' ); ?>"><?php _e( 'Content:' ); ?></label>
				<textarea class="widefat" rows="8" id="<?php echo $this->get_field_id('text'); ?>" name="<?php echo $this->get_field_name('text'); ?>"><?php echo esc_textarea( $instance['text'] ); ?></textarea>
			</p>
			<p>
				<input id="<?php echo $this->get_field_id('filter'); ?>" name="<?php echo $this->get_field_name('filter'); ?>" type="checkbox"<?php checked( $filter ); ?> />&nbsp;<label for="<?php echo $this->get_field_id('filter'); ?>"><?php _e( 'Automatically add paragraphs to above Content only', 'strong-testimonials' ); ?></label>
			</p>
			<p>
				<label for="<?php echo $this->get_field_id( 'view' ); ?>">
					<?php _ex( 'View:', 'widget setting', 'strong-testimonials' ); ?>
				</label>
				<select class="widefat" id="<?php echo $this->get_field_id( 'view' ); ?>"
				        name="<?php echo $this->get_field_name( 'view' ); ?>" autocomplete="off">
					<option value=""><?php _e( '&mdash; Select &mdash;' ); ?></option>
					<?php
					foreach ( $views as $view ) {
						printf( '<option value="%s" %s>%s</option>', $view['id'], selected( $view['id'], $instance['view'] ), $view['name'] );
					}
					?>
				</select>
			</p>
		</div>
		<?php
	}

	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title'] = sanitize_text_field( $new_instance['title'] );
		if ( current_user_can( 'unfiltered_html' ) ) {
			$instance['text'] = $new_instance['text'];
		}
		else {
			$instance['text'] = wp_kses_post( stripslashes( $new_instance['text'] ) );
		}
		$instance['filter'] = !empty( $new_instance['filter'] );
		$instance['view']   = sanitize_text_field( $new_instance['view'] );
		return array_merge( $this->defaults, $instance );
	}

}


/**
 * Load widget
 */
function wpmtst_load_view_widget() {
	register_widget( 'Strong_Testimonials_View_Widget' );
}
add_action( 'widgets_init', 'wpmtst_load_view_widget' );


/**
 * Compatibility: CM Tooltip Glossary
 *
 * Instead of being registered, the [glossary_exclude] is handled in a late content filter
 * which leaves the shortcode behind since we don't run that filter on widget content.
 *
 * @since 2.11.9
 *
 * @param $content
 *
 * @return mixed
 */
function wpmtst_remove_glossary_exclude( $content ) {
	if ( class_exists( 'CMTooltipGlossaryFrontend' ) ) {
		$content = str_replace( array( '[glossary_exclude]', '[/glossary_exclude]' ), array( '', '' ), $content );
	}

	return $content;
}
add_filter( 'wpmtst_widget_text', 'wpmtst_remove_glossary_exclude' );
