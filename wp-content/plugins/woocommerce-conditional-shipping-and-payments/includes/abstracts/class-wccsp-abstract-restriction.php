<?php
/**
 * WC_CSP_Restriction class
 *
 * @author   SomewhereWarm <info@somewherewarm.gr>
 * @package  WooCommerce Conditional Shipping and Payments
 * @since    1.0.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Abstract Restriction class.
 *
 * @class   WC_CSP_Restriction
 * @version 1.2.0
 */
class WC_CSP_Restriction extends WC_Settings_API {

	/** @var string Unique ID for the Restriction - must be set */
	var $id;

	/** @var string Restriction title */
	var $title;

	/** @var string Restriction description */
	var $description;

	/**
	 * @var array Restriction types supported
	 *
	 * If the restriction needs to hook itself into 'woocommerce_add_to_cart_validation', 'woocommerce_check_cart_items', 'woocommerce_update_cart_validation', or 'woocommerce_after_checkout_validation',
	 * if must declare support for the 'add-to-cart', 'cart', 'cart-update', or 'checkout' validation types
	 * and implement the 'WC_CSP_Add_To_Cart_Restriction', 'WC_CSP_Cart_Restriction', 'WC_CSP_Update_Cart_Restriction', or 'WC_CSP_Checkout_Restriction' interfaces.
	 */
	var $validation_types;

	/** @var array Restriction has options in product write panels */
	var $has_admin_product_fields;

	/** @var array Restriction has global options */
	var $has_admin_global_fields;

	/** @var array Restriction supports multiple rules */
	var $supports_multiple;

	/** @var array Restriction conditions */
	var $conditions;

	/**
	 * Restriction title.
	 *
	 * @return string
	 */
	public function get_title() {
		return $this->title;
	}

	/**
	 * Restriction description.
	 *
	 * @return string
	 */
	public function get_description() {
		return $this->description;
	}

	/**
	 * Shop hook(s) where the restriction validates itself (add-to-cart, cart, update-cart, checkout).
	 *
	 * @return array
	 */
	public function get_validation_types() {
		return $this->validation_types;
	}

	/**
	 * If the restriction has options on the product Restrictions write-panel.
	 *
	 * @return boolean
	 */
	public function has_admin_product_fields() {
		return $this->has_admin_product_fields;
	}

	/**
	 * Display options on the product Restrictions write-panel.
	 *
	 * By default expects fields posted inside an indexed array.
	 *
	 * @param  int    $index    restriction fields array index
	 * @param  string $options  metabox options
	 * @return string
	 */
	public function get_admin_product_fields_html( $index, $options ) {
		return false;
	}

	/**
	 * Validate, process and return posted product metabox options.
	 *
	 * By default expects all fields posted inside an indexed array.
	 *
	 * @param  array  $posted
	 * @return array
	 */
	public function process_admin_product_fields( $posted ) {
		return $posted;
	}

	/**
	 * If the restriction has options on the global Restrictions page.
	 *
	 * @return boolean
	 */
	public function has_admin_global_fields() {
		return $this->has_admin_global_fields;
	}

	/**
	 * Display options on the global Restrictions sections.
	 *
	 * By default expects fields posted inside an indexed array.
	 *
	 * @param  int    $index    restriction fields array index
	 * @param  string $options  metabox options
	 * @return string
	 */
	public function get_admin_global_fields_html( $index, $options ) {
		return false;
	}

	/**
	 * Validate, process and return global settings.
	 *
	 * By default expects fields posted inside an indexed array.
	 *
	 * @param  array  $posted_data
	 * @return array
	 */
	public function process_admin_global_fields( $posted_data ) {
		return $posted_data;
	}

