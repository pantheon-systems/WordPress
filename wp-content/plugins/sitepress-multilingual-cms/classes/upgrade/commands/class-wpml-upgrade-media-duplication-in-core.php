<?php

class WPML_Upgrade_Media_Duplication_In_Core implements IWPML_Upgrade_Command {

	const DUPLICATE_FEATURED_META_KEY = '_wpml_media_featured';
	const FEATURED_AS_TRANSLATED_META_KEY = '_wpml_featured_image_as_translated';
	const TRANSIENT_DEFERRED_UPGRADE_IN_PROGRESS = 'wpml_upgrade_media_duplication_in_progress';
	const MAX_TIME = 10;

	/** @var SitePress */
	private $sitepress;

	/** @var WPML_Upgrade $wpml_upgrade */
	private $wpml_upgrade;

	/** @var wpdb $wpdb */
	private $wpdb;

	/** @var WPML_Notices $notices */
	private $notices;

	/** @var WPML_Media_Attachments_Duplication $media_attachment_duplication */
	private $media_attachment_duplication;

	/** @var array $post_thumbnail_map */
	private $post_thumbnail_map;

	/** @var int $start_time */
	private $start_time;

	public function __construct( array $args ) {
		$this->sitepress    = $args[0];
		$this->wpdb         = $args[1]->get_wpdb();
		$this->notices      = $args[2];
	}

	/**
	 * @return bool
	 */
	public function run_admin() {
		$this->update_global_settings();

		if ( $this->has_notice() ) {
			$this->create_or_refresh_notice();
			return false;
		}

		if ( $this->find_posts_altered_between_402_and_404() ) {
			/**
			 * The rest of the upgrade needs to run when all the custom post types are registered
			 */
			add_action( 'init', array( $this, 'deferred_upgrade_admin' ), PHP_INT_MAX );
			return false;
		}

		$this->remove_notice();
		return true;
	}

	public function deferred_upgrade_admin() {
		list( $is_complete ) = $this->process_upgrade();

		if ( ! $is_complete ) { // We could not complete the upgrade in the same request
			$this->create_or_refresh_notice();
		}
	}

	/**
	 * @return bool
	 */
	public function run_ajax() {
		/**
		 * The rest of the upgrade needs to run when all the custom post types are registered
		 */
		add_action( 'init', array( $this, 'deferred_upgrade_ajax' ), PHP_INT_MAX );
		return false;
	}

	public function deferred_upgrade_ajax() {
		list( $is_complete, $remaining ) = $this->process_upgrade();

		if ( $is_complete ) {
			$data = array(
				'response' => esc_html__( 'The upgrade is complete.', 'sitepress' ),
				'complete' => true,
			);
		} elseif ( $remaining ) {
			$data = array(
				'response' => sprintf( esc_html__( '%d items remaining...', 'sitepress' ), $remaining ),
				'complete' => false,
			);
		} else {
			$data = array( 'concurrent_request' => true );
		}

		wp_send_json_success( $data );
	}

	/**
	 * @return bool
	 */
	public function run_frontend() {
		return false;
	}

	/**
	 * @return array
	 */
	public function get_results() {
		return array();
	}

	private function process_upgrade() {
		$remaining   = null;
		$is_complete = false;

		if ( ! $this->acquire_lock() ) {
			return $is_complete;
		}

		$this->start_timer();
		$source_posts = $this->find_posts_altered_between_402_and_404();
		$remaining    = count( $source_posts );

		$should_duplicate_media = $this->should_duplicate_media();

		foreach ( $source_posts as $key => $source_post ) {
			if ( $should_duplicate_media ) {
				$this->duplicate_missing_attachments_for_post( $source_post );
			}

			$this->duplicate_missing_featured_image_for_post( $source_post );

			$remaining--;

			if ( $this->is_max_time_elapsed() ) {
				break;
			}
		}

		if ( ! $this->is_max_time_elapsed() ) {
			$this->cleanup_display_featured_as_translated_meta();
			$this->remove_notice();
			$is_complete = true;
		}

		$this->release_lock();
		return array( $is_complete, $remaining );
	}

