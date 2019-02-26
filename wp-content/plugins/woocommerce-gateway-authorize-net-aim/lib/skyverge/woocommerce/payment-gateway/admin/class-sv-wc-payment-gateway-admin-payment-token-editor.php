<?php
/**
 * WooCommerce Payment Gateway Framework
 *
 * This source file is subject to the GNU General Public License v3.0
 * that is bundled with this package in the file license.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.html
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@skyverge.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade the plugin to newer
 * versions in the future. If you wish to customize the plugin for your
 * needs please refer to http://www.skyverge.com
 *
 * @package   SkyVerge/WooCommerce/Payment-Gateway/Admin
 * @author    SkyVerge
 * @copyright Copyright (c) 2013-2018, SkyVerge, Inc.
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

defined( 'ABSPATH' ) or exit;

/**
 * The token editor.
 *
 * @since 4.3.0
 */
class SV_WC_Payment_Gateway_Admin_Payment_Token_Editor {


	/** @var \SV_WC_Payment_Gateway_Direct the gateway object **/
	protected $gateway;


	/**
	 * Construct the editor.
	 *
	 * @since 4.3.0
	 * @param \SV_WC_Payment_Gateway_Direct the gateway object
	 */
	public function __construct( SV_WC_Payment_Gateway_Direct $gateway ) {

		$this->gateway = $gateway;

		// Load the editor scripts and styles
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts_styles' ) );

		// Display the tokens markup inside the editor
		add_action( 'wc_payment_gateway_' . $this->get_gateway()->get_id() . '_token_editor_tokens', array( $this, 'display_tokens' ) );

		/** AJAX actions **/

		// Get the blank token markup via AJAX
		add_action( 'wp_ajax_wc_payment_gateway_' . $this->get_gateway()->get_id() . '_admin_get_blank_payment_token', array( $this, 'ajax_get_blank_token' ) );

		// Remove a token via AJAX
		add_action( 'wp_ajax_wc_payment_gateway_' . $this->get_gateway()->get_id() . '_admin_remove_payment_token', array( $this, 'ajax_remove_token' ) );

		// Refresh the tokens via AJAX
		add_action( 'wp_ajax_wc_payment_gateway_' . $this->get_gateway()->get_id() . '_admin_refresh_payment_tokens', array( $this, 'ajax_refresh_tokens' ) );
	}


	/**
	 * Load the editor scripts and styles.
	 *
	 * @since 4.3.0
	 */
	public function enqueue_scripts_styles() {

		// Stylesheet
		wp_enqueue_style( 'sv-wc-payment-gateway-token-editor', $this->get_gateway()->get_plugin()->get_payment_gateway_framework_assets_url() . '/css/admin/sv-wc-payment-gateway-token-editor.min.css', array(), SV_WC_Plugin::VERSION );

		// Main editor script
		wp_enqueue_script( 'sv-wc-payment-gateway-token-editor', $this->get_gateway()->get_plugin()->get_payment_gateway_framework_assets_url() . '/js/admin/sv-wc-payment-gateway-token-editor.min.js', array( 'jquery' ), SV_WC_Plugin::VERSION, true );

		wp_localize_script( 'sv-wc-payment-gateway-token-editor', 'wc_payment_gateway_token_editor', array(
			'ajax_url' => admin_url( 'admin-ajax.php' ),
			'actions'  => array(
				'remove_token' => array(
					'ays'   => __( 'Are you sure you want to remove this token?', 'woocommerce-plugin-framework' ),
					'nonce' => wp_create_nonce( 'wc_payment_gateway_admin_remove_payment_token' ),
				),
				'add_token' => array(
					'nonce' => wp_create_nonce( 'wc_payment_gateway_admin_get_blank_payment_token' ),
				),
				'refresh' => array(
					'nonce' => wp_create_nonce( 'wc_payment_gateway_admin_refresh_payment_tokens' ),
				),
				'save' => array(
					'error' => __( 'Invalid token data', 'woocommerce-plugin-framework' ),
				),
			),
		) );
	}


