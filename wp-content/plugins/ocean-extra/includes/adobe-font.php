<?php


class OceanWP_Adobe_Font {

	public $active_status;
	private static $_instance = null;
	private static $font_base = 'oe-adobe-fonts';
	const REMOTE_URL_BASE     = 'https://use.typekit.net/%s.css';
	const OE_ADOBE_FONTS_VER  = '1.0.0';

	/**
	 * Main OceanWP_Adobe_Font Instance
	 *
	 * @static
	 * @see OceanWP_Adobe_Font()
	 * @return Main OceanWP_Adobe_Font instance
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	} // End instance()

	/**
	 * Constructor
	 */
	public function __construct() {
		$enable_module = $this->get_active_status();
		if ( empty( $enable_module ) ) {
			return;
		}

		$enable_elementor_mode  = get_option( 'owp_adobe_fonts_integration_enable_elementor', 0 );
		$enable_customizer_mode = get_option( 'owp_adobe_fonts_integration_enable_customizer', 0 );

		if ( ! empty( $enable_elementor_mode ) || ! empty( $enable_customizer_mode ) ) {
			add_action( 'wp_enqueue_scripts', array( $this, 'adobe_font_css' ) );
		}

		if ( ! empty( $enable_elementor_mode ) ) {
			// Elementor page builder.
			add_filter( 'elementor/fonts/groups', array( $this, 'elementor_group' ) );
			add_filter( 'elementor/fonts/additional_fonts', array( $this, 'add_elementor_fonts' ) );
		}

		if ( ! empty( $enable_customizer_mode ) ) {
			add_action( 'ocean_customizer_fonts', array( $this, 'render_customizer_group' ) );
			add_action( 'enqueue_block_editor_assets', array( $this, 'adobe_font_css' ) );
		}
	}

	/**
	 * Cloning is forbidden.
	 */
	public function __clone() {
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?' ), '1.0.0' );
	}

	/**
	 * Deserializing instances of this class is forbidden.
	 */
	public function __wakeup() {
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?' ), '1.0.0' );
	}

	public function get_project_id() {
		return get_option( 'owp_adobe_fonts_integration_project_id' );
	}

	public function get_active_status() {
		return get_option( 'owp_adobe_fonts_integration' );
	}

	public function check_project_id() {
		try {
			$project_id = get_option( 'owp_adobe_fonts_integration_project_id' );

			if ( empty( $project_id ) ) {
				throw new Exception( 'Project ID can not be empty.' );
			}

			$adobe_uri = 'https://typekit.com/api/v1/json/kits/' . $project_id . '/published';
			$response  = wp_remote_get(
				$adobe_uri,
				array(
					'timeout' => '30',
				)
			);

			if ( is_wp_error( $response ) || wp_remote_retrieve_response_code( $response ) !== 200 ) {
				throw new Exception( 'Project ID is invalid.' );
			}

			$this->save( $project_id, $response );

			return array(
				'status'  => 'success',
				'message' => 'Project ID is valid',
			);
		} catch ( Exception $e ) {
			delete_option( 'oe-adobe-fonts' );

			return array(
				'status'  => 'failed',
				'message' => $e->getMessage(),
			);
		}
	}

	public function save( $project_id, $response_data ) {
		$project_info = array();

		$data     = json_decode( wp_remote_retrieve_body( $response_data ), true );
		$families = $data['kit']['families'];

		foreach ( $families as $family ) {

			$family_name = str_replace( ' ', '-', $family['name'] );

			$project_info[ $family_name ] = array(
				'family'   => $family_name,
				'fallback' => str_replace( '"', '', $family['css_stack'] ),
				'weights'  => array(),
			);

			foreach ( $family['variations'] as $variation ) {

				$variations = str_split( $variation );

				switch ( $variations[0] ) {
					case 'n':
						$style = 'normal';
						break;
					default:
						$style = 'normal';
						break;
				}

				$weight = $variations[1] . '00';

				if ( ! in_array( $weight, $project_info[ $family_name ]['weights'] ) ) {
					$project_info[ $family_name ]['weights'][] = $weight;
				}
			}

			$project_info[ $family_name ]['slug']      = $family['slug'];
			$project_info[ $family_name ]['css_names'] = $family['css_names'];
		}

		$options                        = array();
		$options['oe-adobe-font-id']    = ! empty( $project_id ) ? sanitize_text_field( $project_id ) : '';
		$options['oe-adobe-fonts-list'] = isset( $project_id ) ? $project_info : '';

		update_option( 'oe-adobe-fonts', $options );
	}

