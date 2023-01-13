<?php

/* WordPress Check */
if ( ! defined( 'ABSPATH' ) ) {

	exit;
}

/**
 * API Client
 */
class Ocean_Push_Monkey_Client {

	public $endpointURL;
	public $registerURL;
	public $cartURL;

	/* Public */

	const PLAN_NAME_KEY = 'oe_push_monkey_plan_name_output';

	/**
	 * Get the stats for an Account Key.
	 * @param string $account_key 
	 * @return mixed; false if nothing found; array otherwise.
	 */
	public function get_stats( $account_key ) {

		$stats_api_url = $this->endpointURL . '/stats/api';
		$args = array( 'body' => array( 'account_key' => $account_key ) );
		$response = wp_remote_post( $stats_api_url, $args );
		if( is_wp_error( $response ) ) {
			return ( object ) array( 'error' => $response->get_error_message() );
		} else {

			$body = wp_remote_retrieve_body( $response );
			$output = json_decode( $body ); 
			return $output;
		}
		return false;
	}

	/**
	 * Get the Website Push ID for an Account Key.
	 * @param string $account_key 
	 * @return string; array with error info if an error occured.
	 */
	public function get_website_push_ID( $account_key ) {

		$url = $this->endpointURL . '/v2/api/website_push_id';
		$args = array( 'body' => array( 'account_key' => $account_key ) );

		$response = wp_remote_post( $url, $args );

		if( is_wp_error( $response ) ) {

			return ( object ) array( 'error' => $response->get_error_message() );
		} 
		$body = wp_remote_retrieve_body( $response );
		$output = json_decode( $body ); 
		return $output;
	}

	/**
	 * Sends a desktop push notification.
	 * @param string $account_key 
	 * @param string $title 
	 * @param string $body 
	 * @param string $url_args 
	 * @param boolean $custom 
	 */
	public function send_push_notification( $account_key, $title, $body, $url_args, $custom, $segments, $locations, $image = NULL ) {

		$url = $this->endpointURL . '/push_message';
		$args = array( 
			'account_key' => $account_key,
			'title' => $title,
			'body' => $body, 
			'url_args' => $url_args,
			'send_to_segments_string' => implode(",", $segments),
			'send_to_locations_string' => implode(",", $locations),
			'image' => $image
		);
		if ( $custom ) {

			$args['custom'] = true;
		}
		$response = $this->post_with_file( $url, $args, $image );
		if( is_wp_error( $response ) ) {
		} else {
		}
	}

	/**
	 * Get the plan name.
	 * @param string $account_key 
	 * @return string; array with error info otherwise.
	 */
	public function get_plan_name( $account_key ) {

		$output = $this->cache->get( self::PLAN_NAME_KEY );
		if ( $output ) {
			return (object) $output;
		}

		$url = $this->endpointURL . '/v2/api/get_plan_name';
		$args = array( 'body' => array( 'account_key' => $account_key ) );

		$response = wp_remote_post( $url, $args );

		if( is_wp_error( $response ) ) {

			return ( object ) array( 'error' => $response->get_error_message() );
		} 
		$body = wp_remote_retrieve_body( $response );
		$output = json_decode( $body ); 
		$serialized_output = json_decode( $body, true );
		if ( isset( $output->error ) ) {
			return $output->error;
		} else {
			$this->cache->store( self::PLAN_NAME_KEY, $serialized_output );
			return $output;
		}
		return '';
	}

	/**
	 * Get all the segments
	 * @param string $account_key
	 * @return associative array of [id=>string]
	 */
	public function get_segments( $account_key ) {

		$segments_api_url = $this->endpointURL . '/push/v1/segments/' . $account_key;
		$response = wp_remote_post( $segments_api_url, array() );
		if( is_wp_error( $response ) ) {
			return ( object ) array( 'error' => $response->get_error_message() );
		} else {

			$body = wp_remote_retrieve_body( $response );
			$output = json_decode( $body, true ); 
			if ( isset( $output["segments"] ) ) {

				if ( count( $output["segments"] ) > 0 ) {

					if ( gettype($output["segments"][0]) == "array" ) {

						return $output["segments"];
					}
				}
			}
		}
		return array();		
	}

	/**
	 * Save a segments
	 * @param string $account_key
	 * @param string $name	 
	 * @return response or error
	 */
	public function save_segment( $account_key, $name ) {

		$url = $this->endpointURL . '/push/v1/segments/create/' . $account_key;
		$args = array( 'body' => array( 
			
			'name' => $name
			) );
		$response = wp_remote_post( $url, $args );
		if ( is_wp_error( $response ) ) {
			
			return ( object ) array( 'error' => $response->get_error_message() );
		} else {

			$body = wp_remote_retrieve_body( $response );
			$output = json_decode( $body );
			return $output;				
		}
		return false;
	}

