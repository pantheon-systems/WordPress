<?php
/**
 * Templates class.
 *
 * @since 1.25
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'Strong_Templates' ) ) :

class Strong_Templates {

	/**
	 * @var array
	 */
	public $templates;

	public function __construct() {
		$this->templates = $this->find_templates();
	}

	/**
	 * @param null $type
	 *
	 * @return array
	 */
	public function find_templates( $type = null ) {

		$search = array(
			'child_theme'  => array(
				'source' => __( 'Child Theme', 'strong-testimonials' ),
				'path'   => get_stylesheet_directory() . '/' . WPMTST,
				'uri'    => get_stylesheet_directory_uri() . '/' . WPMTST,
				'order'  => 2,
			),
			'parent_theme' => array(
				'source' => __( 'Parent Theme', 'strong-testimonials' ),
				'path'   => get_template_directory() . '/' . WPMTST,
				'uri'    => get_template_directory_uri() . '/' . WPMTST,
				'order'  => 3,
			),
			'plugin'       => array(
				'source' => __( 'Plugin', 'strong-testimonials' ),
				'path'   => WPMTST_TPL,
				'uri'    => WPMTST_TPL_URI,
				'order'  => 4,
			),
		);

		/**
		 * Filter the search paths.
		 */
		$search = apply_filters( 'wpmtst_template_search_paths', $search );

		/**
		 * Insert order if necessary so custom templates appear first.
		 *
		 * @since 2.22
		 */
		foreach ( $search as $key => $where ) {
			if ( ! isset( $where['source'] ) ) {
				$search[ $key ]['source'] = __( 'Custom', 'strong-testimonials' );
			}
			if ( ! isset( $where['order'] ) ) {
				$search[ $key ]['order'] = 1;
			}
		}

		uasort( $search, array( $this, 'sort_array_by_search_order' ) );

		$files = array();
		foreach ( $search as $key => $bases ) {
			$new_files = $this->scandir_top( $bases['path'], $bases['uri'], $type );
			if ( is_array( $new_files ) && $new_files ) {
				//uasort( $new_files, array( $this, 'sort_array_by_name' ) );
				uasort( $new_files, array( $this, 'sort_array_by_order' ) );
				$files[ $bases['source'] ] = $new_files;
			}
		}

		// Filter the list of found templates
		$files = array_filter( apply_filters( 'wpmtst_templates_found', array_filter( $files ) ) );

		return $files;
	}

	/**
	 * @param string $name
	 *
	 * @return array|bool
	 */
	public function get_template_by_name( $name = '' ) {
		foreach ( $this->templates as $source => $source_templates ) {
			foreach ( $source_templates as $key => $template ) {
				if ( $key == $name ) {
					return $template;
				}
			}
		}

		return false;
	}

	/**
	 * @param null $types
	 *
	 * @return array
	 */
	public function get_templates( $types = null ) {
		return $this->get_templates_by_type( $types );
	}

	/**
	 * @param null $types
	 *
	 * @return array
	 */
	public function get_templates_by_type( $types = null ) {
		if ( ! $types ) {
			return $this->templates;
		}

		$types    = (array) $types;
		$filtered = array();

		foreach ( $this->templates as $source => $source_templates ) {
			foreach ( $source_templates as $key => $template ) {
				if ( isset( $template['config']['type'] ) ) {
					if ( in_array( $template['config']['type'], $types ) ) {
					$filtered[ $source ][ $key ] = $template;
				}
				} else {
					$key_parts = explode( ':', $key );
					$type = $key_parts[1];
					if ( 'content' == $type ) {
						$type = 'display';
					}
					if ( in_array( $type, $types ) ) {
						$filtered[ $source ][ $key] = $template;
					}
				}
			}
		}

		return array_filter( $filtered );
	}

	/**
	 * Return list of templates by key.
	 *
	 * @return array
	 */
	public function get_template_keys() {
		$template_keys = array();
		foreach ( $this->templates as $source => $source_templates ) {
			$template_keys = array_merge( $template_keys, array_keys( $source_templates ) );
		}

		return $template_keys;
	}

	/**
	 * Get template attribute.
	 *
	 * @param           $atts
	 * @param string    $part
	 * @param bool|true $use_default
	 *
	 * @return string
	 */
	public function get_template_attr( $atts, $part = 'template', $use_default = true ) {
		// Build a list of potential template part names.
		$template_search = array();

		// [1]
		/*
		 * Divi Builder compatibility. Everybody has to be special.
		 * @since 2.22.0
		 * TODO Abstract this.
		 */
		if ( 'stylesheet' == $part ) {
			if ( isset( $atts['divi_builder'] ) && $atts['divi_builder'] && wpmtst_divi_builder_active() ) {
				$template_search[] = $atts['template'] .= '-divi';
			}
		}

		// [2]
		if ( isset( $atts['template'] ) ) {
			$template_search[] = $atts['template'];
		}

		// [3]
		if ( $use_default ) {
			$template_search[] = apply_filters( 'wpmtst_default_template', 'default', $atts );
		}

		// Search list of already found template files. Stop at first match.

		$template_info = false;
		foreach ( $template_search as $template_key ) {
			foreach ( $this->templates as $source => $source_templates ) {
				if ( isset( $source_templates[ $template_key ] ) ) {
					$template_info = $source_templates[ $template_key ];
					break 2;
				}
			}
		}

		// Return the requested part

		if ( $template_info && isset( $template_info[ $part ] ) && $template_info[ $part ] ) {
			return $template_info[ $part ];
		}

		return '';
	}

	/**
	 * Get template attribute.
	 *
	 * @param           $atts
	 * @param string    $part
	 * @param bool|true $use_default
	 *
	 * @return string
	 */
	public function get_template_config( $atts, $part = 'name', $use_default = true ) {
		// Build a list of potential template part names.
		$template_search = array();

		// [1]
		/*
		 * Divi Builder compatibility. Everybody has to be special.
		 * @since 2.22.0
		 * TODO Abstract this.
		 */
		if ( 'stylesheet' == $part ) {
			if ( isset( $atts['divi_builder'] ) && $atts['divi_builder'] && wpmtst_divi_builder_active() ) {
				$template_search[] = $atts['template'] .= '-divi';
			}
		}

		// [2]
		if ( isset( $atts['template'] ) ) {
			$template_search[] = $atts['template'];
		}

		// [3]
		if ( $use_default ) {
			$template_search[] = apply_filters( 'wpmtst_default_template', 'default', $atts );
		}

		// Search list of already found template files. Stop at first match.

		$template_info = false;
		foreach ( $template_search as $template_key ) {
			foreach ( $this->templates as $source => $source_templates ) {
				if ( isset( $source_templates[ $template_key ] ) ) {
					$template_info = $source_templates[ $template_key ];
					break 2;
				}
			}
		}

		// Return the requested part (name, template, stylesheet,etc.)

		if ( $template_info && isset( $template_info['config'][ $part ] ) && $template_info['config'][ $part ] ) {
			return $template_info['config'][ $part ];
		}

		return '';
	}

	/**
	 * @param $path
	 * @param $uri
	 * @param $type
	 *
	 * @return array|bool
	 */
	public function scandir_top( $path, $uri, $type ) {
		if ( ! is_dir( $path ) ) {
			return false;
		}

		$files = array();
		$templates = scandir( $path );
		foreach ( $templates as $template ) {
			if ( '.' == $template[0] ) {
				continue;
			}

			if ( ! is_dir( $path . '/' . $template ) ) {
				continue;
			}

			// Find files in this directory
			$files_found = $this->scandir( $template, $path, $uri, array( 'json', 'php', 'css', 'js' ), $type );

			if ( ! $files_found ) {
				continue;
			}

			foreach ( $files_found as $key => $template_files ) {
				if ( isset( $template_files['config']['format_version'] ) && '1.0' == $template_files['config']['format_version'] ) {
					// Template format version 1 (no config file)
					$template_name     = basename( $template_files['template'], '.php' );
					$new_key           = $template . ':' . $template_name;
						$files[ $new_key ]       = $template_files;
					}
				else {
					// Template format version 2 (has config.json)
					$files[ $template ] = $template_files;
				}

			}
		}

		return $files;
	}

	/**
	 * @param      $template
	 * @param      $path
	 * @param      $uri
	 * @param null $extensions
	 * @param      $type
	 *
	 * @return array|bool
	 */
	public function scandir( $template, $path, $uri, $extensions = null, $type ) {
		if ( ! is_dir( $path . '/' . $template ) ) {
			return false;
		}

		if ( $extensions ) {
			$extensions = (array) $extensions;
		}

		// Bail if requested template type not found
		if ( $type && ! file_exists( $path . '/' . $template . '/' . $type . '.php' ) ) {
			return false;
		}

		$files = array();

		/**
		 * Template header tags for template format version 1
		 */
		$tags = apply_filters( 'wpmtst_template_header_tags', array(
			'name'        => 'Template Name',
			'description' => 'Description',
			'deps'        => 'Scripts',  // registered scripts
			'styles'      => 'Styles',   // registered styles or fonts
			'force'       => 'Force',    // dependent options
		) );

		/**
		 * Check for config file first.
		 *
		 * @since 2.30.0
		 */
		$config_found = false;
		if ( file_exists( $path . '/' . $template . '/config.json' ) ) {
			$files[ $template ]['config'] = (array) json_decode( file_get_contents( $path . '/' . $template . '/config.json' ) );
			$config_found = true;
		}

		/**
		 * Process the files.
		 * This creates an array of properties: file paths and config parameters.
		 */
		$results = scandir( $path . '/' . $template);
		foreach ( $results as $result ) {
			if ( '.' == $result[0] ) {
				continue;
			}

			if ( is_dir( $path . '/' . $template . '/' . $result ) ) {
				continue;
			}

			// If no extensions specified or if extension matches
			if ( !$extensions || preg_match( '~\.(' . implode( '|', $extensions ) . ')$~', $result ) ) {

				$default_config = array(
					'name'           => '',
					'description'    => '',
					'type'           => 'display',
					'order'          => 10,
					'scripts'        => '',
					'styles'         => '',
					'force'          => '',
					'options'        => '',
					'format_version' => '2.0',
				);

				$filename = pathinfo( $result, PATHINFO_FILENAME );
				$ext      = pathinfo( $result, PATHINFO_EXTENSION );

				// Template, stylesheet, script, or other?
				switch ( $ext ) {
					//case 'json':
					//	$key  = 'config';
					//	$base = $uri;
					//	break;
					case 'php':
						$key  = 'template';
						$base = $path;
						break;
					case 'css':
						$key  = 'stylesheet';
						$base = $uri;
						break;
					case 'js':
						$key  = 'script';
						$base = $uri;
						break;
					default:
						$key  = '';
						$base ='';
				}

				if ( $key ) {
					$files[ $template ][ $key ] = $base . '/' . $template . '/' . $result;
				}

				// Convert V1 templates to V2 by creating config array from main template file.
				if ( 'template' == $key ) {
					if ( ! $config_found ) {
						$file_data = get_file_data( $path . '/' . $template . '/' . $result, $tags );

						// Start config array
						$config = array(
							'format_version' => '1.0',
						);

						// Store header tags
					foreach ( $tags as $tag => $label ) {

						if ( 'name' == $tag ) {
								// Get the template name
							if ( isset( $file_data['name'] ) && $file_data['name'] ) {
									$config['name'] = $file_data['name'];
							}
							else {
								// Use the directory name
									$config['name'] = ucwords( str_replace( array( '_', '-' ), ' ', basename( $path ) ) );
							}
						}
						else {
								$config[ $tag ] = $file_data[ $tag ];
						}

					}

						// Set the template type
						if ( 'form.php' == $filename ) {
							$config['type'] = 'form';
					}
						elseif ( 'widget.php' == $filename ) {
							$config['type'] = 'widget';
					}

						$files[ $template ]['config'] = array_merge( $default_config, $config );
				}
			}

		}
		}

		return $files;
	}

	/**
	 * @param $a
	 * @param $b
	 *
	 * @return int
	 */
	public function sort_array_by_name( $a, $b ) {
		if ( ! isset( $a['name'] ) ) {
			$a['name'] = '';
		}
		if ( ! isset( $b['name'] ) ) {
			$b['name'] = '';
		}

		return strcmp( $a['name'], $b['name'] );
	}

	/**
	 * @param $a
	 * @param $b
	 *
	 * @return int
	 */
	public function sort_array_by_order( $a, $b ) {
		if ( ! isset( $a['config']['order'] ) ) {
			$a['config']['order'] = 0;
		}
		if ( ! isset( $b['config']['order'] ) ) {
			$b['config']['order'] = 0;
		}

		if ( $a['config']['order'] == $b['config']['order'] ) {
			return 0;
		}

		return ( $a['config']['order'] < $b['config']['order'] ) ? -1 : 1;
	}

	/**
	 * @param $a
	 * @param $b
	 *
	 * @return int
	 */
	public function sort_array_by_search_order( $a, $b ) {
		if ( ! isset( $a['order'] ) || ! isset( $b['order'] ) )
			return 0;

		if ( $a['order'] == $b['order'] ) {
			return 0;
		}

		return ( $a['order'] < $b['order'] ) ? -1 : 1;
	}

	/**
	 * @param $a
	 * @param $b
	 *
	 * @return int
	 */
	public function sort_array_by_order_name( $a, $b ) {
		if ( ! isset( $a['name'] ) || ! isset( $b['name'] ) )
			return 0;

		if ( $a['order'] == $b['order'] )
			return strcasecmp( $a['name'], $b['name'] );

		return ( $a['order'] < $b['order'] ) ? -1 : 1;
	}

}

endif;