	/**
	 * Add Custom Font group to elementor font list.
	 */
	public function elementor_group( $font_groups ) {
		$new_group[ self::$font_base ] = __( 'OE Adobe Fonts', 'ocean-extra' );
		$font_groups                   = $new_group + $font_groups;
		return $font_groups;
	}

	public function render_customizer_group() {
		$fonts_list  = get_option( 'oe-adobe-fonts' );
		$all_fonts    = $fonts_list['oe-adobe-fonts-list'];
		$custom_fonts = array();
		if ( ! empty( $all_fonts ) ) {
			?>
			<optgroup label="<?php esc_attr_e( 'OE Adobe Fonts', 'ocean-extra' ); ?>">
				<?php
				foreach ( $all_fonts as $font_family_name => $fonts_url ) {
					$font_slug = isset( $fonts_url['slug'] ) ? $fonts_url['slug'] : '';
					$font_css  = isset( $fonts_url['css_names'][0] ) ? $fonts_url['css_names'][0] : $font_slug;
					// $custom_fonts[$font_css] = self::$font_base;
					?>
					<option value="<?php echo esc_attr( $font_css ); ?>"><?php echo esc_html( $font_slug ); ?></option>
					<?php
				}
				?>
			</optgroup>
			<?php
		}

		if ( $google_fonts = oceanwp_google_fonts_array() ) {
			?>

			<?php
			// Loop through font options and add to select.
			foreach ( $google_fonts as $font ) {
				?>

			<?php } ?>

			<?php
		}
	}

	/**
	 * Add Custom Fonts to the Elementor.
	 */
	public function add_elementor_fonts( $fonts ) {
		$fonts_list  = get_option( 'oe-adobe-fonts' );
		$all_fonts    = $fonts_list['oe-adobe-fonts-list'];
		$custom_fonts = array();
		if ( ! empty( $all_fonts ) ) {
			foreach ( $all_fonts as $font_family_name => $fonts_url ) {
				$font_slug                 = isset( $fonts_url['slug'] ) ? $fonts_url['slug'] : '';
				$font_css                  = isset( $fonts_url['css_names'][0] ) ? $fonts_url['css_names'][0] : $font_slug;
				$custom_fonts[ $font_css ] = self::$font_base;
			}
		}

		return array_merge( $fonts, $custom_fonts );
	}

	public function adobe_font_css() {
		$adobe_remote_url = $this->get_adobe_url();
		if ( false !== $adobe_remote_url ) {
			wp_enqueue_style( 'oe-adobe-css', $adobe_remote_url, array(), self::OE_ADOBE_FONTS_VER );
		}
	}

	private function get_adobe_url() {
		$adode_fonts_info = get_option( 'oe-adobe-fonts' );
		if ( empty( $adode_fonts_info['oe-adobe-fonts-list'] ) ) {
			return false;
		}

		return sprintf( self::REMOTE_URL_BASE, $adode_fonts_info['oe-adobe-font-id'] );
	}
}

/**
 * Returns the main instance of OceanWP_Adobe_Font to prevent the need to use globals.
 *
 * @return object OceanWP_Adobe_Font
 */
function OceanWP_Adobe_Font() {
	return OceanWP_Adobe_Font::instance();
} // End OceanWP_Adobe_Font()

OceanWP_Adobe_Font();
