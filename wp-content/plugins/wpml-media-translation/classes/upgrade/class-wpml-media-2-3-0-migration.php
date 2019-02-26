<?php

class WPML_Media_2_3_0_Migration {

	const FLAG = 'wpml_media_2_3_migration';
	const BATCH_SIZE = 200;

	const MAX_BATCH_REQUEST_TIME = 5;

	/**
	 * @var wpdb
	 */
	private $wpdb;
	/**
	 * @var SitePress
	 */
	private $sitepress;

	/**
	 * WPML_Media_2_3_0_Migration constructor.
	 *
	 * @param wpdb $wpdb
	 * @param SitePress $sitepress
	 */
	public function __construct( wpdb $wpdb, SitePress $sitepress ) {
		$this->wpdb      = $wpdb;
		$this->sitepress = $sitepress;
	}

	public static function migration_complete() {
		return WPML_Media::get_setting( self::FLAG );
	}

	private function mark_migration_complete() {
		return WPML_Media::update_setting( self::FLAG, 1 );
	}

	public function is_required() {
		if ( $this->wpdb->get_var( "SELECT COUNT(ID) FROM {$this->wpdb->posts} WHERE post_type='attachment'" ) ) {
			return true;
		}
		self::mark_migration_complete();

		return false;
	}

	public function add_hooks() {
		add_filter( 'wpml_media_menu_overrides', array( $this, 'override_default_menu' ) );
		add_action( 'wp_ajax_wpml_media_2_3_0_upgrade', array( $this, 'run_upgrade' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_js' ) );
	}

	public function override_default_menu( $menu_elements ) {
		$menu_elements[] = array( $this, 'render_menu' );
		return $menu_elements;
	}

	public function maybe_show_admin_notice() {
		if ( is_admin() && ! $this->is_wpml_media_screen() ) {
			add_action( 'admin_notices', array( $this, 'render_menu' ) );
		}
	}

	private function is_wpml_media_screen() {
		return isset( $_GET['page'] ) && $_GET['page'] === 'wpml-media';
	}

	public function enqueue_js() {
		$wpml_media_url = $this->sitepress->get_wp_api()->constant( 'WPML_MEDIA_URL' );
		wp_enqueue_script( 'wpml-media-2-3-0-upgrade', $wpml_media_url . '/res/js/upgrade/upgrade-2-3-0.js', array( 'jquery' ), false, true );
	}

	public function render_menu() {

		if( $this->is_wpml_media_screen() ): ?>
		<div class="wrap wrap-wpml-media-upgrade">
			<h2><?php esc_html_e( 'Upgrade required', 'wpml-media' ) ?></h2>
		<?php endif; ?>
			<div class="notice notice-warning" id="wpml-media-2-3-0-update" style="padding-bottom:8px">
				<p>
				<?php printf( esc_html__( 'The %sWPML Media%s database needs updating. Please run the updater and leave the tab open until it completes.', 'wpml-media' ),
					'<strong>', '</strong>' ); ?>
				</p>
				<input type="button" class="button-primary alignright" value="<?php echo esc_attr_x( 'Update', 'Update button label', 'wpml-media' ); ?>" />
				<input type="hidden" name="nonce" value="<?php echo wp_create_nonce( 'wpml-media-2-3-0-update' ); ?>" />
				<span class="spinner"></span>
				<p class="alignleft status description"></p><br clear="all" />
			</div>
		<?php if( $this->is_wpml_media_screen() ): ?>
		</div>
		<?php endif;

	}

	private function reset_new_content_settings() {
		$wpml_media_settings = get_option( '_wpml_media' );
		// reset (will not remove since it's used by WCML)
		$wpml_media_settings['new_content_settings']['always_translate_media'] = 0;
		$wpml_media_settings['new_content_settings']['duplicate_media']        = 0;
		$wpml_media_settings['new_content_settings']['duplicate_featured']     = 0;
		update_option( '_wpml_media', $wpml_media_settings );
	}

	public function run_upgrade() {

		if ( isset( $_POST['nonce'] ) && ( $_POST['nonce'] === wp_create_nonce( 'wpml-media-2-3-0-update' ) ) ) {

			$step = isset( $_POST['step'] ) ? $_POST['step'] : '';

			if ( 'reset-new-content-settings' === $step ) {
				$this->reset_new_content_settings();
				wp_send_json_success(
					array( 'status' => esc_html__( 'Reset new content duplication settings', 'wpml-media' ), )
				);
			} elseif ( 'migrate-attachments' === $step ) {
				$offset = isset( $_POST['offset'] ) ? (int) $_POST['offset'] : 0;

				$batch_size = $this->get_dynamic_batch_size( $_POST );

				$left = $this->migrate_attachments( $offset, $batch_size );

				if ( $left ) {
					$status   = sprintf(
						esc_html__( 'Updating attachments translation status: %d remaining.', 'wpml-media' ),
						$left );
					$continue = 1;
					$offset   += $batch_size;
				} else {
					$this->mark_migration_complete();
					$status   = esc_html__( 'Update complete!', 'wpml-media' );
					$continue = 0;
					$offset   = 0;
				}

				wp_send_json_success(
					array(
						'status'            => $status,
						'goon'              => $continue,
						'offset'            => $offset,
						'timestamp'         => microtime( true )
					)
				);

			} else {
				wp_send_json_error( array( 'error' => 'Invalid step' ) );
			}

		} else {
			wp_send_json_error( array( 'error' => 'Invalid nonce' ) );
		}

	}

	private function migrate_attachments( $offset = 0, $batch_size = self::BATCH_SIZE ) {

		$sql = "SELECT SQL_CALC_FOUND_ROWS  p.ID
				FROM {$this->wpdb->posts} p
				  JOIN {$this->wpdb->prefix}icl_translations t
				  ON t.element_id = p.ID AND p.post_type='attachment'
				WHERE t.source_language_code IS NULL
				LIMIT %d, %d";

		$sql_prepared = $this->wpdb->prepare( $sql, $offset, $batch_size );

		$original_attachments = $this->wpdb->get_results( $sql_prepared );

		$total_attachments = $this->wpdb->get_var( 'SELECT FOUND_ROWS() ' );

		if ( $original_attachments ) {
			foreach ( $original_attachments as $attachment ) {

				$post_element = new WPML_Post_Element( $attachment->ID, $this->sitepress );
				$translations = $post_element->get_translations();

				$media_file = get_post_meta( $attachment->ID, '_wp_attached_file', true );

				foreach ( $translations as $translation ) {
					if ( (int) $attachment->ID !== $translation->get_id() ) {
						$media_translation_status = WPML_Media_Translation_Status::NOT_TRANSLATED;
						$media_file_translation   = get_post_meta( $translation->get_id(), '_wp_attached_file', true );
						if ( $media_file_translation !== $media_file ) {
							$media_translation_status = WPML_Media_Translation_Status::TRANSLATED;
						}
						update_post_meta(
							$attachment->ID,
							WPML_Media_Translation_Status::STATUS_PREFIX . $translation->get_language_code(),
							$media_translation_status
						);
					}
				}

			}
		}

		$left = max( 0, $total_attachments - $offset );

		return $left;
	}

	private function get_dynamic_batch_size( $request ){
		$batch_size_factor = isset( $request['batch_size_factor'] ) ? (int) $request['batch_size_factor'] : 1;
		if ( ! empty( $request['timestamp'] ) ) {
			$elapsed_time = microtime( true ) - (float) $request['timestamp'];

			if ( $elapsed_time < self::MAX_BATCH_REQUEST_TIME ) {
				$batch_size_factor ++;
			} else {
				$batch_size_factor = max( 1, $batch_size_factor - 1 );
			}
		}
		return self::BATCH_SIZE * $batch_size_factor;
	}
}