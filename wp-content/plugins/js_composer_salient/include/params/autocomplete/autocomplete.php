<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

/**
 * Class Vc_AutoComplete
 * Param type 'autocomplete'
 * Used to create input field with predefined or ajax values suggestions.
 * See usage example in bottom of this file.
 * @since 4.4
 */
class Vc_AutoComplete {
	/**
	 * @since 4.4
	 * @var array $settings - param settings
	 */
	protected $settings;
	/**
	 * @since 4.4
	 * @var string $value - current param value (if multiple it is splitted by ',' comma to make array)
	 */
	protected $value;
	/**
	 * @since 4.4
	 * @var string $tag - shortcode name(base)
	 */
	protected $tag;

	/**
	 * @param array $settings - param settings (from vc_map)
	 * @param string $value - current param value
	 * @param string $tag - shortcode name(base)
	 *
	 * @since 4.4
	 */
	public function __construct( $settings, $value, $tag ) {
		$this->tag = $tag;
		$this->settings = $settings;
		$this->value = $value;
	}

	/**
	 * @since 4.4
	 * vc_filter: vc_autocomplete_{shortcode_tag}_{param_name}_render - hook to define output for autocomplete item
	 * @return string
	 */
	public function render() {
		$output = '<div class="vc_autocomplete-field">' .
		          '<ul class="vc_autocomplete' .
		          ( ( isset( $this->settings['settings'], $this->settings['settings']['display_inline'] ) && true === $this->settings['settings']['display_inline'] ) ? ' vc_autocomplete-inline' : '' ) .
		          '">';

		if ( isset( $this->value ) && strlen( $this->value ) > 0 ) {
			$values = explode( ',', $this->value );
			foreach ( $values as $key => $val ) {
				$value = array(
					'value' => trim( $val ),
					'label' => trim( $val ),
				);
				if ( isset( $this->settings['settings'], $this->settings['settings']['values'] ) && ! empty( $this->settings['settings']['values'] ) ) {
					foreach ( $this->settings['settings']['values'] as $data ) {
						if ( trim( $data['value'] ) == trim( $val ) ) {
							$value['label'] = $data['label'];
							break;
						}
					}
				} else {
					// Magic is here. this filter is used to render value correctly ( must return array with 'value', 'label' keys )
					$value = apply_filters( 'vc_autocomplete_' . $this->tag . '_' . $this->settings['param_name'] . '_render', $value, $this->settings, $this->tag );
				}

				if ( is_array( $value ) && isset( $value['value'], $value['label'] ) ) {
					$output .= '<li data-value="' . $value['value'] . '"  data-label="' . $value['label'] . '" data-index="' . $key . '" class="vc_autocomplete-label vc_data"><span class="vc_autocomplete-label">' . $value['label'] . '</span> <a class="vc_autocomplete-remove">&times;</a></li>';
				}
			}
		}

		$output .= '<li class="vc_autocomplete-input"><span role="status" aria-live="polite" class="ui-helper-hidden-accessible"></span><input class="vc_auto_complete_param" type="text" placeholder="' . __( 'Click here and start typing...', 'js_composer' ) . '" value="' . $this->value . '" autocomplete="off"></li>' .
		           '<li class="vc_autocomplete-clear"></li>' .
		           '</ul>';

		$output .= '<input name="' .
		           $this->settings['param_name'] .
		           '" class="wpb_vc_param_value  ' .
		           $this->settings['param_name'] . ' ' .
		           $this->settings['type'] . '_field" type="hidden" value="' . $this->value . '" ' .
		           ( ( isset( $this->settings['settings'] ) && ! empty( $this->settings['settings'] ) ) ? ' data-settings="' . htmlentities( json_encode( $this->settings['settings'] ), ENT_QUOTES, 'utf-8' ) . '" ' : '' ) .
		           ' /></div>';

		return $output;
	}
}

/**
 * @action wp_ajax_vc_get_autocomplete_suggestion - since 4.4 used to hook ajax requests for autocomplete suggestions
 */
add_action( 'wp_ajax_vc_get_autocomplete_suggestion', 'vc_get_autocomplete_suggestion' );
/**
 * @since 4.4
 */
function vc_get_autocomplete_suggestion() {
	vc_user_access()
		->checkAdminNonce()
		->validateDie()
		->wpAny( 'edit_posts', 'edit_pages' )
		->validateDie();

	$query = vc_post_param( 'query' );
	$tag = strip_tags( vc_post_param( 'shortcode' ) );
	$param_name = vc_post_param( 'param' );
	vc_render_suggestion( $query, $tag, $param_name );
}

