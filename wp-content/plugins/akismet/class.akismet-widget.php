<?php
/**
 * @package Akismet
 */

// We plan to gradually remove all of the disabled lint rules below.
// phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped

class Akismet_Widget extends WP_Widget {

	function __construct() {
		load_plugin_textdomain( 'akismet' );

		add_action( 'wp_enqueue_scripts', array( $this, 'akismet_widget_enqueue_styles' ) );

		parent::__construct(
			'akismet_widget',
			__( 'Akismet Widget', 'akismet' ),
			array( 'description' => __( 'Display the number of spam comments Akismet has caught', 'akismet' ) )
		);
	}

	public function akismet_widget_enqueue_styles() {
		// Register the stylesheet handle
		wp_register_style( 'akismet-widget-style', false ); // No external file, just a handle

		// Enqueue the registered stylesheet
		wp_enqueue_style( 'akismet-widget-style' );

		// Add inline styles
		$inline_css = "
			.a-stats {
				--akismet-color-mid-green: #357b49;
				--akismet-color-white: #fff;
				--akismet-color-light-grey: #f6f7f7;

				max-width: 350px;
				width: auto;
			}

			.a-stats * {
				all: unset;
				box-sizing: border-box;
			}

			.a-stats strong {
				font-weight: 600;
			}

			.a-stats a.a-stats__link,
			.a-stats a.a-stats__link:visited,
			.a-stats a.a-stats__link:active {
				background: var(--akismet-color-mid-green);
				border: none;
				box-shadow: none;
				border-radius: 8px;
				color: var(--akismet-color-white);
				cursor: pointer;
				display: block;
				font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', 'Roboto', 'Oxygen-Sans', 'Ubuntu', 'Cantarell', 'Helvetica Neue', sans-serif;
				font-weight: 500;
				padding: 12px;
				text-align: center;
				text-decoration: none;
				transition: all 0.2s ease;
			}

			/* Extra specificity to deal with TwentyTwentyOne focus style */
			.widget .a-stats a.a-stats__link:focus {
				background: var(--akismet-color-mid-green);
				color: var(--akismet-color-white);
				text-decoration: none;
			}

			.a-stats a.a-stats__link:hover {
				filter: brightness(110%);
				box-shadow: 0 4px 12px rgba(0, 0, 0, 0.06), 0 0 2px rgba(0, 0, 0, 0.16);
			}

			.a-stats .count {
				color: var(--akismet-color-white);
				display: block;
				font-size: 1.5em;
				line-height: 1.4;
				padding: 0 13px;
				white-space: nowrap;
			}
		";
		wp_add_inline_style( 'akismet-widget-style', $inline_css );
	}

	function form( $instance ) {
		if ( $instance && isset( $instance['title'] ) ) {
			$title = $instance['title'];
		} else {
			$title = __( 'Spam Blocked', 'akismet' );
		}
		?>

		<p>
		<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php esc_html_e( 'Title:', 'akismet' ); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
		</p>

		<?php
	}

	function update( $new_instance, $old_instance ) {
		$instance          = array();
		$instance['title'] = strip_tags( $new_instance['title'] );
		return $instance;
	}

	function widget( $args, $instance ) {
		$count = get_option( 'akismet_spam_count' );

		if ( ! isset( $instance['title'] ) ) {
			$instance['title'] = __( 'Spam Blocked', 'akismet' );
		}

		echo $args['before_widget'];
		if ( ! empty( $instance['title'] ) ) {
			echo $args['before_title'];
			echo esc_html( $instance['title'] );
			echo $args['after_title'];
		}
		?>

		<div class="a-stats">
			<?php // Specifying colors inline for maximum specificity without using !important ?>
			<a href="https://akismet.com" class="a-stats__link" target="_blank" rel="noopener" style="background-color: var(--akismet-color-mid-green); color: var(--akismet-color-white);">
				<?php

				echo wp_kses(
					sprintf(
					/* translators: The placeholder is the number of pieces of spam blocked by Akismet. */
						_n(
							'<strong class="count">%1$s spam</strong> blocked by <strong>Akismet</strong>',
							'<strong class="count">%1$s spam</strong> blocked by <strong>Akismet</strong>',
							$count,
							'akismet'
						),
						number_format_i18n( $count )
					),
					array(
						'strong' => array(
							'class' => true,
						),
					)
				);

				?>
			</a>
		</div>

		<?php
		echo $args['after_widget'];
	}
}

function akismet_register_widgets() {
	register_widget( 'Akismet_Widget' );
}

add_action( 'widgets_init', 'akismet_register_widgets' );