	/**
	 * Display the token editor.
	 *
	 * @since 4.3.0
	 * @param int $user_id the user ID
	 */
	public function display( $user_id ) {

		$id      = $this->get_gateway()->get_id();
		$title   = $this->get_title();
		$columns = $this->get_columns();
		$actions = $this->get_actions();

		include( $this->get_gateway()->get_plugin()->get_payment_gateway_framework_path() . '/admin/views/html-user-payment-token-editor.php' );
	}


	/**
	 * Display the tokens.
	 *
	 * @since 4.3.0
	 * @param int $user_id the user ID
	 */
	public function display_tokens( $user_id ) {

		$tokens = $this->get_tokens( $user_id );

		$fields     = $this->get_fields();
		$input_name = $this->get_input_name();
		$actions    = $this->get_token_actions();
		$type       = $this->get_payment_type();

		$index = 0;

		foreach ( $tokens as $token ) {

			include( $this->get_gateway()->get_plugin()->get_payment_gateway_framework_path() . '/admin/views/html-user-payment-token-editor-token.php' );

			$index++;
		}
	}


	/**
	 * Save the token editor.
	 *
	 * @since 4.3.0
	 * @param int $user_id the user ID
	 */
	public function save( $user_id ) {

		$tokens = ( isset( $_POST[ $this->get_input_name() ] ) ) ? $_POST[ $this->get_input_name() ] : array();

		$built_tokens = array();

		foreach ( $tokens as $data ) {

			$token_id = $data['id'];

			unset( $data['id'] );

			if ( ! $token_id ) {
				continue;
			}

			if ( 'credit_card' === $data['type'] ) {
				$data = $this->prepare_expiry_date( $data );
			}

			// Set the default method
			$data['default'] = $token_id === SV_WC_Helper::get_post( $this->get_input_name() . '_default' );

			if ( $data = $this->validate_token_data( $token_id, $data ) ) {
				$built_tokens[ $token_id ] = $this->build_token( $user_id, $token_id, $data );
			}
		}

		$this->update_tokens( $user_id, $built_tokens );
	}


	/**
	 * Add a token via AJAX.
	 *
	 * @since 4.3.0
	 */
	public function ajax_get_blank_token() {

		check_ajax_referer( 'wc_payment_gateway_admin_get_blank_payment_token', 'security' );

		$index = SV_WC_Helper::get_request( 'index' );

		if ( $index ) {

			$fields     = $this->get_fields();
			$input_name = $this->get_input_name();
			$actions    = $this->get_token_actions();
			$type       = $this->get_payment_type();
			$user_id    = 0;

			$token = array_fill_keys( array_keys( $fields ), '' );
			$token['id']      = '';
			$token['expiry']  = '';
			$token['default'] = false;

			ob_start();

			include( $this->get_gateway()->get_plugin()->get_payment_gateway_framework_path() . '/admin/views/html-user-payment-token-editor-token.php' );

			$html = ob_get_clean();

			wp_send_json_success( $html );

		} else {

			wp_send_json_error();
		}
	}


	/**
	 * Remove a token via AJAX.
	 *
	 * @since 4.3.0
	 */
	public function ajax_remove_token() {

		check_ajax_referer( 'wc_payment_gateway_admin_remove_payment_token', 'security' );

		$user_id  = SV_WC_Helper::get_request( 'user_id' );
		$token_id = SV_WC_Helper::get_request( 'token_id' );

		if ( $this->remove_token( $user_id, $token_id ) ) {
			wp_send_json_success();
		} else {
			wp_send_json_error();
		}
	}


	/**
	 * Refresh the tokens list via AJAX.
	 *
	 * @since 4.3.0
	 */
	public function ajax_refresh_tokens() {

		check_ajax_referer( 'wc_payment_gateway_admin_refresh_payment_tokens', 'security' );

		$user_id = SV_WC_Helper::get_request( 'user_id' );

		if ( $user_id ) {

			ob_start();

			$this->display_tokens( $user_id );

			$html = ob_get_clean();

			wp_send_json_success( trim( $html ) );

		} else {

			wp_send_json_error();
		}
	}


