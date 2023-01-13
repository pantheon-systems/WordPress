<?php

/**
 * Control Preload google fonts.
 */
add_filter( 'style_loader_tag', 'oceanwp_preload_local_webfonts', 10, 2 );
if ( ! function_exists( 'oceanwp_preload_local_webfonts' ) ) {
	function oceanwp_preload_local_webfonts( $html, $handle ) {
		if ( strpos( $handle, 'oceanwp-google-font' ) === false && strpos( $handle, 'google-fonts-' ) === false ) {
			return $html;
		}
		if ( true != get_theme_mod( 'ocean_preload_local_google_font', false ) ) {
			return $html;
		}
		return str_replace( "rel='stylesheet'", "rel='preload' as='style' onload='this.rel=\"stylesheet\"'", $html );;
	}
}

/**
 * Control Elementor google fonts.
 */
add_filter( 'style_loader_src', 'oceanwp_local_elementor_webfonts_enqueue', 20, 2 );
if ( ! function_exists( 'oceanwp_local_elementor_webfonts_enqueue' ) ) {
	function oceanwp_local_elementor_webfonts_enqueue( $src, $handle ) {
		if ( strpos( $handle, 'google-fonts-' ) !== 0 ) {
			return $src;
		}

		if ( true != get_theme_mod( 'ocean_local_elementor_google_font', false ) ) {
			return $src;
		}

		$url_obj    = parse_url( $src );
		$url_params = array();
		if ( ! empty( $url_obj['query'] ) ) {
			parse_str( $url_obj['query'], $url_params );
		}
		if ( ! empty( $url_params['family'] ) ) {
			$md5_key = $url_params['family'];
		}

		if ( empty( $md5_key ) ) {
			return $src;
		}

		$font_style = ocean_get_google_font_css( $src );
		$font_style = explode( "\n", $font_style );

		if ( is_array( $font_style ) && ! empty( $font_style ) ) {

			$webfonts_dir_path     = oceanwp_get_local_webfonts_data_dir( $handle );
			$webfonts_css_dir_path = oceanwp_get_local_webfonts_css_data_dir( $handle );

			if ( is_array( $webfonts_dir_path ) && is_array( $webfonts_css_dir_path ) ) {
				foreach ( $font_style as $font_style_line ) {
					$font_style_line = trim( $font_style_line );

					preg_match_all( "'url\((.*?)\)'si", $font_style_line, $matches );

					if ( $matches !== null && ! empty( $matches[0] ) && ! empty( $matches[1] ) ) {

						$remote_font_file_url = reset( $matches[1] );

						if ( ! empty( $remote_font_file_url ) ) {

							$file_name = basename( $remote_font_file_url );

							$local_font_file_path = trailingslashit( $webfonts_dir_path['upload_dir'] ) . $file_name;
							$local_font_file_url  = apply_filters( 'oceanwp_local_font_url', trailingslashit( $webfonts_dir_path['upload_url'] ) . $file_name );

							$local_css_file_name = md5( $md5_key ) . '.css';
							$local_css_file_path = trailingslashit( $webfonts_css_dir_path['upload_dir'] ) . $local_css_file_name;

							if ( file_exists( $local_font_file_path ) ) {
								$font_style                   = str_replace( $remote_font_file_url, $local_font_file_url, $font_style );
								$is_wrote_local_css_file_path = file_put_contents( $local_css_file_path, $font_style );
								if ( $is_wrote_local_css_file_path !== false ) {
									$src = trailingslashit( $webfonts_css_dir_path['upload_url'] ) . $local_css_file_name;
								}
							} else {
								require_once ABSPATH . 'wp-admin' . '/includes/file.php';

								$tmp = download_url( $remote_font_file_url );

								if ( ! is_wp_error( $tmp ) ) {
									$move_new_file = @copy( $tmp, $local_font_file_path );

									if ( $move_new_file && file_exists( $local_font_file_path ) ) {
										$font_style                   = str_replace( $remote_font_file_url, $local_font_file_url, $font_style );
										$is_wrote_local_css_file_path = file_put_contents( $local_css_file_path, $font_style );
										if ( $is_wrote_local_css_file_path !== false ) {
											$src = trailingslashit( $webfonts_css_dir_path['upload_url'] ) . $local_css_file_name;
										}
									}

									@unlink( $tmp );
								}
							}
						}
					}
				}
			}
		}

		return $src;
	}
}


