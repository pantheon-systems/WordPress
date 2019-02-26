<?php
if ( ! class_exists( 'WPSmushNextGenBulk' ) ) {
	class WPSmushNextGenBulk extends WpSmushNextGen {

		function __construct() {
			add_action( 'wp_ajax_wp_smushit_nextgen_bulk', array( $this, 'smush_bulk' ) );
		}

		function smush_bulk() {

			global $wpsmushnextgenstats, $wpsmush_db, $wpsmushit_admin, $wpsmushnextgenadmin, $wp_smush;

			$stats = array();

			if ( empty( $_GET['attachment_id'] ) ) {
				wp_send_json_error( 'missing id' );
			}

			$send_error = false;

			$atchmnt_id = absint( (int) $_GET['attachment_id'] );

			$smush = $this->smush_image( $atchmnt_id, '', false, true );

			if ( is_wp_error( $smush ) ) {
				$send_error = true;
				$msg        = '';
				$error      = $smush->get_error_message();
				//Check for timeout error and suggest to filter timeout
				if ( strpos( $error, 'timed out' ) ) {
					$msg = '<p class="wp-smush-error-message">' . esc_html__( "Smush request timed out. You can try setting a higher value ( > 60 ) for `WP_SMUSH_API_TIMEOUT`.", "wp-smushit" ) . '</p>';
				}
			} else {
				//Check if a resmush request, update the resmush list
				if ( ! empty( $_REQUEST['is_bulk_resmush'] ) && $_REQUEST['is_bulk_resmush'] ) {
					$wpsmushit_admin->update_resmush_list( $atchmnt_id, 'wp-smush-nextgen-resmush-list' );
				}
				$stats['is_lossy'] = !empty( $smush['stats'] ) ? $smush['stats']['lossy'] : 0;

				//Size before and after smush
				$stats['size_before'] = !empty( $smush['stats'] ) ? $smush['stats']['size_before'] : 0;
				$stats['size_after'] = !empty( $smush['stats'] ) ? $smush['stats']['size_after'] : 0;
			}

			//Get the resmush ids list
			if ( empty( $wpsmushnextgenadmin->resmush_ids ) ) {
				$wpsmushnextgenadmin->resmush_ids = get_option( 'wp-smush-nextgen-resmush-list' );
			}

			$wpsmushnextgenadmin->resmush_ids = empty( $wpsmushnextgenadmin->resmush_ids ) ? get_option( 'wp-smush-nextgen-resmush-list' ) : array();
			$resmush_count  = ! empty( $wpsmushnextgenadmin->resmush_ids ) ? count( $wpsmushnextgenadmin->resmush_ids ) : 0;
			$smushed_images = $wpsmushnextgenstats->get_ngg_images( 'smushed' );

			//remove resmush ids from smushed images list
			if ( $resmush_count > 0 && is_array( $wpsmushnextgenadmin->resmush_ids ) ) {
				foreach ( $smushed_images as $image_k => $image ) {
					if ( in_array( $image_k, $wpsmushnextgenadmin->resmush_ids ) ) {
						unset( $smushed_images[ $image_k ] );
					}
				}
			}

			//Get the image count and smushed images count
			$image_count   = ! empty( $smush ) && ! empty( $smush['sizes'] ) ? count( $smush['sizes'] ) : 0;
			$smushed_count = is_array( $smushed_images ) ? count( $smushed_images ) : 0;

			$stats['smushed'] = ! empty( $wpsmushnextgenadmin->resmush_ids ) ? $smushed_count - $resmush_count : $smushed_count;
			$stats['count']   = $image_count;

			$send_error ? wp_send_json_error( array(
				'stats'     => $stats,
				'error_msg' => $msg
			) ) : wp_send_json_success( array( 'stats' => $stats ) );
		}

	}
}