	/**
	 * Delete a segments
	 * @param string $account_key
	 * @param string $id of segment	 
	 * @return response or error
	 */
	public function delete_segment( $account_key, $id ) {

		$url = $this->endpointURL . '/push/v1/segments/delete/' . $account_key;
		$args = array( 'body' => array( 
			
			'id' => $id
			) );
		$response = wp_remote_post( $url, $args );
		if ( is_wp_error( $response ) ) {
			
			return ( object ) array( 'error' => $response->get_error_message() );
		} else {

			$body = wp_remote_retrieve_body( $response );
			$output = json_decode( $body );
			return $output;				
		}
		return false;		
	}

	/**
	 * Retrieve the status of a welcome message
	 * @param string $account_key
	 * @return associative array of JSON response
	 */
	public function get_welcome_message_status( $account_key ) {

		$url = $this->endpointURL . '/v2/api/welcome_notification_status/' . $account_key;
		$response = wp_remote_post( $url, array() );
		if( is_wp_error( $response ) ) {
			return ( object ) array( 'error' => $response->get_error_message() );			
		}
		$body = wp_remote_retrieve_body( $response );
		$output = json_decode( $body, true ); 
		if ( empty( $output ) ) {

			return ( object ) array( 'error' => 'empty' );			
		}
		return $output;
	}

	/**
	 * Retrieve the status of a welcome message
	 * @param string $account_key
	 * @return associative array of JSON response
	 */
	public function get_custom_prompt( $account_key ){

		$url = $this->endpointURL . '/v2/api/custom_prompt/' . $account_key;
		$response = wp_remote_post( $url, array() );
		if( is_wp_error( $response ) ) {
			return ( object ) array( 'error' => $response->get_error_message() );			
		}
		$body = wp_remote_retrieve_body( $response );
		$output = json_decode( $body, true ); 
		if ( empty( $output ) ) {

			return ( object ) array( 'error' => 'empty' );			
		}
		return $output;
	}

	/**
	 * Update the welcome message info
	 * @param string $account_key
	 * @param boolean $enabled
	 * @param string $message
 	 * @param string $title
	 * @return boolean. True if operation finished successfully.
	 */
	public function update_custom_prompt( $account_key, $enabled, $title, $message ) {

		$url = $this->endpointURL . '/v2/api/custom_prompt/' . $account_key . '/update';
		$args = array( 'body' => array( 
			
			'custom_prompt_message' => $message,
			'custom_prompt_title' => $title
		) );		
		if ( $enabled ) {

			$args['body']['enabled'] = true;
		}
		$response = wp_remote_post( $url, $args );
		if( is_wp_error( $response ) ) {
			return ( object ) array( 'error' => $response->get_error_message() );			
		}
		$body = wp_remote_retrieve_body( $response );
		$output = json_decode( $body, true ); 
		if ( isset( $output["response"] ) ) {

			if ( $output['response'] == "ok" ) {

				return true;
			}
		}
		return false;		
	}

	/**
	 * Retrieve locations stored for this account key
	 * @param string $account_key
	 * @return associative array of JSON response	 
	 */
	public function get_locations( $account_key ) { 

		$url = $this->endpointURL . '/v2/api/locations/' . $account_key;
		$response = wp_remote_post( $url, array() );
		if( is_wp_error( $response ) ) {
			return ( object ) array( 'error' => $response->get_error_message() );			
		}
		$body = wp_remote_retrieve_body( $response );
		$output = json_decode( $body, true ); 
		if ( empty( $output ) ) {

			return ( object ) array( 'error' => 'empty' );			
		}
		return $output;
	}

	/**
	 * Update the welcome message info
	 * @param string $account_key
	 * @param boolean $enabled
	 * @param string $message
	 * @return boolean. True if operation finished successfully.
	 */
	public function update_welcome_message( $account_key, $enabled, $message ) {

		$url = $this->endpointURL . '/v2/api/update_welcome_notification/' . $account_key;
		$args = array( 'body' => array( 
			
			'message' => $message
		) );		
		if ( $enabled ) {

			$args['body']['enabled'] = true;
		}
		$response = wp_remote_post( $url, $args );
		if( is_wp_error( $response ) ) {
			return ( object ) array( 'error' => $response->get_error_message() );			
		}
		$body = wp_remote_retrieve_body( $response );
		$output = json_decode( $body, true ); 			
		if ( isset( $output["status"] ) ) {

			if ( $output['status'] == "ok" ) {

				return true;
			}
		}
		return false;
	}

