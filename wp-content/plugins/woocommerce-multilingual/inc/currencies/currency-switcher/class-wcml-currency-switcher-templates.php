<?php

class WCML_Currency_Switcher_Templates {

	const CONFIG_FILE = 'config.json';
	const OPTION_NAME = 'wcml_currency_switcher_template_objects';

    /**
    * @var  woocommerce_wpml
    */
    private $woocommerce_wpml;

    /**
    * @var  WPML_WP_API $wp_api
    */
    private $wp_api;

    /**
     * @var string $uploads_path
     */
    private $uploads_path;

    /**
     * @var WPML_File
     */
    private $wpml_file;

    /**
     * @var array $templates Collection of WCML_CS_Template
     */
    private $templates = false;

	/**
	 * @var array $enqueued_templates
	 */
	private $enqueued_templates = array();

    /**
     * @var string $ds
     */
    private $ds = DIRECTORY_SEPARATOR;

    public function __construct(  woocommerce_wpml $woocommerce_wpml, WPML_WP_API $wp_api, WCML_File $wpml_file = null ) {

        $this->woocommerce_wpml = $woocommerce_wpml;
        $this->wp_api           = $wp_api;

        if ( ! $wpml_file ) {
            //TODO: use WPML_FILE class instead after changing requirements for WPML >= 3.6.0
            $wpml_file = new WCML_File();
        }

        $this->wpml_file = $wpml_file;
    }

    public function init_hooks() {

        add_action( 'after_setup_theme',  array( $this, 'after_setup_theme_action' ) );
        add_action( 'admin_head', array( $this, 'admin_enqueue_template_resources' ) );

        //enqueue front resources only when MC enabled
        $wcml_settings = $this->woocommerce_wpml->get_settings();
        if( $wcml_settings['enable_multi_currency'] === $this->wp_api->constant( 'WCML_MULTI_CURRENCIES_INDEPENDENT' ) ){
            add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_template_resources' ) );
        }
    }

    public function after_setup_theme_action() {
        $this->init_available_templates();
    }

    /**
     * @param string $template_slug
     *
     * @return WCML_CS_Template
     */
    public function get_template( $template_slug ) {
	    $ret = false;
        if ( array_key_exists( $template_slug, $this->templates ) ) {
            $ret = $this->templates[ $template_slug ];
        }

        return $ret;
    }

    /**
     * @return array of active WCML_CS_Templates
     */
    public function get_active_templates( $load_default = false ) {

        $templates = array();
        $wcml_settings = $this->woocommerce_wpml->get_settings();

        if ( isset( $wcml_settings['currency_switchers'] ) ) {
            foreach ( $wcml_settings['currency_switchers'] as $switcher_id => $switcher ) {
                if ( ! $this->woocommerce_wpml->cs_properties->is_currency_switcher_active( $switcher_id, $wcml_settings ) ) {
                    continue;
                }

                foreach ( $this->templates as $key => $template ) {
                    if ( $switcher['switcher_style'] === $key && ! isset( $templates[ $key ] ) ) {
                        $templates[ $key ] = $template;
                    }
                }
            }
        }

        if( !$templates && $load_default ){
            //set default template to active
            $templates['wcml-dropdown'] = $this->templates['wcml-dropdown'];
        }

        return $templates;
    }

    /**
     * @return array of template data
     */
    public function get_templates() {

        $templates = array();

        foreach( $this->templates as $key => $template ){

            $template_data = $template->get_template_data();

            if( isset( $template_data['is_core'] ) && $template_data['is_core'] ){
                $templates[ 'core' ][ $key ] = $template_data;
            }else{
                $templates[ 'custom' ][ $key ] = $template_data;
            }
        }

        return $templates;
    }

    /**
     * @return null|string
     */
    private function get_uploads_path() {
        if ( ! $this->uploads_path ) {
            $uploads = wp_upload_dir( null, false );

            if ( isset( $uploads['basedir'] ) ) {
                $this->uploads_path = $uploads['basedir'];
            }
        }

        return $this->uploads_path;
    }

    /**
     * @param string $template_path
     *
     * @return array
     */
    private function parse_template_config( $template_path ) {
        $config = array();
        $configuration_file = $template_path . $this->ds . self::CONFIG_FILE;
        if ( file_exists( $configuration_file ) ) {
            $json_content = file_get_contents( $configuration_file );
            $config       = json_decode( $json_content, true );
        }

        return $config;
    }