	/**
	 * Display metaboxes on the global Restrictions sections.
	 *
	 * @return string
	 */
	public function get_admin_global_metaboxes_html() {

		$global_restrictions = $this->get_global_restriction_data();

		?><tr><td>
		<div id="restrictions_data" class="panel woocommerce_options_panel wc-metaboxes-wrapper postbox">
			<div class="inside">
				<p class="toolbar">
					<select style="display:none;" name="_restriction_type" class="restriction_type">
						<?php
						echo '<option value="' . $this->id . '"></option>';
						?>
					</select>

					<span class="bulk_toggle_wrapper <?php echo empty( $global_restrictions ) ? 'disabled' : '' ; ?>">
						<span class="disabler"></span>
						<a href="#" class="expand_all"><?php _e( 'Expand all', 'woocommerce' ); ?></a>
						<a href="#" class="close_all"><?php _e( 'Close all', 'woocommerce' ); ?></a>
					</span>
				</p>

				<div class="woocommerce_restrictions wc-metaboxes ui-sortable">
					<?php
					if ( ! empty( $global_restrictions ) ) {
						foreach ( $global_restrictions  as $index => $restriction_data ) {
							$this->get_admin_global_metaboxes_content( $index, $restriction_data );
						}
					}
					?>
				</div>

				<p class="toolbar borderless">
					<button id="woocommerce-add-global-restriction" type="button" class="button button-secondary add_restriction"><?php _e( 'Add Restriction', 'woocommerce-conditional-shipping-and-payments' ); ?></button>
				</p>
			</div>
		</div>
		</td></tr><?php
	}

	/**
	 * Get restriction content for admin product metaboxes.
	 *
	 * Product restriction content is always in metaboxes.
	 *
	 * @param  int    $index
	 * @param  array  $options
	 * @param  bool   $ajax
	 * @return str
	 */
	public function get_admin_product_metaboxes_content( $index, $options = array(), $ajax = false ) {

		$restriction_id = $this->id;

		if ( isset( $options[ 'index' ] ) ) {
			$count = $options[ 'index' ] + 1;
		} else {
			$count = $index + 1;
		}

		?>
		<div class="woocommerce_restriction woocommerce_restriction_<?php echo $restriction_id; ?> wc-metabox <?php echo ! $ajax ? 'closed' : ''; ?>" data-restriction_id="<?php echo $restriction_id; ?>" data-index="<?php echo $index; ?>">
			<h3>
				<a href="#" class="remove_row delete"><?php echo __( 'Remove', 'woocommerce' ); ?></a>
				<div class="handlediv" title="<?php echo __( 'Click to toggle', 'woocommerce' ); ?>"></div>
				<strong class="restriction_title"><?php echo sprintf( __( '#<span class="restriction_title_index">%1$s</span> - %2$s: <span class="restriction_title_inner">%3$s</span>', 'woocommerce-conditional-shipping-and-payments' ), $count, $this->get_title(), $this->get_options_description( $options ) ); ?></strong>
			</h3>
			<div class="woocommerce_restriction_data wc-metabox-content" <?php echo ! $ajax ? 'style="display:none;"' : '' ; ?>>
				<input type="hidden" name="restriction[<?php echo $index; ?>][position]" class="position" value="<?php echo $index; ?>"/>
				<input type="hidden" name="restriction[<?php echo $index; ?>][restriction_id]" class="restriction_id" value="<?php echo $restriction_id; ?>"/>
				<?php
				$this->get_admin_product_fields_html( $index, $options );
				do_action( 'woocommerce_csp_admin_product_fields', $this->id, $index, $options );
				?>
			</div>
		</div>
		<?php

	}

	/**
	 * Get restriction content for admin global metaboxes.
	 *
	 * Global restrictions do not necessarily need metaboxes.
	 *
	 * @param  str    $index
	 * @param  array  $options
	 * @param  bool   $ajax
	 * @return str
	 */
	public function get_admin_global_metaboxes_content( $index, $options = array(), $ajax = false ) {

		$restriction_id = $this->id;

		if ( isset( $options[ 'index' ] ) ) {
			$count = $options[ 'index' ] + 1;
		} else {
			$count = $index + 1;
		}

		$state = 'closed';

		if ( $ajax || ( isset( $_GET[ 'view_rule' ] ) && $_GET[ 'view_rule' ] == $index ) ) {
			$state = 'open';
		}

		?>
		<div class="woocommerce_restriction woocommerce_restriction_<?php echo $restriction_id; ?> wc-metabox <?php echo $state; ?>" data-restriction_id="<?php echo $restriction_id; ?>" data-index="<?php echo $index; ?>">
			<h3>
				<a href="#" class="remove_row delete"><?php echo __( 'Remove', 'woocommerce' ); ?></a>
				<div class="handlediv" title="<?php echo __( 'Click to toggle', 'woocommerce' ); ?>"></div>
				<strong class="restriction_title"><?php echo sprintf( __( '#<span class="restriction_title_index">%1$s</span>: <span class="restriction_title_inner">%2$s</span>', 'woocommerce-conditional-shipping-and-payments' ), $count, $this->get_options_description( $options ) ); ?></strong>
			</h3>
			<div class="woocommerce_restriction_data wc-metabox-content" <?php echo $state === 'closed' ? 'style="display:none;"' : '' ; ?>>
				<input type="hidden" name="restriction[<?php echo $index; ?>][position]" class="position" value="<?php echo $index; ?>"/>
				<input type="hidden" name="restriction[<?php echo $index; ?>][restriction_id]" class="restriction_id" value="<?php echo $restriction_id; ?>"/>
				<?php
				$this->get_admin_global_fields_html( $index, $options );
				do_action( 'woocommerce_csp_admin_global_fields', $this->id, $index, $options );
				?>
			</div>
		</div>
		<?php

	}