	private function get_notice_content() {
		ob_start();

		$action = str_replace( '_', '-', strtolower( __CLASS__ ) );

		?>
		<div class="js-main-content">
			<h2><?php esc_html_e( "WPML needs to upgrade the post's media information.", 'sitepress' ); ?></h2>

			<p><?php esc_html_e( "We couldn't complete the whole process in one request. Please click on the \"Upgrade\" button to continue.", 'sitepress' ); ?></p>

			<input type="button" class="button-primary" name="upgrade" value="<?php esc_attr_e( 'Upgrade' ); ?>"/>
			<span class="js-wpml-upgrade-progress" style="display:none"><?php esc_html_e( 'Starting...', 'sitepress' ); ?></span>
			<?php wp_nonce_field( $action . '-nonce', $action . '-nonce' ); ?>
		</div>
		<script>
			jQuery( document ).ready( function ( $ ) {
				var upgradeProgress = $('.js-wpml-upgrade-progress');
				var ajax_request = function () {
					$.ajax( {
						url: ajaxurl,
						type: "POST",
						data: {
							action: '<?php echo $action; ?>',
							nonce: $( '#<?php echo $action; ?>-nonce' ).val()
						},
						success: function ( response ) {
							if ( response.data.concurrent_request ) {
								setTimeout(ajax_request, 3000);
							} else {
								upgradeProgress.text( response.data.response );

								if ( ! response.data.complete ) {
									ajax_request();
								}
							}
						},
                        error: function(jqXHR, textStatus, errorThrown) {
							var errorData = '<p>status code: '+jqXHR.status+'</p><p>errorThrown: ' + errorThrown + '</p><p>jqXHR.responseText:</p><div>'+jqXHR.responseText + '</div>';
							upgradeProgress.html( '<?php echo esc_html__('The following exception has occurred while running the migration, please try again later or contact support if the problem persists.', 'sitepress'); ?><hr>' + errorData );
							console.log('jqXHR:');
							console.log(jqXHR);
							console.log('textStatus:');
							console.log(textStatus);
							console.log('errorThrown:');
							console.log(errorThrown);
                        }
					} );
				};

				$( '.js-main-content' ).find( 'input[name="upgrade"]' ).on( 'click', function ( e ) {
					$( this ).prop( 'disabled', true );
					$('.js-wpml-upgrade-progress').show();
					ajax_request();
				} );
			} );
		</script>
		<?php

		return ob_get_clean();
	}

	/**
	 * Some posts could have been created between WPML 4.0.2 and WPML 4.0.4
	 * And they would have '_wpml_featured_image_as_translated' but not '_wpml_media_featured'
	 */
	private function find_posts_altered_between_402_and_404() {
		$source_posts_missing_duplicate_featured_meta =
			"SELECT pm.post_id AS ID, pm.meta_value AS duplicate_featured, t.trid, t.element_type FROM {$this->wpdb->postmeta} AS pm
			 LEFT JOIN {$this->wpdb->prefix}icl_translations AS t
			 	ON t.element_id = pm.post_id AND t.element_type LIKE 'post_%'
			 LEFT JOIN {$this->wpdb->postmeta} AS duplicate_featured
				ON duplicate_featured.post_id = pm.post_id AND duplicate_featured.meta_key = '" . self::DUPLICATE_FEATURED_META_KEY . "'
			 WHERE pm.meta_key = '" . self::FEATURED_AS_TRANSLATED_META_KEY . "'
				AND t.source_language_code IS NULL
				AND duplicate_featured.meta_value IS NULL
			";

		return $this->wpdb->get_results( $source_posts_missing_duplicate_featured_meta );
	}

	private function duplicate_missing_featured_image_for_post( $post ) {
		if ( $post->duplicate_featured == 1 && $this->has_thumbnail( $post->ID ) ) {
			$post->post_type = preg_replace( '/^post_/', '', $post->element_type );
			$this->get_media_attachment_duplication()->duplicate_featured_image_in_post( $post, $this->get_post_thumbnail_map() );
		}

		// Add the meta to the source post and its translations
		$translations = $this->sitepress->get_element_translations( $post->trid, $post->element_type );
		$post_ids     = wp_list_pluck( $translations, 'element_id' );

		$this->wpdb->query(
			$this->wpdb->prepare(
				"INSERT INTO {$this->wpdb->prefix}postmeta ( post_id, meta_key, meta_value )
				 SELECT post_id, '" . self::DUPLICATE_FEATURED_META_KEY . "', %d
		         FROM {$this->wpdb->postmeta} WHERE post_id IN(" . wpml_prepare_in( $post_ids ) . ")
		            AND meta_key = '" . self::FEATURED_AS_TRANSLATED_META_KEY . "'",
				$post->duplicate_featured
			)
		);
	}

