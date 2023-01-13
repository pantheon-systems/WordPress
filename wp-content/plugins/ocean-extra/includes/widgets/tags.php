<?php
/**
 * Tags widget.
 *
 * @package OceanWP WordPress theme
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Ocean_Extra_Tags_Widget' ) ) {
	class Ocean_Extra_Tags_Widget extends WP_Widget {

		/**
		 * Register widget with WordPress.
		 *
		 * @since 1.0.0
		 */
		public function __construct() {
			parent::__construct(
				'ocean_tags',
				esc_html__( '&raquo; Tags Cloud', 'ocean-extra' ),
				array(
					'classname'   => 'widget-oceanwp-tags tags-widget',
					'description' => esc_html__( 'A cloud of your most used tags.', 'ocean-extra' ),
					'customize_selective_refresh' => true,
				)
			);
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		}

		/**
		 * Enqueue scripts.
		 *
		 * @since 1.3.8
		 *
		 * @param string $hook_suffix
		 */
		public function enqueue_scripts( $hook_suffix ) {
			if ( 'widgets.php' !== $hook_suffix ) {
				return;
			}

			wp_enqueue_style( 'wp-color-picker' );
			wp_enqueue_script( 'wp-color-picker' );
			wp_enqueue_script( 'underscore' );
		}

		/**
		 * Front-end display of widget.
		 *
		 * @see WP_Widget::widget()
		 * @since 1.0.0
		 *
		 * @param array $args     Widget arguments.
		 * @param array $instance Saved values from database.
		 */
		public function widget( $args, $instance ) {

			$padding 			= isset( $instance['padding'] ) ? $instance['padding'] : '';
			$bg_color 			= isset( $instance['bg_color'] ) ? $instance['bg_color'] : '';
			$bg_hover_color 	= isset( $instance['bg_hover_color'] ) ? $instance['bg_hover_color'] : '';
			$link_color 		= isset( $instance['link_color'] ) ? $instance['link_color'] : '';
			$link_hover_color 	= isset( $instance['link_hover_color'] ) ? $instance['link_hover_color'] : '';
			$border_color 		= isset( $instance['border_color'] ) ? $instance['border_color'] : '';
			$border_hover_color = isset( $instance['border_hover_color'] ) ? $instance['border_hover_color'] : '';
			$font_size 			= isset( $instance['font_size'] ) ? $instance['font_size'] : '';
			$line_height 		= isset( $instance['line_height'] ) ? $instance['line_height'] : '';
			$letter_spacing 	= isset( $instance['letter_spacing'] ) ? $instance['letter_spacing'] : '';
			$text_transform 	= isset( $instance['text_transform'] ) ? $instance['text_transform'] : '';

			$current_taxonomy = $this->_get_current_taxonomy( $instance );

			if ( ! empty( $instance['title'] ) ) {
				$title = $instance['title'];
			} else {

				if ( 'post_tag' == $current_taxonomy ) {
					$title = esc_html__( 'Tags', 'oceanwp' );
				} else {
					$tax = get_taxonomy($current_taxonomy);
					$title = $tax->labels->name;
				}

			}

			/**
			 * Filters the taxonomy used in the Tag Cloud widget.
			 *
			 * @since 2.8.0
			 * @since 3.0.0 Added taxonomy drop-down.
			 *
			 * @see wp_tag_cloud()
			 *
			 * @param array $args Args used for the tag cloud widget.
			 */
			$tag_cloud = wp_tag_cloud( apply_filters( 'widget_tag_cloud_args', array(
				'taxonomy' 	=> $current_taxonomy,
				'echo' 		=> false
			) ) );

			if ( empty( $tag_cloud ) ) {
				return;
			}

			/** This filter is documented in wp-includes/widgets/class-wp-widget-pages.php */
			$title = apply_filters( 'widget_title', $title, $instance, $this->id_base );

			// Before widget WP hook
			echo $args['before_widget'];

				// Style
				if ( $padding && '8px 12px' != $padding
					|| $bg_color && '#f8f8f8' != $bg_color
					|| $bg_hover_color && '#f1f1f1' != $bg_hover_color
					|| $link_color && '#333' != $link_color
					|| $link_hover_color && '#333' != $link_hover_color
					|| $border_color && '#e9e9e9' != $border_color
					|| $link_hover_color && '#333' != $link_hover_color
					|| $font_size && '12' != $font_size
					|| $line_height && '12' != $line_height
					|| $letter_spacing && '0.4' != $letter_spacing
					|| $text_transform && 'default' != $text_transform ) {

					echo '<style type="text/css">';

					if ( $padding && '8px 12px' != $padding
						|| $bg_color && '#f8f8f8' != $bg_color
						|| $link_color && '#333' != $link_color
						|| $border_color && '#e9e9e9' != $border_color
						|| $font_size && '12' != $font_size
						|| $line_height && '12' != $line_height
						|| $letter_spacing && '0.4' != $letter_spacing
						|| $text_transform && 'default' != $text_transform ) {

						echo '.' . esc_attr( $this->id ) . '.tagcloud a{';
						if ( $padding && '8px 12px' != $padding ) {
							echo 'padding:' . esc_attr( $padding ) . ';';
						}
						if ( $bg_color && '#f8f8f8' != $bg_color ) {
							echo 'background-color:' . esc_attr( $bg_color ) . ';';
						}
						if ( $link_color && '#333' != $link_color ) {
							echo 'color:' . esc_attr( $link_color ) . ';';
						}
						if ( $border_color && '#e9e9e9' != $border_color ) {
							echo 'border-color:' . esc_attr( $border_color ) . ';';
						}
						if ( $font_size && '12' != $font_size ) {
							echo 'font-size:' . esc_attr( $font_size ) . 'px!important;';
						}
						if ( $line_height && '12' != $line_height ) {
							echo 'line-height:' . esc_attr( $line_height ) . 'px;';
						}
						if ( $letter_spacing && '0.4' != $letter_spacing ) {
							echo 'letter-spacing:' . esc_attr( $letter_spacing ) . 'px;';
						}
						if ( $text_transform && 'default' != $text_transform ) {
							echo 'text-transform:' . esc_attr( $text_transform ) . ';';
						}
						echo '}';

					}

					if ( $bg_hover_color && '#f1f1f1' != $bg_hover_color
						|| $link_hover_color && '#333' != $link_hover_color
						|| $border_hover_color && '#ddd' != $border_hover_color ) {
						echo '.' . esc_attr( $this->id ) . '.tagcloud a:hover{';
							if ( $bg_hover_color && '#f1f1f1' != $bg_hover_color ) {
								echo 'background-color:' . esc_attr( $bg_hover_color ) . ';';
							}
							if ( $link_hover_color && '#333' != $link_hover_color ) {
								echo 'color:' . esc_attr( $link_hover_color ) . ';';
							}
							if ( $border_hover_color && '#ddd' != $border_hover_color ) {
								echo 'border-color:' . esc_attr( $border_hover_color ) . ';';
							}
						echo '}';
					}

					echo '</style>';

				}

				if ( $title ) {
					echo $args['before_title'] . $title . $args['after_title'];
				}

				echo '<div class="tagcloud '. esc_attr( $this->id ) .'">';

					echo $tag_cloud;

				echo "</div>\n";

			// After widget WP hook
			echo $args['after_widget'];

		}

		/**
		 * Sanitize widget form values as they are saved.
		 *
		 * @see WP_Widget::update()
		 * @since 1.0.0
		 *
		 * @param array $new_instance Values just sent to be saved.
		 * @param array $old_instance Previously saved values from database.
		 *
		 * @return array Updated safe values to be saved.
		 */
		public function update( $new_instance, $old_instance ) {
			$instance 							= array();
			$instance['title'] 					= sanitize_text_field( $new_instance['title'] );
			$instance['taxonomy'] 				= stripslashes( $new_instance['taxonomy'] );
			$instance['padding']  				= strip_tags( $new_instance['padding'] );
			$instance['bg_color']    			= sanitize_hex_color( $new_instance['bg_color'] );
			$instance['bg_hover_color']  		= sanitize_hex_color( $new_instance['bg_hover_color'] );
			$instance['link_color']    			= sanitize_hex_color( $new_instance['link_color'] );
			$instance['link_hover_color']  		= sanitize_hex_color( $new_instance['link_hover_color'] );
			$instance['border_color']    		= sanitize_hex_color( $new_instance['border_color'] );
			$instance['border_hover_color']  	= sanitize_hex_color( $new_instance['border_hover_color'] );
			$instance['font_size']      		= strip_tags( $new_instance['font_size'] );
			$instance['line_height']      		= strip_tags( $new_instance['line_height'] );
			$instance['letter_spacing']      	= strip_tags( $new_instance['letter_spacing'] );
			$instance['text_transform']      	= strip_tags( $new_instance['text_transform'] );
			return $instance;
		}

		/**
		 * Back-end widget form.
		 *
		 * @see WP_Widget::form()
		 * @since 1.0.0
		 *
		 * @param array $instance Previously saved values from database.
		 */
		public function form( $instance ) {

			$instance = wp_parse_args( ( array ) $instance, array(
				'title'             	=> '',
				'padding'  				=> '',
				'bg_color'    			=> '#f8f8f8',
				'bg_hover_color' 		=> '#f1f1f1',
				'link_color'    		=> '#333',
				'link_hover_color' 		=> '#333',
				'border_color'    		=> '#e9e9e9',
				'border_hover_color' 	=> '#ddd',
				'font_size'				=> '12',
				'line_height'			=> '12',
				'letter_spacing'		=> '0.4',
				'text_transform'		=> 'default',
			) );

			$current_taxonomy 	= $this->_get_current_taxonomy($instance);
			$taxonomies 		= get_taxonomies( array( 'show_tagcloud' => true ), 'object' );
			$id 				= $this->get_field_id( 'taxonomy' );
			$name 				= $this->get_field_name( 'taxonomy' );
			$input 				= '<input type="hidden" id="' . $id . '" name="' . $name . '" value="%s" />'; ?>

			<p>
				<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_html_e( 'Title', 'ocean-extra' ); ?>:</label>
				<input class="widefat" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $instance['title'] ); ?>" />
			</p>

			<?php
			switch ( count( $taxonomies ) ) {

			// No tag cloud supporting taxonomies found, display error message
			case 0:
				echo '<p>' . esc_html__( 'The tag cloud will not be displayed since there are no taxonomies that support the tag cloud widget.', 'ocean-extra' ) . '</p>';
				printf( $input, '' );
				break;

			// Just a single tag cloud supporting taxonomy found, no need to display options
			case 1:
				$keys = array_keys( $taxonomies );
				$taxonomy = reset( $keys );
				printf( $input, esc_attr( $taxonomy ) );
				break;

			// More than one tag cloud supporting taxonomy found, display options
			default:
				printf(
					'<p><label for="%1$s">%2$s</label>' .
					'<select class="widefat" id="%1$s" name="%3$s">',
					$id,
					esc_html__( 'Taxonomy:', 'ocean-extra' ),
					$name
				);

				foreach ( $taxonomies as $taxonomy => $tax ) {
					printf(
						'<option value="%s"%s>%s</option>',
						esc_attr( $taxonomy ),
						selected( $taxonomy, $current_taxonomy, false ),
						$tax->labels->name
					);
				}

				echo '</select></p>';
			} ?>

			<p>
				<label for="<?php echo esc_attr( $this->get_field_id( 'padding' ) ); ?>"><?php esc_html_e( 'Padding:', 'ocean-extra' ); ?></label>
				<input class="widefat" type="text" id="<?php echo esc_attr( $this->get_field_id( 'padding' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'padding' ) ); ?>" value="<?php echo esc_attr( $instance['padding'] ); ?>" />
				<small style="color: #777;"><?php esc_html_e( 'top left bottom right, eg: 15px 8px 15px 25px', 'ocean-extra' ); ?></small>
			</p>

			<p>
				<label for="<?php echo esc_attr( $this->get_field_id( 'bg_color' ) ); ?>"><?php esc_html_e( 'Background Color:', 'ocean-extra' ); ?></label>
				<input class="color-picker" type="text" id="<?php echo esc_attr( $this->get_field_id( 'bg_color' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'bg_color' ) ); ?>" value="<?php echo esc_attr( $instance['bg_color'] ); ?>" />
			</p>

			<p>
				<label for="<?php echo esc_attr( $this->get_field_id( 'bg_hover_color' ) ); ?>"><?php esc_html_e( 'Background Hover Color:', 'ocean-extra' ); ?></label>
				<input class="color-picker" type="text" id="<?php echo esc_attr( $this->get_field_id( 'bg_hover_color' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'bg_hover_color' ) ); ?>" value="<?php echo esc_attr( $instance['bg_hover_color'] ); ?>" />
			</p>

			<p>
				<label for="<?php echo esc_attr( $this->get_field_id( 'link_color' ) ); ?>"><?php esc_html_e( 'Link Color:', 'ocean-extra' ); ?></label>
				<input class="color-picker" type="text" id="<?php echo esc_attr( $this->get_field_id( 'link_color' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'link_color' ) ); ?>" value="<?php echo esc_attr( $instance['link_color'] ); ?>" />
			</p>

			<p>
				<label for="<?php echo esc_attr( $this->get_field_id( 'link_hover_color' ) ); ?>"><?php esc_html_e( 'Link Hover Color:', 'ocean-extra' ); ?></label>
				<input class="color-picker" type="text" id="<?php echo esc_attr( $this->get_field_id( 'link_hover_color' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'link_hover_color' ) ); ?>" value="<?php echo esc_attr( $instance['link_hover_color'] ); ?>" />
			</p>

			<p>
				<label for="<?php echo esc_attr( $this->get_field_id( 'border_color' ) ); ?>"><?php esc_html_e( 'Border Color:', 'ocean-extra' ); ?></label>
				<input class="color-picker" type="text" id="<?php echo esc_attr( $this->get_field_id( 'border_color' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'border_color' ) ); ?>" value="<?php echo esc_attr( $instance['border_color'] ); ?>" />
			</p>

			<p>
				<label for="<?php echo esc_attr( $this->get_field_id( 'border_hover_color' ) ); ?>"><?php esc_html_e( 'Border Hover Color:', 'ocean-extra' ); ?></label>
				<input class="color-picker" type="text" id="<?php echo esc_attr( $this->get_field_id( 'border_hover_color' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'border_hover_color' ) ); ?>" value="<?php echo esc_attr( $instance['border_hover_color'] ); ?>" />
			</p>

			<p>
				<label for="<?php echo esc_attr( $this->get_field_id( 'font_size' ) ); ?>"><?php esc_html_e( 'Font Size (px):', 'ocean-extra' ); ?></label>
				<input class="widefat" type="number" min="5" max="50" step="1" id="<?php echo esc_attr( $this->get_field_id( 'font_size' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'font_size' ) ); ?>" value="<?php echo esc_attr( $instance['font_size'] ); ?>" />
			</p>

			<p>
				<label for="<?php echo esc_attr( $this->get_field_id( 'line_height' ) ); ?>"><?php esc_html_e( 'Line Height (px):', 'ocean-extra' ); ?></label>
				<input class="widefat" type="number" min="5" max="200" step="1" id="<?php echo esc_attr( $this->get_field_id( 'line_height' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'line_height' ) ); ?>" value="<?php echo esc_attr( $instance['line_height'] ); ?>" />
			</p>

			<p>
				<label for="<?php echo esc_attr( $this->get_field_id( 'letter_spacing' ) ); ?>"><?php esc_html_e( 'Letter Spacing (px):', 'ocean-extra' ); ?></label>
				<input class="widefat" type="number" min="0" max="5" step="0.1" id="<?php echo esc_attr( $this->get_field_id( 'letter_spacing' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'letter_spacing' ) ); ?>" value="<?php echo esc_attr( $instance['letter_spacing'] ); ?>" />
			</p>

			<p>
				<label for="<?php echo esc_attr( $this->get_field_id('text_transform') ); ?>"><?php esc_html_e( 'Text Transform:', 'ocean-extra' ); ?></label>
				<select class="widget-select widefat" name="<?php echo esc_attr( $this->get_field_name('text_transform') ); ?>" id="<?php echo esc_attr( $this->get_field_id('position') ); ?>">
					<option value="default" <?php selected( $instance['text_transform'], 'default' ) ?>><?php esc_html_e( 'Default', 'ocean-extra' ); ?></option>
					<option value="capitalize" <?php selected( $instance['text_transform'], 'capitalize' ) ?>><?php esc_html_e( 'Capitalize', 'ocean-extra' ); ?></option>
					<option value="lowercase" <?php selected( $instance['text_transform'], 'lowercase' ) ?>><?php esc_html_e( 'Lowercase', 'ocean-extra' ); ?></option>
					<option value="uppercase" <?php selected( $instance['text_transform'], 'uppercase' ) ?>><?php esc_html_e( 'Uppercase', 'ocean-extra' ); ?></option>
					<option value="none" <?php selected( $instance['text_transform'], 'none' ) ?>><?php esc_html_e( 'None', 'ocean-extra' ); ?></option>
				</select>
			</p>

		<?php
		}

		/**
		 * Retrieves the taxonomy for the current Tag cloud widget instance.
		 *
		 * @since 4.4.0
		 * @access public
		 *
		 * @param array $instance Current settings.
		 * @return string Name of the current taxonomy if set, otherwise 'post_tag'.
		 */
		public function _get_current_taxonomy($instance) {
			if ( !empty($instance['taxonomy']) && taxonomy_exists($instance['taxonomy']) )
				return $instance['taxonomy'];

			return 'post_tag';
		}

	}
}
register_widget( 'Ocean_Extra_Tags_Widget' );