	private function init_available_templates() {

		$is_admin_ui_page = isset( $_GET['page'] ) && 'wpml-wcml' === $_GET['page'] && isset( $_GET['tab'] ) && 'multi-currency' === $_GET['tab'];

		if ( ! $is_admin_ui_page ) {
			$this->templates = $this->get_templates_from_transient();
		}

		if ( $this->templates === false ) {
			$templates    = array();
			$dirs_to_scan = array();

			$sub_dir = $this->ds . 'templates' . $this->ds . 'currency-switchers';

			$wcml_core_path   = WCML_PLUGIN_PATH . $sub_dir;
			$theme_path       = get_template_directory() . $this->ds . 'wpml' . $sub_dir;
			$child_theme_path = get_stylesheet_directory() . $this->ds . 'wpml' . $sub_dir;
			$uploads_path     = $this->get_uploads_path() . $this->ds . 'wpml' . $sub_dir;

			array_unshift( $dirs_to_scan, $wcml_core_path, $theme_path, $child_theme_path, $uploads_path );

			/**
			 * Filter the directories to scan
			 *
			 * @param array $dirs_to_scan
			 */
			$dirs_to_scan = apply_filters( 'wcml_cs_directories_to_scan', $dirs_to_scan );

			$templates_paths = $this->scan_template_paths( $dirs_to_scan );

			foreach ( $templates_paths as $template_path ) {
				$template_path = $this->wpml_file->fix_dir_separator( $template_path );
				if ( file_exists( $template_path . $this->ds . WCML_Currency_Switcher_Template::FILENAME ) ) {
					$tpl    = array();
					$config = $this->parse_template_config( $template_path );

					$tpl['path'] = $template_path;
					$tpl['name'] = isset( $config['name'] ) ? $config['name'] : null;
					$tpl['name'] = $this->get_unique_name( $tpl['name'], $template_path );
					$tpl['slug'] = sanitize_title_with_dashes( $tpl['name'] );
					$tpl['css']  = $this->get_files( 'css', $template_path, $config );
					$tpl['js']   = $this->get_files( 'js', $template_path, $config );

					if ( $this->is_core_template( $template_path ) ) {
						$tpl['is_core'] = true;
						$tpl['slug']    = isset( $config['slug'] ) ? $config['slug'] : $tpl['slug'];
					}

					$templates[ $tpl['slug'] ] = new WCML_Currency_Switcher_Template( $this->woocommerce_wpml, $tpl );
				}
			}

			update_option( self::OPTION_NAME, $templates );

			$this->set_templates( $templates );
		}

	}

	private function get_templates_from_transient() {
		$templates = get_option( self::OPTION_NAME );
		if ( $templates && $this->are_template_paths_valid( $templates ) ) {
			return $templates;
		}
		return false;
	}


	private function are_template_paths_valid( $templates ) {
		$paths_are_valid = true;
		foreach ( $templates as $template ) {
			if (
				$template instanceof WCML_Currency_Switcher_Template &&
				! $template->is_path_valid()
			) {
				$paths_are_valid = false;
				break;
			}
		}
		return $paths_are_valid;
	}

    /**
     * @param array $dirs_to_scan
     *
     * @return array
     */
    private function scan_template_paths( $dirs_to_scan ) {
        $templates_paths = array();

        foreach ( $dirs_to_scan as $dir ) {
            if ( !is_dir( $dir ) ) {
                continue;
            }
            $files = scandir( $dir );
            $files = array_diff( $files, array( '..', '.' ) );
            if ( count( $files ) > 0 ) {
                foreach ( $files as $file ) {
                    $template_path = $dir . '/' . $file;
                    if ( is_dir( $template_path )
                        && file_exists( $template_path . $this->ds . WCML_Currency_Switcher_Template::FILENAME )
                        && file_exists( $template_path . $this->ds . self::CONFIG_FILE )
                    ) {
                        $templates_paths[] = $template_path;
                    }
                }
            }
        }

        return $templates_paths;
    }


    /**
     * @param string $ext
     * @param string $template_path
     * @param array $config
     *
     * @return array|null
     */
    private function get_files( $ext, $template_path, $config ) {
        $resources = array();

        if( isset( $config[ $ext ] ) ) {
            $config[ $ext ] = is_array( $config[ $ext ] ) ? $config[ $ext ] : array( $config[ $ext ] );
            foreach ( $config[ $ext ] as $file ) {
                $file = untrailingslashit( $template_path ) .$this->ds . $file;
                $resources[] = $this->wpml_file->get_uri_from_path( $file );
            }
        } else {
            $search_path = $template_path . $this->ds . '*.' . $ext;
            if ( glob( $search_path ) ) {
                foreach ( glob( $search_path ) as $file ) {
                    $resources[] = $this->wpml_file->get_uri_from_path( $file );
                }
            }
        }

        return $resources;
    }