	/**
	 * Update the WooCommerce setting
	 * @param string $account_key
	 * @param string $delay
	 * @param string $title	 
	 * @param string $message
	 * @param file $image
	 * @return boolean. True if operation finished successfully.
	 */
	public function update_woo_settings( $account_key, $delay, $title, $message, $image_path, $image_name ) {

		$url = $this->endpointURL . '/magento/v1/api/update/' . $account_key;
		$args = array( 
		
			'abandoned_cart_delay' => $delay,
			'abandoned_cart_title' => $title,
			'abandoned_cart_message' => $message,
			'image' => $image_name
		);	
		$response = $this->post_with_file( $url, $args, $image_path, $image_name );
		if( is_wp_error( $response ) ) {
			return ( object ) array( 'error' => $response->get_error_message() );			
		}
		$body = wp_remote_retrieve_body( $response );
		$output = json_decode( $body, true ); 
		if ( isset( $output["response"] ) ) {

			if ( $output['response'] == "ok" ) {

				return true;
			}
		}
		return false;		
	}

	/**
	 * Retrieve the WooCommerce setting
	 * @param string $account_key
	 * @return associative array of JSON response.
	 */
	public function get_woo_settings( $account_key ) {
		if ( $account_key ) {
			$url = $this->endpointURL . '/magento/v1/api/get/' . $account_key;
			$response = wp_remote_post( $url, array() );
			if( is_wp_error( $response ) ) {
				return ( object ) array( 'error' => $response->get_error_message() );			
			}
			$body = wp_remote_retrieve_body( $response );
			$output = json_decode( $body, true ); 
			if ( empty( $output ) ) {

				return ( object ) array( 'error' => 'empty' );			
			}
			return $output;
		}
	}

	/**
 	 * Creates cart with the API token and cart ID
 	 * Returns false on WP errors.
	 * Returns true.
	 * @param string $api_token
	 * @param string $cart_id
	 * @return mixed; false if nothing found; array otherwise. 
	 */
	public function create_cart( $cart_id, $api_token ) {

		$cart_url = $this->cartURL;
		$args = array( 
			'headers'   => array( 'Content-Type' => 'application/json; charset=utf-8' ),
		  'body'      => json_encode( array( 'token' => $api_token, 'cart_id' => $cart_id ) ) 
		);
		$response = wp_remote_post( $cart_url, $args );
		if ( is_wp_error( $response ) ) {
			
			return ( object ) array( 'error' => $response->get_error_message() );
		} 
		$body = wp_remote_retrieve_body( $response );
		$output = json_decode( $body, true );
		if ( isset( $output["response"] ) ) {

			if ( $output['response'] == "ok" ) {
				return true;
			}
		}	
		return false;
	}

	/**
 	 * Updates cart with the API token and cart ID
	 * Returns false on WP errors.
	 * Returns true.
	 * @param string $api_token
	 * @param string $cart_id
	 * @return mixed; false if nothing found; array otherwise. 
	 */
	public function update_cart( $cart_id, $api_token ) {
		$cart_url = $this->cartURL;
		$args = array( 
			'method'  => 'PUT',
			'headers' => array( 'Content-Type' => 'application/json; charset=utf-8' ),
			'body'    => json_encode(array( 'token' => $api_token, 'cart_id' => $cart_id ) ) 
		);
		$response = wp_remote_request( $cart_url, $args );
		
		if ( is_wp_error( $response ) ) {
			
			return ( object ) array( 'error' => $response->get_error_message() );
		} 
		$body = wp_remote_retrieve_body( $response );
		$output = json_decode( $body,true );
		if ( isset( $output["response"] ) ) {

			if ( $output['response'] == "ok" ) {

				return true;
			}
		}
		return false;
	}

	/* Private */

	function __construct( $endpoint_url ) {

		$this->endpointURL = $endpoint_url;
		$this->registerURL = $endpoint_url.'/v2/register';
		$this->cartURL = $endpoint_url.'/magento/v1/cart';
	}

	function post_with_file( $url, $data, $file_path, $filename = NULL ) {

		$boundary = wp_generate_password( 24 );
		$headers  = array(

			'content-type' => 'multipart/form-data; boundary=' . $boundary
		);
		$payload = '';
		// First, add the standard POST fields:
		foreach ( $data as $name => $value ) {

			$payload .= '--' . $boundary;
			$payload .= "\r\n";
			$payload .= 'Content-Disposition: form-data; name="' . $name . '"' . "\r\n\r\n";
			$payload .= $value;
			$payload .= "\r\n";
		}
		// Upload the file
		if ( $file_path ) {

			$payload .= '--' . $boundary;
			$payload .= "\r\n";
			if ( $filename ) {

				$payload .= 'Content-Disposition: form-data; name="' . 'image' . '"; filename="' . basename( $filename ) . '"' . "\r\n";
			} else {

				$payload .= 'Content-Disposition: form-data; name="' . 'image' . '"; filename="' . basename( $file_path ) . '"' . "\r\n";				
			}
			//        $payload .= 'Content-Type: image/jpeg' . "\r\n";
			$payload .= "\r\n";
			$payload .= file_get_contents( $file_path );
			$payload .= "\r\n";
		}
		$payload .= '--' . $boundary . '--';
		$response = wp_remote_post( $url,
			array(
				'headers'    => $headers,
				'body'       => $payload,
			)
		);
		return $response;
	}
}