	/**
	 * Build a token object from data saved in the admin.
	 *
	 * This method allows concrete gateways to add special token data.
	 * See Authorize.net CIM for an example.
	 *
	 * @since 4.3.0
	 * @param int $user_id the user ID
	 * @param string $token_id the token ID
	 * @param array $data the token data
	 * @return \SV_WC_Payment_Gateway_Payment_Token the payment token object
	 */
	protected function build_token( $user_id, $token_id, $data ) {

		return $this->get_gateway()->get_payment_tokens_handler()->build_token( $token_id, $data );
	}


	/**
	 * Update the user's token data.
	 *
	 * @since 4.3.0
	 * @param int $user_id the user ID
	 * @param array the token objects
	 */
	protected function update_tokens( $user_id, $tokens ) {

		$this->get_gateway()->get_payment_tokens_handler()->update_tokens( $user_id, $tokens, $this->get_gateway()->get_environment() );
	}


	/**
	 * Remove a specific token.
	 *
	 * @since 4.3.0
	 * @param int $user_id the user ID
	 * @param string $token_id the token ID
	 * @return bool whether the token was successfully removed
	 */
	protected function remove_token( $user_id, $token_id ) {

		return $this->get_gateway()->get_payment_tokens_handler()->remove_token( $user_id, $token_id, $this->get_gateway()->get_environment() );
	}


	/**
	 * Validate a token's data before saving.
	 *
	 * Concrete gateways can override this to provide their own validation.
	 *
	 * @since 4.3.0
	 * @param array $data the token data
	 * @return array|bool the validated token data or false if the token should not be saved
	 */
	protected function validate_token_data( $token_id, $data ) {

		/**
		 * Filter the validated token data.
		 *
		 * @since 4.3.0
		 * @param array $data the validated token data
		 * @param string $token_id the token ID
		 * @param \SV_WC_Payment_Gateway_Admin_Payment_Token_Editor the token editor instance
		 * @return array the validated token data
		 */
		return apply_filters( 'wc_payment_gateway_' . $this->get_gateway()->get_id() . '_token_editor_validate_token_data', $data, $token_id, $this );
	}


	/**
	 * Correctly format a credit card expiration date for storage.
	 *
	 * @since 4.3.0
	 * @param array $data
	 * @return array
	 */
	protected function prepare_expiry_date( $data ) {

		// expiry date must be present, include a forward slash and be 5 characters (MM/YY)
		if ( ! $data['expiry'] || ! SV_WC_Helper::str_exists( $data['expiry'], '/' ) || 5 !== strlen( $data['expiry'] ) ) {
			unset( $data['expiry'] );
			return $data;
		}

		list( $data['exp_month'], $data['exp_year'] ) = explode( '/', $data['expiry'] );

		unset( $data['expiry'] );

		return $data;
	}


	/**
	 * Get the stored tokens for a user.
	 *
	 * @since 4.3.0
	 * @param int $user_id the user ID
	 * @return array the tokens in db format
	 */
	protected function get_tokens( $user_id ) {

		// Clear any cached tokens
		$this->get_gateway()->get_payment_tokens_handler()->clear_transient( $user_id );

		$stored_tokens = $this->get_gateway()->get_payment_tokens_handler()->get_tokens( $user_id );
		$tokens        = array();

		foreach( $stored_tokens as $token ) {

			$token_id = $token->get_id();

			// Set the token data
			$tokens[ $token_id ] = $token->to_datastore_format();

			$tokens[ $token_id ]['id'] = $token_id;

			// Set the credit card expiration date
			if ( $token->is_credit_card() ) {
				$tokens[ $token_id ]['expiry'] = $token->get_exp_month() && $token->get_exp_year() ? $token->get_exp_date() : '';
			}

			$tokens[ $token_id ]['default'] = $token->is_default();

			// Parse against the editor field IDs so we don't have to isset throughout the HTML
			$tokens[ $token_id ] = wp_parse_args( $tokens[ $token_id ], array_fill_keys( array_keys( $this->get_fields() ), '' ) );
		}

		return $tokens;
	}


