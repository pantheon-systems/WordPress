<?php

abstract class WPML_Custom_Field_Setting extends WPML_TM_User {

	/** @var  string $index */
	private $index;

	/**
	 * WPML_Custom_Field_Setting constructor.
	 *
	 * @param TranslationManagement $tm_instance
	 * @param string                $index
	 */
	public function __construct( &$tm_instance, $index ) {
		parent::__construct( $tm_instance );
		$this->index = $index;
	}

	/**
	 * @return bool true if the custom field setting is given by a setting in
	 *              a wpml-config.xml
	 */
	public function is_read_only() {

		return in_array(
			$this->index,
			$this->tm_instance->settings[ $this->get_array_setting_index( 'readonly_config' ) ],
			true );
	}

	/**
	 * @return bool
	 */
	public function is_unlocked() {

		return isset( $this->tm_instance->settings[ $this->get_unlocked_setting_index() ][ $this->index ] ) &&
		     (bool) $this->tm_instance->settings[ $this->get_unlocked_setting_index() ][ $this->index ];
	}

	/**
	 * @return bool
	 */
	public function excluded() {

		return in_array( $this->index, $this->get_excluded_keys() ) ||
		       ( $this->is_read_only() &&
		         $this->status() === WPML_IGNORE_CUSTOM_FIELD &&
		         ! $this->is_unlocked()
		       );
	}

	public function status() {
		$state_index = $this->get_state_array_setting_index();
		if ( ! isset( $this->tm_instance->settings[ $state_index ][ $this->index ] ) ) {
			$this->tm_instance->settings[ $state_index ][ $this->index ] = WPML_IGNORE_CUSTOM_FIELD;
		}

		return (int) $this->tm_instance->settings[ $state_index ][ $this->index ];
	}

	public function make_read_only() {
		$ro_index                                   = $this->get_array_setting_index( 'readonly_config' );
		$this->tm_instance->settings[ $ro_index ][] = $this->index;
		$this->tm_instance->settings[ $ro_index ]   = array_unique( $this->tm_instance->settings[ $ro_index ] );
	}

	public function set_to_copy() {
		$this->set_state( WPML_COPY_CUSTOM_FIELD );
	}

	public function set_to_copy_once() {
		$this->set_state( WPML_COPY_ONCE_CUSTOM_FIELD );
	}

	public function set_to_translatable() {
		$this->set_state( WPML_TRANSLATE_CUSTOM_FIELD );
	}

	public function set_to_nothing() {
		$this->set_state( WPML_IGNORE_CUSTOM_FIELD );
	}
	
	public function set_editor_style( $style ) {
		$this->tm_instance->settings[ $this->get_array_setting_index( 'editor_style' ) ][ $this->index ] = $style;
	}
	
	public function get_editor_style() {
		$setting = $this->get_array_setting_index( 'editor_style' );
		return isset( $this->tm_instance->settings[ $setting ][ $this->index ] ) ? $this->tm_instance->settings[ $setting ][ $this->index ] : '';
	}

	public function set_editor_label( $label ) {
		$this->tm_instance->settings[ $this->get_array_setting_index( 'editor_label' ) ][ $this->index ] = $label;
	}

	public function get_editor_label() {
		$setting = $this->get_array_setting_index( 'editor_label' );
		return isset( $this->tm_instance->settings[ $setting ][ $this->index ] ) ? $this->tm_instance->settings[ $setting ][ $this->index ] : '';
	}

	public function set_editor_group( $group ) {
		$this->tm_instance->settings[ $this->get_array_setting_index( 'editor_group' ) ][ $this->index ] = $group;
	}

	public function get_editor_group() {
		$setting = $this->get_array_setting_index( 'editor_group' );

		return isset( $this->tm_instance->settings[ $setting ][ $this->index ] ) ? $this->tm_instance->settings[ $setting ][ $this->index ] : '';
	}

	public function set_translate_link_target( $state, $sub_fields ) {
		if ( isset( $sub_fields[ 'value' ] ) ) {
			// it's a single sub field
			$sub_fields = array( $sub_fields );
		}
		$this->tm_instance->settings[ $this->get_array_setting_index( 'translate_link_target' ) ][ $this->index ] = array( 'state' => $state, 'sub_fields' => $sub_fields );
	}
	
	public function is_translate_link_target() {
		$array_index = $this->get_array_setting_index( 'translate_link_target' );
		return isset( $this->tm_instance->settings[ $array_index ][ $this->index ] ) ?
					( $this->tm_instance->settings[ $array_index ][ $this->index ][ 'state' ] ||
					  $this->get_translate_link_target_sub_fields() ) :
					false;

	}

	public function get_translate_link_target_sub_fields() {
		$array_index = $this->get_array_setting_index( 'translate_link_target' );
		return isset( $this->tm_instance->settings[ $array_index ][ $this->index ][ 'sub_fields' ] ) ?
					$this->tm_instance->settings[ $array_index ][ $this->index ][ 'sub_fields' ] :
					array();
	}

	public function set_convert_to_sticky( $state ) {
		$this->tm_instance->settings[ $this->get_array_setting_index( 'convert_to_sticky' ) ][ $this->index ] = $state;
	}

	public function is_convert_to_sticky() {
		$array_index = $this->get_array_setting_index( 'convert_to_sticky' );
		return isset( $this->tm_instance->settings[ $array_index ][ $this->index ] ) ?
					$this->tm_instance->settings[ $array_index ][ $this->index ] :
					false;
	}

	public function set_encoding( $encoding ) {
		$this->tm_instance->settings[ $this->get_array_setting_index( 'encoding' ) ][ $this->index ] = $encoding;
	}

	public function get_encoding() {
		$setting = $this->get_array_setting_index( 'encoding' );
		return isset( $this->tm_instance->settings[ $setting ][ $this->index ] ) ? $this->tm_instance->settings[ $setting ][ $this->index ] : '';
	}

	/**
	 * @param array $whitelist
	 */
	public function set_attributes_whitelist( $whitelist ) {
		if ( ! is_array( $whitelist ) ) {
			throw new InvalidArgumentException( '$whitelist should be an array.' );
		}
		$this->tm_instance->settings[ $this->get_array_setting_index( 'attributes_whitelist' ) ][ $this->index ] = $whitelist;
	}

	public function get_attributes_whitelist() {
		$setting = $this->get_array_setting_index( 'attributes_whitelist' );
		return isset( $this->tm_instance->settings[ $setting ][ $this->index ] ) ? $this->tm_instance->settings[ $setting ][ $this->index ] : array();
	}

	private function set_state( $state ) {
		$this->tm_instance->settings[ $this->get_state_array_setting_index() ][ $this->index ] = $state;
	}

	/**
	 * @return string
	 */
	private function get_array_setting_index( $index ) {
		return $this->get_setting_prefix() . $index;
	}

	/**
	 * @return string
	 */
	protected abstract function get_state_array_setting_index();

	protected abstract function get_unlocked_setting_index();
	
	/**
	 * @return  string[]
	 */
	protected abstract function get_excluded_keys();

	/**
	 * @return string
	 */
	protected abstract function get_setting_prefix();

}