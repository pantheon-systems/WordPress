<?php

/**
 * Ocean Notifications.
 */
class Ocean_Notifications {

	protected static $instance = null;
	public $option             = false;

	const SOURCE_URL = 'https://notifi.oceanwp.org/notifications/notifications-info.json';
	const OPTION_KEY = 'ocean_notifications';

	public static function get_instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	public function __construct() {
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueues' ) );
		add_action( 'ocean_notifications_content', array( $this, 'output' ) );

		// Action for cron
		add_action( 'ocean_admin_notifications_update', array( $this, 'update' ) );

		add_action( 'wp_ajax_ocean_notification_block', array( $this, 'block' ) );
	}

	/**
	 * Get option value
	 */
	public function get_option( $cache = true ) {
		if ( $this->option && $cache ) {
			return $this->option;
		}

		$option = get_option( self::OPTION_KEY, array() );

		$this->option = array(
			'update'        => ! empty( $option['update'] ) ? $option['update'] : 0,
			'notifications' => ! empty( $option['notifications'] ) ? $option['notifications'] : array(),
			'blocked'       => ! empty( $option['blocked'] ) ? $option['blocked'] : array(),
		);

		return $this->option;
	}

	/**
	 * Fetch notifications from feed
	 */
	public function fetch_notifications() {
		$res = wp_remote_get( self::SOURCE_URL );

		if ( is_wp_error( $res ) ) {
			return array();
		}

		$body = wp_remote_retrieve_body( $res );

		if ( empty( $body ) ) {
			return array();
		}

		return $this->validate( json_decode( $body, true ) );
	}

	/**
	 * Validate notification data before it is saved
	 */
	public function validate( $notifications ) {
		$data = array();

		if ( ! is_array( $notifications ) || empty( $notifications || empty( $notifications['notifications'] ) ) ) {
			return $data;
		}

		$option = $this->get_option();

		foreach ( $notifications['notifications'] as $notification ) {

			if ( empty( $notification['content'] ) ) {
				continue;
			}

			if ( ! empty( $notification['end_date'] ) && time() > strtotime( $notification['end_date'] ) ) {
				continue;
			}

			if ( ! empty( $option['blocked'] ) && in_array( $notification['id'], $option['blocked'] ) ) {
				continue;
			}

			$data[] = $notification;
		}

		return $data;
	}

	/**
	 * Check start and end dates
	 */
	public function check_dates( $notifications ) {
		if ( ! is_array( $notifications ) || empty( $notifications ) ) {
			return array();
		}

		foreach ( $notifications as $key => $notification ) {
			if (
				( ! empty( $notification['start_date'] ) && time() < strtotime( $notification['start_date'] ) ) ||
				( ! empty( $notification['end_date'] ) && time() > strtotime( $notification['end_date'] ) )
			) {
				unset( $notifications[ $key ] );
			}
		}

		return $notifications;
	}

	/**
	 * Get notification details
	 */
	public function get() {
		$option = $this->get_option();

		if ( empty( $option['update'] ) || time() > $option['update'] + DAY_IN_SECONDS ) {
			if ( ! wp_next_scheduled( 'ocean_admin_notifications_update' ) ) {
				wp_schedule_single_event( time() + 60, 'ocean_admin_notifications_update' );
			}
		}

		$notifications = ! empty( $option['notifications'] ) ? $this->check_dates( $option['notifications'] ) : array();

		return array_merge( $notifications, array() );
	}

	/**
	 * Get notifications count
	 */
	public function get_count() {
		return count( $this->get() );
	}

	/**
	 * Update notification details from remote storage
	 */
	public function update() {
		$notifications = $this->fetch_notifications();
		$option        = $this->get_option();

		update_option(
			self::OPTION_KEY,
			array(
				'update'        => time(),
				'notifications' => $notifications,
				'blocked'       => $option['blocked'],
			)
		);
	}

	public function enqueues() {
		$notifications = $this->get();

		if ( empty( $notifications ) ) {
			return;
		}

		wp_enqueue_style(
			'ocean-admin-notifications',
			plugins_url( 'assets/css/notifications.min.css', __FILE__ ),
			array(),
			OCEANWP_THEME_VERSION
		);

		wp_enqueue_script(
			'ocean-admin-notifications',
			plugins_url( 'assets/js/notifications.min.js', __FILE__ ),
			array( 'jquery' ),
			OCEANWP_THEME_VERSION
		);

		wp_localize_script(
			'ocean-admin-notifications',
			'ocean_notifications_admin',
			$this->get_localized_data()
		);
	}