	private function has_thumbnail( $post_id ) {
		return (bool) $this->wpdb->get_var(
			$this->wpdb->prepare(
				"SELECT meta_value FROM {$this->wpdb->postmeta} WHERE meta_key = '_thumbnail_id' AND post_id = %d",
				$post_id
			)
		);
	}

	/**
	 * @return array
	 */
	private function get_post_thumbnail_map() {
		if ( ! $this->post_thumbnail_map ) {
			list( $this->post_thumbnail_map ) = $this->get_media_attachment_duplication()->get_post_thumbnail_map();
		}

		return $this->post_thumbnail_map;
	}

	private function duplicate_missing_attachments_for_post( $post ) {
		$attachment_ids = $this->wpdb->get_col(
			$this->wpdb->prepare(
				"SELECT ID FROM {$this->wpdb->posts} WHERE post_type = 'attachment' AND post_parent = %d",
				$post->ID
			)
		);

		foreach ( $attachment_ids as $attachment_id ) {

			foreach ( $this->sitepress->get_active_languages() as $language_code => $active_language ) {
				$this->get_media_attachment_duplication()->create_duplicate_attachment( (int) $attachment_id, (int) $post->ID, $language_code );
			}

		}

		update_post_meta( $post->ID, '_wpml_media_duplicate', true );
	}

	private function should_duplicate_media() {
		$settings = $this->get_media_settings();

		return isset( $settings['new_content_settings']['duplicate_media'] )
		     && $settings['new_content_settings']['duplicate_media'];
	}

	public function update_global_settings() {

		$settings = $this->get_media_settings();
		$settings['new_content_settings']['always_translate_media'] = true;
		$settings['new_content_settings']['duplicate_media']        = true;
		$settings['new_content_settings']['duplicate_featured']     = true;

		update_option( WPML_Media_Duplication_Setup::MEDIA_SETTINGS_OPTION_KEY, $settings );
	}

	private function cleanup_display_featured_as_translated_meta() {
		$this->wpdb->query( "DELETE FROM {$this->wpdb->postmeta} WHERE meta_key = '" . self::FEATURED_AS_TRANSLATED_META_KEY . "'" );
	}

	private function mark_migration_completed() {
		$this->wpml_upgrade->mark_command_as_executed( $this );
	}

	private function get_media_settings() {
		return get_option( WPML_Media_Duplication_Setup::MEDIA_SETTINGS_OPTION_KEY, array() );
	}

	private function get_media_attachment_duplication() {
		global $wpml_language_resolution;

		if ( ! $this->media_attachment_duplication ) {
			$this->media_attachment_duplication = new WPML_Media_Attachments_Duplication(
				$this->sitepress,
				new WPML_Model_Attachments( $this->sitepress, wpml_get_post_status_helper() ),
				$this->wpdb,
				$wpml_language_resolution
			);
		}

		return $this->media_attachment_duplication;
	}

	private function acquire_lock() {
		$lock = get_transient( self::TRANSIENT_DEFERRED_UPGRADE_IN_PROGRESS );

		if ( $lock ) {
			return false;
		}

		set_transient( self::TRANSIENT_DEFERRED_UPGRADE_IN_PROGRESS, true, MINUTE_IN_SECONDS );
		return true;
	}

	private function release_lock() {
		delete_transient( self::TRANSIENT_DEFERRED_UPGRADE_IN_PROGRESS );
	}

	private function start_timer() {
		$this->start_time = time();
	}

	private function is_max_time_elapsed() {
		return self::MAX_TIME <= ( time() - $this->start_time );
	}

	private function remove_notice() {
		$this->notices->remove_notice( 'default',  __CLASS__ );
	}

	private function create_or_refresh_notice() {
		$notice = $this->notices->create_notice( __CLASS__, $this->get_notice_content() );
		$notice->add_display_callback( array( 'WPML_Notice_Show_On_Dashboard_And_WPML_Pages', 'is_on_page' ) );
		$notice->set_css_class_types( 'info' );
		$this->notices->add_notice( $notice );
	}

	private function has_notice() {
		return $this->notices->get_notice( __CLASS__, 'default' );
	}
}