	/**
	 * Validate, process and return global options as required by 'update_global_restriction_data'.
	 *
	 * By default expects all fields posted inside an indexed 'restriction' array.
	 *
	 * @return array
	 */
	public function process_global_restriction_data() {

		if ( isset( $_POST[ 'restriction' ] ) ) {
			$posted_restrictions_data = $_POST[ 'restriction' ];
		}

		$count            = 0;
		$loop             = 0;
		$restriction_data = array();

		if ( isset( $posted_restrictions_data ) ) {

			uasort( $posted_restrictions_data, array( $this, 'cmp' ) );

			foreach ( $posted_restrictions_data as &$posted_restriction_data ) {

				$posted_restriction_data[ 'index' ] = $loop + 1;

				$processed_data = $this->process_admin_global_fields( $posted_restriction_data );

				if ( $processed_data ) {

					$processed_data                     = apply_filters( 'woocommerce_csp_process_admin_global_fields', $processed_data, $posted_restriction_data, $this->id );
					$processed_data[ 'restriction_id' ] = $this->id;
					$processed_data[ 'index' ]          = $count;

					if ( WC_CSP_Core_Compatibility::is_wc_version_gte_2_6() ) {
						$processed_data[ 'wc_26_shipping' ] = 'yes';
					}

					$restriction_data[ $count ]         = $processed_data;
					$count++;
				}

				$loop++;
			}

			return $restriction_data;
		}

		return false;
	}

	/**
	 * Update global restriction settings.
	 *
	 * All settings are stored in the 'woocommerce_restrictions_global_settings' option by default.
	 *
	 * @return void
	 */
	public function update_global_restriction_data() {

		$restriction_data = get_option( 'wccsp_restrictions_global_settings', array() );

		$processed_data = $this->process_global_restriction_data();

		if ( ! $processed_data ) {
			unset( $restriction_data[ $this->id ] );
		} else {
			$restriction_data[ $this->id ] = $processed_data;
		}

		update_option( 'wccsp_restrictions_global_settings', $restriction_data );
	}

	/**
	 * Sort posted restriction data.
	 */
    public function cmp( $a, $b ) {

	    if ( $a[ 'position' ] == $b[ 'position' ] ) {
	        return 0;
	    }

	    return ( $a[ 'position' ] < $b[ 'position' ] ) ? -1 : 1;
	}

	/**
	 * If the restriction supports multiple rule definitions.
	 * @return boolean
	 */
	public function supports_multiple() {
		return $this->supports_multiple;
	}

	/**
	 * Retrieves product restriction data.
	 *
	 * @param  int|WC_Product  $product
	 * @return array
	 */
	public function get_product_restriction_data( $product ) {

		if ( is_object( $product ) ) {
			$product_id = WC_CSP_Core_Compatibility::get_product_id( $product );
			$product    = $product_id === WC_CSP_Core_Compatibility::get_id( $product ) ? $product : wc_get_product( $product_id );
		} else {
			$product_id = absint( $product );
			$product    = wc_get_product( $product_id );
		}

		$restriction_data = array();

		$disable_product_restrictions = get_option( 'wccsp_restrictions_disable_product', false );

		if ( $disable_product_restrictions === 'yes' ) {
			return $restriction_data;
		}

		$restriction_meta = WC_CSP_Core_Compatibility::is_wc_version_gte_2_7() && $product ? $product->get_meta( '_wccsp_restrictions', true ) : get_post_meta( $product_id, '_wccsp_restrictions', true );

		$restrictions = WC_CSP()->restrictions->maybe_update_restriction_data( $restriction_meta, 'product' );

		if ( $restrictions ) {
			foreach ( $restrictions as $restriction ) {

				if ( $restriction[ 'restriction_id' ] == $this->id ) {
					$restriction_data[] = $restriction;
				}
			}
		}

		return $restriction_data;
	}