	/**
	 * Get the editor title.
	 *
	 * @since 4.3.0
	 * @return string
	 */
	protected function get_title() {

		$title = $this->get_gateway()->get_title();

		// Append the environment name if there are multiple
		if ( $this->get_gateway()->get_plugin()->get_admin_user_handler()->has_multiple_environments() ) {
			$title .= ' ' . sprintf( __( '(%s)', 'woocommerce-plugin-framework' ), $this->get_gateway()->get_environment_name() );
		}

		/**
		 * Filter the token editor name.
		 *
		 * @since 4.3.0
		 * @param string $title the editor title
		 * @param \SV_WC_Payment_Gateway_Admin_Payment_Token_Editor $editor the editor object
		 */
		return apply_filters( 'wc_payment_gateway_' . $this->get_gateway()->get_id() . '_token_editor_title', $title, $this );
	}


	/**
	 * Get the editor columns.
	 *
	 * @since 4.3.0
	 * @return array
	 */
	protected function get_columns() {

		$fields  = $this->get_fields();
		$columns = array();

		foreach ( $fields as $field_id => $field ) {
			$columns[ $field_id ] = isset( $field['label'] ) ? $field['label'] : '';
		}

		$columns['default'] = __( 'Default', 'woocommerce-plugin-framework' );
		$columns['actions'] = '';

		/**
		 * Filter the admin token editor columns.
		 *
		 * @since 4.3.0
		 * @param array $columns
		 * @param \SV_WC_Payment_Gateway_Admin_Payment_Token_Editor $editor the editor object
		 */
		$columns = apply_filters( 'wc_payment_gateway_' . $this->get_gateway()->get_id() . '_token_editor_columns', $columns, $this );

		return $columns;
	}


	/**
	 * Get the editor fields.
	 *
	 * @since 4.3.0
	 * @return array
	 */
	protected function get_fields( $type = '' ) {

		if ( ! $type ) {
			$type = $this->get_gateway()->get_payment_type();
		}

		switch ( $type ) {

			case 'credit-card' :

				// Define the credit card fields
				$fields = array(
					'id' => array(
						'label'    => __( 'Token ID', 'woocommerce-plugin-framework' ),
						'editable' => ! $this->get_gateway()->get_api()->supports_get_tokenized_payment_methods(),
						'required' => true,
					),
					'card_type' => array(
						'label'   => __( 'Card Type', 'woocommerce-plugin-framework' ),
						'type'    => 'select',
						'options' => $this->get_card_type_options(),
					),
					'last_four' => array(
						'label'   => __( 'Last Four', 'woocommerce-plugin-framework' ),
						'attributes' => array(
							'pattern'   => '[0-9]{4}',
							'maxlength' => 4,
						),
					),
					'expiry'    => array(
						'label' => __( 'Expiration (MM/YY)', 'woocommerce-plugin-framework' ),
						'attributes' => array(
							'placeholder' => 'MM/YY',
							'pattern'     => '(0[1-9]|1[012])[- /.]\d\d',
							'maxlength'   => 5,
						),
					),
				);

			break;

			case 'echeck' :

				// Define the echeck fields
				$fields = array(
					'id' => array(
						'label'    => __( 'Token ID', 'woocommerce-plugin-framework' ),
						'editable' => ! $this->get_gateway()->get_api()->supports_get_tokenized_payment_methods(),
						'required' => true,
					),
					'account_type' => array(
						'label'   => __( 'Account Type', 'woocommerce-plugin-framework' ),
						'type'    => 'select',
						'options' => array(
							'checking' => __( 'Checking', 'woocommerce-plugin-framework' ),
							'savings'  => __( 'Savings', 'woocommerce-plugin-framework' ),
						),
					),
					'last_four' => array(
						'label'   => __( 'Last Four', 'woocommerce-plugin-framework' ),
						'attributes' => array(
							'pattern'   => '[0-9]{4}',
							'maxlength' => 4,
						),
					),
				);

			break;

			default :
				$fields = array();
		}

		// Parse each field against the defaults
		foreach ( $fields as $field_id => $field ) {

			$fields[ $field_id ] = wp_parse_args( $field, array(
				'label'      => '',
				'type'       => 'text',
				'attributes' => array(),
				'editable'   => true,
				'required'   => false,
			) );
		}

		/**
		 * Filter the admin token editor fields.
		 *
		 * @since 4.3.0
		 * @param array $fields
		 * @param \SV_WC_Payment_Gateway_Admin_Payment_Token_Editor $editor the editor object
		 */
		$fields = apply_filters( 'wc_payment_gateway_' . $this->get_gateway()->get_id() . '_token_editor_fields', $fields, $this );

		return $fields;
	}


