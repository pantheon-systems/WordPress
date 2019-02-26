<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

/**
 * @param $settings
 * @param $value
 *
 * @since 4.2
 * @return string
 */
function vc_sorted_list_form_field( $settings, $value ) {
	return '<div class="vc_sorted-list">'
	       . '<input name="' . $settings['param_name'] . '" class="wpb_vc_param_value  ' . $settings['param_name'] . ' ' . $settings['type'] . '_field" type="hidden" value="' . $value . '" />'
	       . '<div class="vc_sorted-list-toolbar">' . vc_sorted_list_parts_list( $settings['options'] ) . '</div>'
	       . '<ul class="vc_sorted-list-container"></ul>'
	       . '</div>';
}

/**
 * Teaser box meta box generator for post's edit page.
 * @since 4.2
 * The instance of this class is called from
 */
class Vc_Teaser_Box {
	/**
	 * @since 4.2
	 * @var string
	 */
	protected static $meta_data_name = 'vc_teaser';

	/**
	 * @since 4.4 (code inspection)
	 * @var bool
	 */
	public $teaser_data = false;

	/**
	 * Add action hook to create Meta box. On admin_init jsComposerEditPage method is called.
	 * @since 4.2
	 */
	public function init() {
		add_action( 'admin_init', array( $this, 'jsComposerEditPage' ), 6 );
	}

	/**
	 * Calls add_meta_box function for generating
	 * @since 4.2
	 */
	public function jsComposerEditPage() {
		$pt_array = vc_editor_post_types();
		foreach ( $pt_array as $pt ) {
			add_meta_box( 'vc_teaser', __( 'VC: Custom Teaser', 'js_composer' ), array(
				$this,
				'outputTeaser',
			), $pt, 'side' );
		}
		add_action( 'save_post', array( $this, 'saveTeaserMetaBox' ) );
	}

	/**
	 * Get teaser box data from database.
	 *
	 * @param $name
	 * @param bool $id
	 *
	 * @since 4.2
	 * @return string
	 */
	public function getTeaserData( $name, $id = false ) {
		if ( false === $id ) {
			$id = get_the_ID();
		}
		$this->teaser_data = get_post_meta( $id, self::$meta_data_name, true );

		return isset( $this->teaser_data[ $name ] ) ? $this->teaser_data[ $name ] : '';
	}

	/**
	 * Outputs teaser box html content.
	 *
	 * @since 4.2
	 */
	public function outputTeaser() {
		wp_enqueue_script( 'wpb_jscomposer_teaser_js' );
		wp_localize_script( 'wpb_jscomposer_teaser_js', 'i18nVcTeaser', array(
			'empty_title' => __( 'Empty title', 'js_composer' ),
			'text_label' => __( 'Text', 'js_composer' ),
			'image_label' => __( 'Image', 'js_composer' ),
			'title_label' => __( 'Title', 'js_composer' ),
			'link_label' => __( 'Link', 'js_composer' ),
			'text_text' => __( 'Text', 'js_composer' ),
			'text_excerpt' => __( 'Excerpt', 'js_composer' ),
			'text_custom' => __( 'Custom', 'js_composer' ),
			'image_featured' => __( 'Featered', 'js_composer' ),
			'image_custom' => __( 'Custom', 'js_composer' ),
			'link_label_text' => __( 'Link text', 'js_composer' ),
			'no_link' => __( 'No link', 'js_composer' ),
			'link_post' => __( 'Link to post', 'js_composer' ),
			'link_big_image' => __( 'Link to big image', 'js_composer' ),
			'add_custom_image' => __( 'Add custom image', 'js_composer' ),
		) );
		$output = '<div class="vc_teaser-switch"><label><input type="checkbox" name="' . self::$meta_data_name . '[enable]" value="1" id="vc_teaser-checkbox"' . ( '1' === $this->getTeaserData( 'enable' ) ? ' checked="true"' : '' ) . '> ' . __( 'Enable custom teaser', 'js_composer' ) . '</label></div>';
		$output .= '<input type="hidden" name="' . self::$meta_data_name . '[data]" class="vc_teaser-data-field" value="' . htmlspecialchars( $this->getTeaserData( 'data' ) ) . '">';
		$output .= '<div class="vc_teaser-constructor-hint">';
		$output .= '<p>' . __( 'Customize teaser block design to overwrite default settings used in "Carousel" content element.', 'js_composer' ) . '</p>';
		$output .= '</div>';
		$output .= '<div class="vc_teaser-constructor">';
		$output .= '<div class="vc_toolbar"></div>';
		$output .= '<div class="clear vc_teaser-list"></div>';
		$output .= '<div class="vc_teaser_loading_block" style="display: none;">';
		$output .= '<img src="' . get_site_url() . '/wp-admin/images/wpspin_light.gif" /></div>';
		$output .= '<div class="vc_teaser-footer"><label>Background color</label><br/><input type="text" name="' . self::$meta_data_name . '[bgcolor]" value="' . htmlspecialchars( $this->getTeaserData( 'bgcolor' ) ) . '" class="vc_teaser-bgcolor"></div>';
		$output .= '</div>';
		require_once vc_path_dir( 'TEMPLATES_DIR', 'teaser.html.php' );
		echo $output;
	}

	/**
	 * @param $post_id
	 *
	 * @since 4.2
	 */
	public function saveTeaserMetaBox( $post_id ) {
		if ( isset( $_POST[ self::$meta_data_name ] ) ) {
			$options = isset( $_POST[ self::$meta_data_name ] ) ? $_POST[ self::$meta_data_name ] : '';
			update_post_meta( (int) $post_id, self::$meta_data_name, $options );
		}
	}
}

/**
 * @param $list
 *
 * @since 4.2
 * @return string
 */
function vc_sorted_list_parts_list( $list ) {
	$output = '';
	foreach ( $list as $control ) {
		$output .= '<div class="vc_sorted-list-checkbox"><label><input type="checkbox" name="vc_sorted_list_element" value="' . $control[0] . '" data-element="' . $control[0] . '" data-subcontrol="' . ( count( $control ) > 1 ? htmlspecialchars( json_encode( array_slice( $control, 2 ) ) ) : '' ) . '"> <span>' . htmlspecialchars( $control[1] ) . '</span></label></div>';
	}

	return $output;
}

/**
 * @param $value
 *
 * @since 4.2
 * @return array
 */
function vc_sorted_list_parse_value( $value ) {
	$data = array();
	$split = preg_split( '/\,/', $value );
	foreach ( $split as $v ) {
		$v_split = array_map( 'rawurldecode', preg_split( '/\|/', $v ) );
		if ( count( $v_split ) > 0 ) {
			$data[] = array( $v_split[0], count( $v_split ) > 1 ? array_slice( $v_split, 1 ) : array() );
		}
	}

	return $data;
}

global $vc_teaser_box;
$vc_teaser_box = new Vc_Teaser_Box();

/**
 * @param $attributes
 *
 * @since 4.2
 * @return mixed
 */
function vc_add_teaser_box_generator( $attributes ) {
	global $vc_teaser_box;
	$vc_teaser_box->init();

	return $attributes;
}

/* nectar addition */ 
//add_filter( 'vc_mapper_attribute_sorted_list', 'vc_add_teaser_box_generator' );
/* nectar addition end */ 