	/**
	 * Retrieves global restriction data.
	 *
	 * @return array
	 */
	public function get_global_restriction_data( $debug_active = true ) {

		$restriction_data = array();

		$disable_global_restrictions = get_option( 'wccsp_restrictions_disable_global', false );

		if ( $debug_active && $disable_global_restrictions === 'yes' ) {
			if ( ! is_admin() || ( defined( 'DOING_AJAX' ) && true == DOING_AJAX ) ) {
				return $restriction_data;
			}
		}

		$global_restrictions = WC_CSP()->restrictions->maybe_update_restriction_data( get_option( 'wccsp_restrictions_global_settings', false ), 'global' );

		if ( $global_restrictions && isset( $global_restrictions[ $this->id ] ) ) {
			foreach ( $global_restrictions[ $this->id ] as $restriction ) {
				$restriction_data[] = $restriction;
			}
		}

		return $restriction_data;
	}

	/**
	 * Checks if all conditions of a restriction instance are true.
	 *
	 * @param  array  $restriction_data
	 * @param  array  $args
	 * @return boolean
	 */
	public function check_conditions_apply( $restriction_data, $args = array() ) {

		// Conditions apply if no conditions are defined.
		if ( empty( $restriction_data[ 'conditions' ] ) ) {
			return true;
		}

		$conditions = $restriction_data[ 'conditions' ];

		// Otherwise, all conditions must apply to return true.
		$conditions_apply = true;

		foreach ( $conditions as $condition_key => $condition_data ) {

			if ( ! apply_filters( 'woocommerce_csp_check_condition', WC_CSP()->conditions->check_condition( $condition_data, $args ), $condition_key, $condition_data, $args, $conditions ) ) {
				$conditions_apply = false;
				break;
			}
		}

		return $conditions_apply;
	}

	/**
	 * Compiles a 'resolution' message that describes what steps can be taken to overcome a restriction based on the defined conditions.
	 *
	 * @param  array  $restriction_data
	 * @param  array  $args
	 * @return string
	 */
	public function get_conditions_resolution( $restriction_data, $args = array() ) {

		// Conditions have no resolution if no conditions are defined.
		if ( empty( $restriction_data[ 'conditions' ] ) ) {
			return false;
		}

		$conditions = $restriction_data[ 'conditions' ];

		$resolutions = array();
		$string      = '';

		foreach ( $conditions as $condition_key => $condition_data ) {

			$resolution = apply_filters( 'woocommerce_csp_get_condition_resolution', WC_CSP()->conditions->get_condition_resolution( $condition_data, $args ), $condition_key, $condition_data, $args, $conditions );

			if ( false !== $resolution ) {
				$resolutions[] = $resolution;
			}
		}

		if ( ! empty( $resolutions ) ) {

			if ( count( $resolutions ) == 1 ) {

				return current( $resolutions );

			} else {

				$string = current( $resolutions );

				for ( $i = 1; $i < count( $resolutions ) - 1; $i++ ) {

					/* translators: Used to stitch together a resolution meesage based on a restriction's active conditions */
					$string = sprintf( __( '%1$s, %2$s', 'woocommerce-conditional-shipping-and-payments' ), $string, $resolutions[ $i ] );
				}

				/* translators: Used to stitch together a resolution meesage based on a restriction's active conditions - last condition */
				$string = sprintf( __( '%1$s, or %2$s', 'woocommerce-conditional-shipping-and-payments' ), $string, end( $resolutions ) );

			}

		} else {

			return false;
		}

		return $string;
	}

	/**
	 * Display a short summary of the restriction's settings.
	 *
	 * @param  array  $options
	 * @return string
	 */
	public function get_options_description( $options ) {
		return '';
	}
}
