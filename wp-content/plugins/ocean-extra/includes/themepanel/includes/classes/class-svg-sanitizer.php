<?php

if ( ! class_exists( 'Ocean_Extra_SVG_Sanitizer' ) ) {
	class Ocean_Extra_SVG_Sanitizer {


		/**
		 * The sanitizer
		 *
		 * @var \enshrined\svgSanitize\Sanitizer
		 */
		protected $sanitizer;

		public function __construct() {
			 // Try and include our autoloader.
			if ( is_readable( OE_PATH . 'includes/themepanel/includes/libs/vendor/autoload.php' ) ) {
				require OE_PATH . 'includes/themepanel/includes/libs/vendor/autoload.php';

				$this->sanitizer = new enshrined\svgSanitize\Sanitizer();
				$this->sanitizer->minify( true );

				add_filter( 'wp_handle_upload_prefilter', array( $this, 'sanitize_svg' ) );
			} else {
				update_option( 'oe_svg_support_active_status', 'no' );
				add_action(
					'admin_notices',
					function () {
						?>
					<div class="notice notice-error">
						<p>
							<?php
							echo wp_kses_post(
								sprintf(
									/* translators: %1$s is the command that needs to be run. */
									__( 'You appear to be running a development version of Safe SVG. Please run %1$s in order for things to work properly.', 'ocean-extra' ),
									'<code>composer install</code>'
								)
							);
							?>
						</p>
					</div>
						<?php
					}
				);
				return;
			}
		}

		/**
		 * Check if the file is an SVG, if so handle appropriately
		 *
		 * @param $file
		 *
		 * @return mixed
		 */
		public function sanitize_svg( $file ) {
			// Ensure we have a proper file path before processing
			if ( ! isset( $file['tmp_name'] ) ) {
				return $file;
			}

			$file_name   = isset( $file['name'] ) ? $file['name'] : '';
			$wp_filetype = wp_check_filetype_and_ext( $file['tmp_name'], $file_name );
			$type        = ! empty( $wp_filetype['type'] ) ? $wp_filetype['type'] : '';

			if ( $type === 'image/svg+xml' ) {
				if ( ! $this->sanitize( $file['tmp_name'] ) ) {
					$file['error'] = __(
						"Sorry, this file couldn't be sanitized so for security reasons wasn't uploaded",
						'ocean-extra'
					);
				}
			}

			return $file;
		}

		/**
		 * Sanitize the SVG
		 *
		 * @param $file
		 *
		 * @return bool|int
		 */
		protected function sanitize( $file ) {
			$dirty = file_get_contents( $file );

			// Is the SVG gzipped? If so we try and decode the string
			if ( $is_zipped = $this->is_gzipped( $dirty ) ) {
				$dirty = gzdecode( $dirty );

				// If decoding fails, bail as we're not secure
				if ( $dirty === false ) {
					return false;
				}
			}

			$clean = $this->sanitizer->sanitize( $dirty );

			if ( $clean === false ) {
				return false;
			}

			// If we were gzipped, we need to re-zip
			if ( $is_zipped ) {
				$clean = gzencode( $clean );
			}

			file_put_contents( $file, $clean );

			return true;
		}

		/**
		 * Check if the contents are gzipped
		 *
		 * @see http://www.gzip.org/zlib/rfc-gzip.html#member-format
		 *
		 * @param $contents
		 *
		 * @return bool
		 */
		protected function is_gzipped( $contents ) {
			if ( function_exists( 'mb_strpos' ) ) {
				return 0 === mb_strpos( $contents, "\x1f" . "\x8b" . "\x08" );
			} else {
				return 0 === strpos( $contents, "\x1f" . "\x8b" . "\x08" );
			}
		}
	}
}

new Ocean_Extra_SVG_Sanitizer();