/**
 * Enqueues Local Google Font
 */
add_filter( 'oceanwp_enqueue_google_font_url', 'oceanwp_webfonts_enqueue', 20, 2 );
if ( ! function_exists( 'oceanwp_webfonts_enqueue' ) ) {
	function oceanwp_webfonts_enqueue( $src, $font_name ) {
		if ( true != get_theme_mod( 'ocean_local_google_font', true ) ) {
			return $src;
		}

		$font_style = ocean_get_google_font_css( $src );
		$font_style = explode( "\n", $font_style );

		if ( is_array( $font_style ) && ! empty( $font_style ) ) {

			$webfonts_dir_path     = oceanwp_get_local_webfonts_data_dir( $font_name );
			$webfonts_css_dir_path = oceanwp_get_local_webfonts_css_data_dir( $font_name );

			if ( is_array( $webfonts_dir_path ) && is_array( $webfonts_css_dir_path ) ) {
				foreach ( $font_style as $font_style_line ) {
					$font_style_line = trim( $font_style_line );

					preg_match_all( "'url\((.*?)\)'si", $font_style_line, $matches );

					if ( $matches !== null && ! empty( $matches[0] ) && ! empty( $matches[1] ) ) {

						$remote_font_file_url = reset( $matches[1] );

						if ( ! empty( $remote_font_file_url ) ) {

							$file_name = basename( $remote_font_file_url );

							$local_font_file_path = trailingslashit( $webfonts_dir_path['upload_dir'] ) . $file_name;
							$local_font_file_url  = apply_filters( 'oceanwp_local_font_url', trailingslashit( $webfonts_dir_path['upload_url'] ) . $file_name );

							$local_css_file_name = md5( $remote_font_file_url ) . '.css';
							$local_css_file_path = trailingslashit( $webfonts_css_dir_path['upload_dir'] ) . $local_css_file_name;

							if ( file_exists( $local_font_file_path ) ) {
								$font_style                   = str_replace( $remote_font_file_url, $local_font_file_url, $font_style );
								$is_wrote_local_css_file_path = file_put_contents( $local_css_file_path, $font_style );
								if ( $is_wrote_local_css_file_path !== false ) {
									$src = trailingslashit( $webfonts_css_dir_path['upload_url'] ) . $local_css_file_name;
								}
							} else {
								require_once ABSPATH . 'wp-admin' . '/includes/file.php';

								$tmp = download_url( $remote_font_file_url );

								if ( ! is_wp_error( $tmp ) ) {
									$move_new_file = @copy( $tmp, $local_font_file_path );

									if ( $move_new_file && file_exists( $local_font_file_path ) ) {
										$font_style                   = str_replace( $remote_font_file_url, $local_font_file_url, $font_style );
										$is_wrote_local_css_file_path = file_put_contents( $local_css_file_path, $font_style );
										if ( $is_wrote_local_css_file_path !== false ) {
											$src = trailingslashit( $webfonts_css_dir_path['upload_url'] ) . $local_css_file_name;
										}
									}

									@unlink( $tmp );
								}
							}
						}
					}
				}
			}
		}

		return $src;
	}
}


/**
 * Get Google Font CSS
 */
if ( ! function_exists( 'ocean_get_google_font_css' ) ) {
	function ocean_get_google_font_css( $url ) {
		if ( strpos( $url, 'https:' ) === false && strpos( $url, 'http:' ) === false ) {
			$url = 'https:' . $url;
		}

		$font_format = get_theme_mod( 'ocean_local_google_font_format', 'ttf' );
		switch ( $font_format ) {
			case 'ttf':
				$user_agent = 'Mozilla/5.0 (Windows; U; Windows NT 6.1; et; rv:1.9.1.9) Gecko/20100315 Firefox/3.5.9';
				break;
			case 'woff':
				$user_agent = 'Mozilla/5.0 (Windows NT 6.3; rv:36.0) Gecko/20100101 Firefox/36.0';
				break;
			case 'woff2':
				$user_agent = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/105.0.0.0 Safari/537.36';
				break;
			default:
				$user_agent = 'Mozilla/5.0 (Windows; U; Windows NT 6.1; et; rv:1.9.1.9) Gecko/20100315 Firefox/3.5.9';
			break;
		}

		$transient_name = 'gf_' . md5( $url ) . '_' . $font_format;
		$cached_value = get_transient( $transient_name );
		if( false === $cached_value ) {
			$request = wp_safe_remote_get(
				$url,
				array(
					'sslverify' => false,
					'user-agent' => $user_agent,
				)
			);
			if ( is_wp_error( $request ) ) {
				return '';
			}
			$result = wp_remote_retrieve_body( $request );
			set_transient( $transient_name, $result , HOUR_IN_SECONDS * 24 );
		} else {
			$result = $cached_value;
		}

		return $result;
	}
}