/**
 * @since 4.4
 *
 * @param $query
 * @param $tag
 * @param $param_name
 *
 * vc_filter: vc_autocomplete_{tag}_{param_name}_callback - hook to get suggestions from ajax. (here you need to hook).
 */
function vc_render_suggestion( $query, $tag, $param_name ) {
	$suggestions = apply_filters( 'vc_autocomplete_' . stripslashes( $tag ) . '_' . stripslashes( $param_name ) . '_callback', $query, $tag, $param_name );
	if ( is_array( $suggestions ) && ! empty( $suggestions ) ) {
		die( json_encode( $suggestions ) );
	}
	die( '' ); // if nothing found..
}

/**
 * Function for rendering param in edit form (add element)
 * Parse settings from vc_map and entered values.
 *
 * @param $settings
 * @param $value
 * @param $tag
 *
 * @since 4.4
 * vc_filter: vc_autocomplete_render_filter - hook to override output of edit for field "autocomplete"
 * @return mixed|void rendered template for params in edit form
 */
function vc_autocomplete_form_field( $settings, $value, $tag ) {

	$auto_complete = new Vc_AutoComplete( $settings, $value, $tag );

	return apply_filters( 'vc_autocomplete_render_filter', $auto_complete->render() );
}

// Some examples
/*	vc_map(
	    array(
			'name'        => __( 'Check autocomplete', 'js_composer' ),
			'base'        => 'vc_some_autocomplete',
			'icon'        => 'icon-wpb-empty_space',
			'category'    => __( 'New Elements', 'js_composer' ),
			'description' => __( 'Something cool and best', 'js_composer' ),
			'params'      => array(
				array(
					'type'        => 'autocomplete',
					'heading'     => __( 'Type a for example', 'js_composer' ),
					'param_name'  => 'ids',
					'settings'    => array(
						'multiple' => true,
						'sortable' => true,
						'min_length' => 1,
						'no_hide' => true, // In UI after select doesn't hide an select list
						'groups' => true, // In UI show results grouped by groups
						'unique_values' => true, // In UI show results except selected. NB! You should manually check values in backend
						'display_inline' => true, // In UI show results inline view
						'values'   => array( // Using key 'values' will disable an AJAX requests on autocomplete input and also any filter for suggestions
							array( 'label' => 'Abrams', 'value' => 1, 'group' => 'category' ),
							array( 'label' => 'Brama', 'value' => 2, 'group' => 'category' ),
							array( 'label' => 'Dron', 'value' => 3, 'group' => 'tags' ),
							array( 'label' => 'Akelloam', 'value' => 4, 'group' => 'tags' ),
							// Label will show when adding
							// Value will saved in input
							// Group only used if groups=>true, this will group data in select dropdown by groups
						),
					),
					'description' => __( '', 'js_composer' ),
				),
			),
		)
    );

	// Or with AJAX suggester:
	See \js_composer\include\classes\vendors\plugins\class-vc-vendor-woocommerce.php
	vc_map( array(
			'name'        => __( 'Product', 'js_composer' ),
			'base'        => 'product',
			'icon'        => 'icon-wpb-woocommerce',
			'category'    => __( 'WooCommerce', 'js_composer' ),
			'description' => __( 'Show a single product by ID or SKU', 'js_composer' ),
			'params'      => array(
				array(
					'type'        => 'autocomplete',
					'heading'     => __( 'Select identificator', 'js_composer' ),
					'param_name'  => 'id',
					'description' => __( 'Input product ID or product SKU or product title to see suggestions', 'js_composer' ),
				),
				array(
					'type'       => 'hidden',
					// This will not show on render, but will be used when defining value for autocomplete
					'param_name' => 'sku',
				),
			)
	) );
		//Filters For autocomplete param:
		//For suggestion: vc_autocomplete_[shortcode_name]_[param_name]_callback
		add_filter( 'vc_autocomplete_product_id_callback', array(
			$this,
			'productIdAutocompleteSuggester'
		), 10, 1 ); // Get suggestion(find). Must return an array
		add_filter( 'vc_autocomplete_product_id_render', array(
			$this,
			'productIdAutocompleteRender'
		), 10, 1 ); // Render exact item. Must return an array (label,value)

	function productIdAutocompleteSuggester($query) -> should proccess your request, and return an multi-dimension associative array with keys 'label', 'value' and if necessary 'group'
	function productIdAutocompleteRender($value) -> should proccess your request (for 1 exact item from "value") and return an associative array with keys 'label','value' and if necessary 'group'
 */