	/**
	 * Get the token payment type.
	 *
	 * @since 4.3.0
	 * @return string
	 */
	protected function get_payment_type() {

		return str_replace( '-', '_', $this->get_gateway()->get_payment_type() );
	}


	/**
	 * Get the credit card type field options.
	 *
	 * @since 4.3.0
	 * @return array
	 */
	protected function get_card_type_options() {

		$card_types = $this->get_gateway()->get_card_types();
		$options    = array();

		foreach ( $card_types as $card_type ) {

			$card_type = SV_WC_Payment_Gateway_Helper::normalize_card_type( $card_type );

			$options[ $card_type ] = SV_WC_Payment_Gateway_Helper::payment_type_to_name( $card_type );
		}

		return $options;
	}


	/**
	 * Get the HTML name for the token fields.
	 *
	 * @since 4.3.0
	 * @return string
	 */
	protected function get_input_name() {

		return 'wc_payment_gateway_' . $this->get_gateway()->get_id() . '_tokens';
	}


	/**
	 * Get the available editor actions.
	 *
	 * @since 4.3.0
	 * @return array
	 */
	protected function get_actions() {

		$actions = array();

		if ( $this->get_gateway()->get_api()->supports_get_tokenized_payment_methods() ) {
			$actions['refresh'] = __( 'Refresh', 'woocommerce-plugin-framework' );
		} else {
			$actions['add-new'] = __( 'Add New', 'woocommerce-plugin-framework' );
		}

		$actions['save'] = __( 'Save', 'woocommerce-plugin-framework' );

		/**
		 * Filter the payment token editor actions.
		 *
		 * @since 4.3.0
		 * @param array $actions the actions
		 * @param \SV_WC_Payment_Gateway_Admin_Payment_Token_Editor $editor the editor object
		 */
		return apply_filters( 'wc_payment_gateway_' . $this->get_gateway()->get_id() . '_token_editor_actions', $actions, $this );
	}


	/**
	 * Get the available token actions.
	 *
	 * @since 4.3.0
	 * @return array
	 */
	protected function get_token_actions() {

		$actions = array(
			'remove' => __( 'Remove', 'woocommerce-plugin-framework' ),
		);

		/**
		 * Filter the token actions.
		 *
		 * @since 4.3.0
		 * @param array $actions the token actions
		 * @param \SV_WC_Payment_Gateway_Admin_Payment_Token_Editor $editor the editor object
		 */
		return apply_filters( 'wc_payment_gateway_' . $this->get_gateway()->get_id() . '_token_editor_token_actions', $actions, $this );
	}


	/**
	 * Get the gateway object.
	 *
	 * @since 4.3.0
	 * @return \SV_WC_Payment_Gateway_Direct the gateway object
	 */
	protected function get_gateway() {
		return $this->gateway;
	}
}