	/**
	 * Output notifications
	 */
	public function output() {
		$notifications = $this->get();

		if ( empty( $notifications ) ) {
			return;
		}

		$notifications_html   = '';
		$current_class        = ' current';
		$content_allowed_tags = array(
			'em'     => array(),
			'strong' => array(),
			'span'   => array(
				'style' => array(),
			),
			'a'      => array(
				'href'   => array(),
				'target' => array(),
				'rel'    => array(),
			),
		);

		foreach ( $notifications as $notification ) {

			// Buttons HTML
			$buttons_html = '';
			if ( ! empty( $notification['button_1_data'] ) ) {
				$buttons_html .= sprintf(
					'<a href="%1$s" class="button button-%2$s"%3$s>%4$s</a>',
					! empty( $notification['button_1_data']['url'] ) ? esc_url( $notification['button_1_data']['url'] ) : '',
					$notification['button_1_data']['primary'] === 'yes' ? 'primary' : 'secondary',
					! empty( $notification['button_1_data']['target'] ) && $notification['button_1_data']['target'] === '_blank' ? ' target="_blank" rel="noopener noreferrer"' : '',
					! empty( $notification['button_1_data']['title'] ) ? sanitize_text_field( $notification['button_1_data']['title'] ) : ''
				);
			}

			if ( ! empty( $notification['button_2_data'] ) ) {
				$buttons_html .= sprintf(
					'<a href="%1$s" class="button button-%2$s"%3$s>%4$s</a>',
					! empty( $notification['button_2_data']['url'] ) ? esc_url( $notification['button_2_data']['url'] ) : '',
					$notification['button_2_data']['primary'] === 'yes' ? 'primary' : 'secondary',
					! empty( $notification['button_2_data']['target'] ) && $notification['button_2_data']['target'] === '_blank' ? ' target="_blank" rel="noopener noreferrer"' : '',
					! empty( $notification['button_2_data']['title'] ) ? sanitize_text_field( $notification['button_2_data']['title'] ) : ''
				);
			}

			$buttons_html = ! empty( $buttons_html ) ? '<div class="ocean-notifications-buttons">' . $buttons_html . '</div>' : '';

			// Notification HTML
			$notifications_html .= sprintf(
				'<div class="ocean-notifications-message%5$s" data-message-id="%4$s">
					<h3 class="ocean-notifications-title">%1$s</h3>
					<p class="ocean-notifications-content">%2$s</p>
					%3$s
				</div>',
				! empty( $notification['title'] ) ? sanitize_text_field( $notification['title'] ) : '',
				! empty( $notification['content'] ) ? wp_kses( $notification['content'], $content_allowed_tags ) : '',
				$buttons_html,
				! empty( $notification['id'] ) ? esc_attr( sanitize_text_field( $notification['id'] ) ) : 0,
				$current_class
			);

			$current_class = '';
		}
		?>

		<div id="ocean-notifications">

			<div class="ocean-notifications-header">
				<div class="ocean-notifications-icon">
					<svg id="Layer_1" height="17" viewBox="0 0 512 512" width="17" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" data-name="Layer 1"><linearGradient id="Blue_2" gradientUnits="userSpaceOnUse" x1="6" x2="506" y1="256" y2="256"><stop offset="0" stop-color="#2bc4f3"/><stop offset=".5" stop-color="#00aeee"/><stop offset="1" stop-color="#0095da"/></linearGradient><linearGradient id="linear-gradient" gradientUnits="userSpaceOnUse" x1="154.596" x2="432.772" y1="154.595" y2="432.771"><stop offset="0"/><stop offset="1" stop-opacity="0"/></linearGradient><circle cx="256" cy="256" fill="url(#Blue_2)" r="250"/><path d="m502.233 299.422-144.833-144.829c-25.354-23.019-60.438-36.482-101.185-36.482-84.388 0-144.607 57.661-144.607 137.889 0 41.369 16.012 76.738 43.093 101.3l144.91 144.9a250.221 250.221 0 0 0 202.622-202.778z" fill="url(#linear-gradient)" opacity=".49"/><path d="m256.219 393.889c-84.388 0-144.607-57.661-144.607-137.889s60.219-137.889 144.607-137.889c84.168 0 144.169 57.443 144.169 137.889s-60 137.885-144.169 137.885zm0-67.291c39.952 0 68.872-29.513 68.872-70.591s-28.92-70.594-68.872-70.594-69.31 29.727-69.31 70.587 29.352 70.59 69.31 70.59z" fill="#fff"/></svg>
				</div>
				<div class="ocean-notifications-title"><?php esc_html_e( 'OceanWP News', 'ocean-extra' ); ?></div>
			</div>

			<div class="ocean-notifications-body">
				<button type="button" class="notice-dismiss"><span class="screen-reader-text"><?php echo esc_attr__( 'Block this message', 'ocean-extra' ); ?></span></button>

				<?php if ( count( $notifications ) > 1 ) : ?>
					<div class="navigation">
						<a class="prev">
							<span class="screen-reader-text"><?php esc_attr_e( 'Previous message', 'ocean-extra' ); ?></span>
							<span aria-hidden="true">‹</span>
						</a>
						<a class="next">
							<span class="screen-reader-text"><?php esc_attr_e( 'Next message', 'ocean-extra' ); ?>"></span>
							<span aria-hidden="true">›</span>
						</a>
					</div>
				<?php endif; ?>

				<div class="ocean-notifications-messages">
					<?php
					echo $notifications_html;
					?>
				</div>
			</div>
		</div>
		<?php
	}

	/**
	 * Block notification
	 */
	public function block() {
		check_ajax_referer( 'ocean-notifications-admin', 'nonce' );

		if ( empty( $_POST['id'] ) ) {
			wp_send_json_error();
		}

		$id     = sanitize_text_field( wp_unslash( $_POST['id'] ) );
		$option = $this->get_option();
		$type   = 'notifications';

		$option['blocked'][] = $id;
		$option['blocked']   = array_unique( $option['blocked'] );

		if ( is_array( $option[ $type ] ) && ! empty( $option[ $type ] ) ) {
			foreach ( $option[ $type ] as $key => $notification ) {
				if ( (string) $notification['id'] === (string) $id ) {
					unset( $option[ $type ][ $key ] );
					break;
				}
			}
		}

		update_option( 'ocean_notifications', $option );

		wp_send_json_success();
	}

	private function get_localized_data() {
		 $strings = array(
			 'ajax_url' => admin_url( 'admin-ajax.php' ),
			 'nonce'    => wp_create_nonce( 'ocean-notifications-admin' ),
		 );

		 return $strings;
	}
}

function ocean_notifications() {
	return Ocean_Notifications::get_instance();
}

// Initialize Ocean_Notifications
ocean_notifications();
