<?php
/**
 * Twitter Widget.
 *
 * @package OceanWP WordPress theme
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Ocean_Extra_Twitter_Widget' ) ) {
	class Ocean_Extra_Twitter_Widget extends WP_Widget {
		/**
		* Register widget with WordPress.
		*/
		public function __construct() {
			parent::__construct(
				'ocean_twitter',
				esc_html__( '&raquo; Twitter', 'ocean-extra' ),
				array(
					'classname'   => 'widget-oceanwp-twitter twitter-widget',
					'description' => esc_html__( 'Pulls in tweets from your twitter account.', 'ocean-extra' ),
					'customize_selective_refresh' => true,
				)
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
			$instance['lang']  = substr( strtoupper( get_locale() ), 0, 2 );

			echo $args['before_widget'];

			$title = isset( $instance['title'] ) ? $instance['title'] : '';

			$title = apply_filters( 'widget_title', $title );
			if ( ! empty( $title ) ) {
				echo $args['before_title'] . $title . $args['after_title'];
			}

			// Start tag output
			// This tag is transformed into the widget markup by Twitter's
			echo '<a class="twitter-timeline"';

			$data_attribs = array(
				'width',
				'height',
				'theme',
				'link-color',
				'border-color',
				'tweet-limit',
				'lang'
			);
			foreach ( $data_attribs as $att ) {
				if ( ! empty( $instance[ $att ] ) && ! is_array( $instance[ $att ] ) ) {
					echo ' data-' . esc_attr( $att ) . '="' . esc_attr( $instance[ $att ] ) . '"';
				}
			}

			if ( ! empty( $instance['chrome'] ) && is_array( $instance['chrome'] ) ) {
				echo ' data-chrome="' . esc_attr( join( ' ', $instance['chrome'] ) ) . '"';
			}

			if ( $instance['username'] ) {
				echo ' href="https://twitter.com/' . esc_attr( $instance['username'] ) . '"';
			}

			// End tag output
			echo '><script async src="https://platform.twitter.com/widgets.js" charset="utf-8"></script>' . esc_html__( 'My Tweets', 'ocean-extra' ) . '</a>';
			// End tag output

			echo $args['after_widget'];
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
			$instance                  = array();
			$instance['title']         = sanitize_text_field( $new_instance['title'] );

			$width = (int) $new_instance['width'];
			if ( $width ) {
				// From publish.twitter.com: 220 <= width <= 1200
				$instance['width'] = min( max( $width, 220 ), 1200 );
			} else {
				$instance['width'] = '';
			}

			$height = (int) $new_instance['height'];
			if ( $height ) {
				// From publish.twitter.com: height >= 200
				$instance['height'] = max( $height, 200 );
			} else {
				$instance['height'] = '';
			}

			$tweet_limit = (int) $new_instance['tweet-limit'];
			if ( $tweet_limit ) {
				$instance['tweet-limit'] = min( max( $tweet_limit, 1 ), 20 );
				/**
				 * A timeline with a specified limit is expanded to the height of those Tweets.
				 * The specified height value no longer applies, so reject the height value
				 * when a valid limit is set: a widget attempting to save both limit 5 and
				 * height 400 would be saved with just limit 5.
				 */
				$instance['height'] = '';
			} else {
				$instance['tweet-limit'] = null;
			}

			$instance['username'] = sanitize_text_field( $new_instance['username'] );

			$hex_regex = '/#([a-f]|[A-F]|[0-9]){3}(([a-f]|[A-F]|[0-9]){3})?\b/';
			foreach ( array( 'link-color', 'border-color' ) as $color ) {
				$new_color = sanitize_text_field( $new_instance[ $color ] );
				if ( preg_match( $hex_regex, $new_color ) ) {
					$instance[ $color ] = $new_color;
				}

			}

			$instance['theme'] = 'light';
			if ( in_array( $new_instance['theme'], array( 'light', 'dark' ) ) ) {
				$instance['theme'] = $new_instance['theme'];
			}

			$instance['chrome'] = array();
			$chrome_settings = array(
				'noheader',
				'nofooter',
				'noborders',
				'transparent',
				'noscrollbar',
			);
			if ( isset( $new_instance['chrome'] ) ) {
				foreach ( $new_instance['chrome'] as $chrome ) {
					if ( in_array( $chrome, $chrome_settings ) ) {
						$instance['chrome'][] = $chrome;
					}
				}
			}

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
			$defaults = array(
				'title'        => esc_html__( 'Follow me on Twitter', 'ocean-extra' ),
				'width'        => '',
				'height'       => '400',
				'username'     => '',
				'link-color'   => '#f96e5b',
				'border-color' => '#e8e8e8',
				'theme'        => 'light',
				'chrome'       => array(),
				'tweet-limit'  => 5,
			);

			$instance = wp_parse_args( (array) $instance, $defaults );
			?>

			<p>
				<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php esc_html_e( 'Title:', 'ocean-extra' ); ?></label>
				<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $instance['title'] ); ?>" />
			</p>

			<p>
				<label for="<?php echo $this->get_field_id( 'width' ); ?>"><?php esc_html_e( 'Width (px):', 'ocean-extra' ); ?></label>
				<input class="widefat" id="<?php echo $this->get_field_id( 'width' ); ?>" name="<?php echo $this->get_field_name( 'width' ); ?>" type="text" value="<?php echo esc_attr( $instance['width'] ); ?>" />
			</p>

			 <p>
				<label for="<?php echo $this->get_field_id( 'height' ); ?>"><?php esc_html_e( 'Height (px):', 'ocean-extra' ); ?></label>
				<input class="widefat" id="<?php echo $this->get_field_id( 'height' ); ?>" name="<?php echo $this->get_field_name( 'height' ); ?>" type="text" value="<?php echo esc_attr( $instance['height'] ); ?>" />
			</p>

			<p>
				<label for="<?php echo $this->get_field_id( 'tweet-limit' ); ?>"><?php esc_html_e( '# of Tweets Shown:', 'ocean-extra' ); ?></label>
				<input class="widefat" id="<?php echo $this->get_field_id( 'tweet-limit' ); ?>" name="<?php echo $this->get_field_name( 'tweet-limit' ); ?>" type="number" min="1" max="20" value="<?php echo esc_attr( $instance['tweet-limit'] ); ?>" />
			</p>

			<p>
				<label for="<?php echo $this->get_field_id( 'username' ); ?>"><?php esc_html_e( 'Username:', 'ocean-extra' ); ?></label>
				<input class="widefat" id="<?php echo $this->get_field_id( 'username' ); ?>" name="<?php echo $this->get_field_name( 'username' ); ?>" type="text" value="<?php echo esc_attr( $instance['username'] ); ?>" />
			</p>

			<p>
				<label for="<?php echo $this->get_field_id( 'chrome-noheader' ); ?>"><?php esc_html_e( 'Layout Options:', 'ocean-extra' ); ?></label><br />
				<input type="checkbox"<?php checked( in_array( 'noheader', $instance['chrome'] ) ); ?> id="<?php echo $this->get_field_id( 'chrome-noheader' ); ?>" name="<?php echo $this->get_field_name( 'chrome' ); ?>[]" value="noheader" /> <label for="<?php echo $this->get_field_id( 'chrome-noheader' ); ?>"><?php esc_html_e( 'No Header', 'ocean-extra' ); ?></label><br />
				<input type="checkbox"<?php checked( in_array( 'nofooter', $instance['chrome'] ) ); ?> id="<?php echo $this->get_field_id( 'chrome-nofooter' ); ?>" name="<?php echo $this->get_field_name( 'chrome' ); ?>[]" value="nofooter" /> <label for="<?php echo $this->get_field_id( 'chrome-nofooter' ); ?>"><?php esc_html_e( 'No Footer', 'ocean-extra' ); ?></label><br />
				<input type="checkbox"<?php checked( in_array( 'noborders', $instance['chrome'] ) ); ?> id="<?php echo $this->get_field_id( 'chrome-noborders' ); ?>" name="<?php echo $this->get_field_name( 'chrome' ); ?>[]" value="noborders" /> <label for="<?php echo $this->get_field_id( 'chrome-noborders' ); ?>"><?php esc_html_e( 'No Borders', 'ocean-extra' ); ?></label><br />
				<input type="checkbox"<?php checked( in_array( 'noscrollbar', $instance['chrome'] ) ); ?> id="<?php echo $this->get_field_id( 'chrome-noscrollbar' ); ?>" name="<?php echo $this->get_field_name( 'chrome' ); ?>[]" value="noscrollbar" /> <label for="<?php echo $this->get_field_id( 'chrome-noscrollbar' ); ?>"><?php esc_html_e( 'No Scrollbar', 'ocean-extra' ); ?></label><br />
				<input type="checkbox"<?php checked( in_array( 'transparent', $instance['chrome'] ) ); ?> id="<?php echo $this->get_field_id( 'chrome-transparent' ); ?>" name="<?php echo $this->get_field_name( 'chrome' ); ?>[]" value="transparent" /> <label for="<?php echo $this->get_field_id( 'chrome-transparent' ); ?>"><?php esc_html_e( 'Transparent Background', 'ocean-extra' ); ?></label>
			</p>

			<p>
				<label for="<?php echo $this->get_field_id( 'link-color' ); ?>"><?php esc_html_e( 'Link Color (hex):', 'ocean-extra' ); ?></label>
				<input class="widefat" id="<?php echo $this->get_field_id( 'link-color' ); ?>" name="<?php echo $this->get_field_name( 'link-color' ); ?>" type="text" value="<?php echo esc_attr( $instance['link-color'] ); ?>" />
			</p>

			<p>
				<label for="<?php echo $this->get_field_id( 'border-color' ); ?>"><?php esc_html_e( 'Border Color (hex):', 'ocean-extra' ); ?></label>
				<input class="widefat" id="<?php echo $this->get_field_id( 'border-color' ); ?>" name="<?php echo $this->get_field_name( 'border-color' ); ?>" type="text" value="<?php echo esc_attr( $instance['border-color'] ); ?>" />
			</p>

			<p>
				<label for="<?php echo $this->get_field_id( 'theme' ); ?>"><?php esc_html_e( 'Timeline Theme:', 'ocean-extra' ); ?></label>
				<select name="<?php echo $this->get_field_name( 'theme' ); ?>" id="<?php echo $this->get_field_id( 'theme' ); ?>" class="widefat">
					<option value="light"<?php selected( $instance['theme'], 'light' ); ?>><?php esc_html_e( 'Light', 'ocean-extra' ); ?></option>
					<option value="dark"<?php selected( $instance['theme'], 'dark' ); ?>><?php esc_html_e( 'Dark', 'ocean-extra' ); ?></option>
				</select>
			</p>
		<?php
		}
	}
}
register_widget( 'Ocean_Extra_Twitter_Widget' );