<?php
/**
 * Fontpack
 *
 */

final class OE_Icon_Picker_Fontpack {

	/**
	 * OE_Icon_Picker_Fontpack singleton
	 *
	 */
	protected static $instance;

	/**
	 * Fontpack directory path
	 *
	 */
	protected $dir;

	/**
	 * Fontpack directory url path
	 *
	 */
	protected $url;

	/**
	 * Error messages
	 *
	 */
	protected $messages = array();

	/**
	 * Icon packs
	 *
	 */
	protected $packs = array();

	/**
	 * Get instance
	 *
	 */
	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Getter magic
	 *
	 */
	public function __get( $name ) {
		if ( isset( $this->$name ) ) {
			return $this->$name;
		}

		return null;
	}

	/**
	 * Setter magic
	 *
	 */
	public function __isset( $name ) {
		return isset( $this->$name );
	}

	/**
	 * Constructor
	 *
	 */
	protected function __construct() {
		/**
		 * Allow different system path for fontpacks
		 *
		 */
		$this->dir = apply_filters( 'oe_icon_picker_fontpacks_dir_path', WP_CONTENT_DIR . '/fontpacks' );

		if ( ! is_readable( $this->dir ) ) {
			return;
		}

		/**
		 * Allow different URL path for fontpacks
		 *
		 */
		$this->url = apply_filters( 'oe_icon_picker_fontpacks_dir_url', WP_CONTENT_URL . '/fontpacks' );

		$this->messages = array(
			'no_config'    => __( 'Icon Picker: %1$s was not found in %2$s.', 'ocean-extra' ),
			'config_error' => __( 'Icon Picker: %s contains an error or more.', 'ocean-extra' ),
			'invalid'      => __( 'Icon Picker: %1$s is not set or invalid in %2$s.', 'ocean-extra' ),
			'duplicate'    => __( 'Icon Picker: %1$s is already registered. Please check your font pack config file: %2$s.', 'ocean-extra' ),
		);

		$this->collect_packs();
		$this->register_packs();
	}

	/**
	 * Collect icon packs
	 *
	 */
	protected function collect_packs() {
		$iterator = new DirectoryIterator( $this->dir );

		foreach ( $iterator as $pack_dir ) {
			if ( $pack_dir->isDot() || ! $pack_dir->isDir() || ! $pack_dir->isReadable() ) {
				continue;
			}

			$pack_dirname = $pack_dir->getFilename();
			$pack_data    = $this->get_pack_data( $pack_dir );

			if ( ! empty( $pack_data ) ) {
				$this->packs[ $pack_dirname ] = $pack_data;
			}
		}
	}

	/**
	 * Register icon packs
	 *
	 */
	protected function register_packs() {
		if ( empty( $this->packs ) ) {
			return;
		}

		$icon_picker = OE_Icon_Selector::instance();
		require_once "{$icon_picker->dir}/includes/types/fontello.php";

		foreach ( $this->packs as $pack_data ) {
			$icon_picker->registry->add( new OE_Icon_Picker_Type_Fontello( $pack_data ) );
		}
	}

	/**
	 * Get icon pack data
	 *
	 */
	protected function get_pack_data( DirectoryIterator $pack_dir ) {
		$pack_dirname  = $pack_dir->getFilename();
		$pack_path     = $pack_dir->getPathname();
		$cache_id      = "icon_picker_fontpack_{$pack_dirname}";
		$cache_data    = get_transient( $cache_id );
		$config_file   = "{$pack_path}/config.json";

		if ( false !== $cache_data && $cache_data['version'] === $pack_dir->getMTime() ) {
			return $cache_data;
		}

		// Make sure the config file exists and is readable.
		if ( ! is_readable( $config_file ) ) {
			trigger_error(
				sprintf(
					esc_html( $this->messages['no_config'] ),
					'<code>config.json</code>',
					sprintf( '<code>%s</code>', esc_html( $pack_path ) )
				)
			);

			return false;
		}

		$config = json_decode( file_get_contents( $config_file ), true );
		$errors = json_last_error();

		if ( ! empty( $errors ) ) {
			trigger_error(
				sprintf(
					esc_html( $this->messages['config_error'] ),
					sprintf( '<code>%s/config.json</code>', esc_html( $pack_path ) )
				)
			);

			return false;
		}

		$keys   = array( 'name', 'glyphs', 'css_prefix_text' );
		$items  = array();

		// Check each required config.
		foreach ( $keys as $key ) {
			if ( empty( $config[ $key ] ) ) {
				trigger_error(
					sprintf(
						esc_html( $this->messages['invalid'] ),
						sprintf( '<code><em>%s</em></code>', esc_html( $key ) ),
						esc_html( $config_file )
					)
				);

				return false;
			}
		}

		// Bail if no glyphs found.
		if ( ! is_array( $config['glyphs'] ) || empty( $config['glyphs'] ) ) {
			return false;
		}

		foreach ( $config['glyphs'] as $glyph ) {
			if ( ! empty( $glyph['css'] ) ) {
				$items[] = array(
					'id'   => $config['css_prefix_text'] . $glyph['css'],
					'name' => $glyph['css'],
				);
			}
		}

		if ( empty( $items ) ) {
			return false;
		}

		$pack_data = array(
			'id'             => "pack-{$config['name']}",
			'name'           => sprintf( __( 'Pack: %s', 'ocean-extra' ), $config['name'] ),
			'version'        => $pack_dir->getMTime(),
			'items'          => $items,
			'stylesheet_uri' => "{$this->url}/{$pack_dirname}/css/{$config['name']}.css",
			'dir'            => "{$this->dir}/{$pack_dirname}",
			'url'            => "{$this->url}/{$pack_dirname}",
		);

		set_transient( $cache_id, $pack_data, DAY_IN_SECONDS );

		return $pack_data;
	}
}