    /**
     * @param string $path
     *
     * @return bool
     */
    private function is_core_template( $path ) {
        return strpos( $path, WCML_PLUGIN_PATH ) === 0;
    }

    /**
     * @param mixed|string|null $name
     * @param string $path
     *
     * @return string
     */
    private function get_unique_name( $name, $path ) {
        if ( is_null( $name ) ) {
            $name = basename( $path );
        }

        if ( strpos( $path, $this->wpml_file->fix_dir_separator( get_template_directory() ) ) === 0 ) {
            $theme = wp_get_theme();
            $name  = $theme . ' - ' . $name;
        } elseif ( strpos( $path, $this->wpml_file->fix_dir_separator( $this->get_uploads_path() ) ) === 0 ) {
            $name = __( 'Uploads', 'woocommerce-multilingual' ) . ' - ' . $name;
        } elseif (
            strpos( $path, $this->wpml_file->fix_dir_separator( WP_PLUGIN_DIR ) ) === 0
            && ! $this->is_core_template( $path )
        ) {
            $plugin_dir = $this->wpml_file->fix_dir_separator( WP_PLUGIN_DIR );
            $plugin_dir = preg_replace( '#' . preg_quote( $plugin_dir ) . '#' , '', $path, 1 );
            $plugin_dir = ltrim( $plugin_dir, $this->ds );
            $plugin_dir = explode( $this->ds, $plugin_dir );

            if ( isset( $plugin_dir[0] ) ) {
                $require = ABSPATH . 'wp-admin' . $this->ds . 'includes' . $this->ds . 'plugin.php';
                require_once( $require );
                foreach ( get_plugins() as $slug => $plugin ) {
                    if ( strpos( $slug, $plugin_dir[0] ) === 0 ) {
                        $name = $plugin['Name'] . ' - ' . $name;
                        break;
                    }
                }
            } else {
                $name = substr( md5( $path ), 0, 8 ) . ' - ' . $name;
            }
        }

        return $name;
    }

    public function enqueue_template_resources( $templates = false ) {

        if( !$templates ){
            $templates =  $this->get_active_templates( true );
        }

        $wcml_settings = $this->woocommerce_wpml->get_settings();

        foreach ( $templates as $slug => $template ) {

            $this->enqueue_template_assets( $slug, $template );

            if ( $template->has_styles() ) {
                $style_handler = $template->get_inline_style_handler();
            }
        }

        if( $templates ){
            if( isset( $wcml_settings[ 'currency_switchers' ] ) ){
                foreach( $wcml_settings[ 'currency_switchers' ] as $key => $switcher_data ){

                    $switcher_template = $switcher_data['switcher_style'];

                    if ( ! isset( $templates[ $switcher_template ] ) ) {
                        continue;
                    }

                    $css = $this->get_color_picket_css( $key, $switcher_data );
                    $template = $templates[ $switcher_template ];

                    if ( $template->has_styles() ) {
                        wp_add_inline_style( $template->get_inline_style_handler(), $css );
                    }else{
                        echo $this->get_inline_style( $key, $switcher_template, $css );
                    }
                }
            }

            if ( ! empty( $wcml_settings['currency_switcher_additional_css'] ) ) {
                $additional_css = $this->sanitize_css( $wcml_settings['currency_switcher_additional_css'] );

                if( !empty( $style_handler ) ){
                    wp_add_inline_style( $style_handler, $additional_css );
                }else{
                    echo $this->get_inline_style( 'currency_switcher', 'additional_css', $additional_css );
                }
            }
        }
    }

	/**
	 * @param string $slug
	 * @param WCML_Currency_Switcher_Template $template
	 *
	 */
    public function enqueue_template_assets( $slug, $template ){

	    $this->enqueued_templates[] = $slug;

	    foreach ( $template->get_scripts() as $k => $url ) {
		    wp_enqueue_script( $template->get_resource_handler( $k ), $url, array(), WCML_VERSION );
	    }

	    foreach ( $template->get_styles() as $k => $url ) {
		    wp_enqueue_style( $template->get_resource_handler( $k ), $url, array(), WCML_VERSION );
	    }

    }


	/**
	 * @param $slug
	 * @param $template
	 */
	public function maybe_late_enqueue_template( $slug, $template ) {
		if ( ! in_array( $slug, $this->enqueued_templates ) ) {
			$this->enqueue_template_assets( $slug, $template );
		}
	}