/**
 * Get Local WebFonts data
 */
if ( ! function_exists( 'oceanwp_get_local_webfonts_data_dir' ) ) {
	function oceanwp_get_local_webfonts_data_dir( $file_name ) {
		$upload      = wp_upload_dir();
		$uploads_dir = 'oceanwp-webfonts';

		// Create directory
		if ( ! is_dir( trailingslashit( $upload['basedir'] ) . $uploads_dir ) ) {
			wp_mkdir_p( trailingslashit( $upload['basedir'] ) . $uploads_dir );
		}

		return array(
			'upload_dir' => trailingslashit( $upload['basedir'] ) . $uploads_dir,
			'upload_url' => trailingslashit( $upload['baseurl'] ) . $uploads_dir,
		);
	}
}

/**
 * Get Local WebFonts CSS data
 */
if ( ! function_exists( 'oceanwp_get_local_webfonts_css_data_dir' ) ) {
	function oceanwp_get_local_webfonts_css_data_dir( $file_name ) {
		$upload      = wp_upload_dir();
		$uploads_dir = 'oceanwp-webfonts-css';

		// Create directory
		if ( ! is_dir( trailingslashit( $upload['basedir'] ) . $uploads_dir ) ) {
			wp_mkdir_p( trailingslashit( $upload['basedir'] ) . $uploads_dir );
		}

		return array(
			'upload_dir' => trailingslashit( $upload['basedir'] ) . $uploads_dir,
			'upload_url' => trailingslashit( $upload['baseurl'] ) . $uploads_dir,
		);
	}
}

add_filter( 'oceanwp_local_font_url', 'oceanwp_webfonts_local_font_url' );
if ( ! function_exists( 'oceanwp_webfonts_local_font_url' ) ) {
	function oceanwp_webfonts_local_font_url( $url ) {
		if ( strpos( $url, 'https://' ) === 0 ) {
			$url = str_replace( 'https://', '//', $url );
		}
		if ( strpos( $url, 'http://' ) === 0 ) {
			$url = str_replace( 'http://', '//', $url );
		}
		return $url;
	}
}


// Setup theme => Generate the custom CSS file.
add_action( 'admin_bar_init', 'ocean_save_customizer_css_in_file', 9999 );
if ( ! function_exists( 'ocean_save_customizer_css_in_file' ) ) {
	function ocean_save_customizer_css_in_file( $output = null ) {

		if ( ! defined( 'FS_CHMOD_DIR' ) ) {
			define( 'FS_CHMOD_DIR', ( fileperms( ABSPATH ) & 0777 | 0755 ) );
		}

		// If Custom File is not selected.
		if ( 'file' !== get_theme_mod( 'ocean_customzer_styling', 'head' ) ) {
			return;
		}

		// Get all the customier css.
		$output = apply_filters( 'ocean_head_css', $output );

		// Get Custom Panel CSS.
		$output_custom_css = wp_get_custom_css();

		// Minified the Custom CSS.
		$output .= oceanwp_minify_css( $output_custom_css );

		$upload_dir = wp_upload_dir(); // Grab uploads folder array.
		$dir        = trailingslashit( $upload_dir['basedir'] ) . 'oceanwp' . DIRECTORY_SEPARATOR; // Set storage directory path.
		$untraling_dir = untrailingslashit( $dir );
		if ( ! file_exists( $untraling_dir ) ) {
			mkdir( $untraling_dir, FS_CHMOD_DIR );
		}
		if( file_exists( $untraling_dir ) ) {
			$filename = $dir . 'custom-style.css';
			if ( file_put_contents( $filename, $output ) ) {
				chmod( $filename, 0644 );
			}
		}
	}
}