    /**
     * @param string $css
     *
     * @return string
     */
    private function sanitize_css( $css ) {
        $css = wp_strip_all_tags( $css );
        $css = preg_replace('/\s+/S', " ", trim( $css ) );
        return $css;
    }

    public function admin_enqueue_template_resources(){
        if( isset( $_GET['page'] ) && $_GET['page'] == 'wpml-wcml' && isset( $_GET['tab'] ) && $_GET['tab'] == 'multi-currency' ){
            $this->enqueue_template_resources( $this->templates );
        }
    }

    public function get_color_picket_css( $switcher_id, $switcher_data ){

        $css = '';
        $wrapper_class = '.'.$switcher_id.'.'.$switcher_data[ 'switcher_style' ];

        if ( $switcher_data[ 'color_scheme' ][ 'border_normal' ] ) {
            $css .= "$wrapper_class, $wrapper_class li, $wrapper_class li li{";
            $css .= "border-color:". $switcher_data[ 'color_scheme' ][ 'border_normal' ] ." ;";
            $css .= "}";
        }

        if ( $switcher_data[ 'color_scheme' ][ 'font_other_normal' ] || $switcher_data[ 'color_scheme' ][ 'background_other_normal' ] ) {
            $css .= "$wrapper_class li>a {";
            $css .= $switcher_data[ 'color_scheme' ][ 'font_other_normal' ] ? "color:". $switcher_data[ 'color_scheme' ][ 'font_other_normal' ] .";" : '';
            $css .= $switcher_data[ 'color_scheme' ][ 'background_other_normal' ] ? "background-color:". $switcher_data[ 'color_scheme' ][ 'background_other_normal' ] .";" : '';
            $css .= "}";
        }

        if ( $switcher_data[ 'color_scheme' ][ 'font_other_hover' ] || $switcher_data[ 'color_scheme' ][ 'background_other_hover' ] ) {
            $css .= "$wrapper_class li:hover>a, $wrapper_class li:focus>a {";
            $css .= $switcher_data[ 'color_scheme' ][ 'font_other_hover' ] ? "color:". $switcher_data[ 'color_scheme' ][ 'font_other_hover' ] .";" : '';
            $css .= $switcher_data[ 'color_scheme' ][ 'background_other_hover' ] ? "background-color:". $switcher_data[ 'color_scheme' ][ 'background_other_hover' ] .";" : '';
            $css .= "}";
        }

        if ( $switcher_data[ 'color_scheme' ][ 'font_current_normal' ] || $switcher_data[ 'color_scheme' ][ 'background_current_normal' ] ) {
            $css .= "$wrapper_class .wcml-cs-active-currency>a {";
            $css .= $switcher_data[ 'color_scheme' ][ 'font_current_normal' ] ? "color:". $switcher_data[ 'color_scheme' ][ 'font_current_normal' ] .";" : '';
            $css .= $switcher_data[ 'color_scheme' ][ 'background_current_normal' ] ? "background-color:". $switcher_data[ 'color_scheme' ][ 'background_current_normal' ] .";" : '';
            $css .= "}";
        }

        if ( $switcher_data[ 'color_scheme' ][ 'font_current_hover' ]|| $switcher_data[ 'color_scheme' ][ 'background_current_hover' ] ) {
            $css .= "$wrapper_class .wcml-cs-active-currency:hover>a, $wrapper_class .wcml-cs-active-currency:focus>a {";
            $css .= $switcher_data[ 'color_scheme' ][ 'font_current_hover' ] ? "color:". $switcher_data[ 'color_scheme' ][ 'font_current_hover' ] .";" : '';
            $css .= $switcher_data[ 'color_scheme' ][ 'background_current_hover' ] ? "background-color:". $switcher_data[ 'color_scheme' ][ 'background_current_hover' ] .";" : '';
            $css .= "}";
        }

        return $css;
    }


    public function get_inline_style( $switcher_id, $switcher_template, $css ) {
        $style_id = 'wcml-cs-inline-styles-' . $switcher_id.'-'.$switcher_template;
        return '<style type="text/css" id="' . $style_id . '">' . $css . '</style>' . PHP_EOL;
    }

	public function set_templates( $templates ) {
    	$this->templates = $templates;
	}

    public function check_is_active( $template ){
        $is_active = false;

        $active_templates = $this->get_active_templates( true );

        foreach( $active_templates as $template_key => $active_template ){
            if ( $template === $template_key ){
                $is_active = true;
                break;
            }
        }

        return $is_active;

    }

    public function get_first_active( ){
        return current( array_keys( $this->get_active_templates( true ) ) );

    }
}
