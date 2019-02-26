<?php

/**
 * @author OnTheGo Systems
 */
class WPML_Notice {
	private $display_callbacks = array();
	private $id;
	private $text;
	private $collapsed_text;
	private $group             = 'default';
	private $restricted_to_user_ids = array();

	private $actions            = array();
	/**
	 * @see \WPML_Notice::set_css_class_types
	 * @var array
	 */
	private $css_class_types                = array();
	private $css_classes                    = array();
	private $dismissible                    = false;
	private $exclude_from_pages             = array();
	private $hideable                       = false;
	private $collapsable                    = false;
	private $restrict_to_pages              = array();
	private $restrict_to_page_prefixes      = array();
	private $restrict_to_screen_ids         = array();
	private $hide_if_notice_exists          = null;
	private $dismissible_for_different_text = true;

	private $default_group_name = 'default';

	private $capabilities = array();

	private $dismiss_reset = false;

	/*
	 * @var bool
	 * @since 4.1.0
	 */
	private $flash = false;

	/**
	 * @var string
	 */
	private $nonce_action;

	/**
	 * WPML_Admin_Notification constructor.
	 *
	 * @param int|string $id
	 * @param string     $text
	 * @param string     $group
	 */
	public function __construct( $id, $text, $group = 'default' ) {
		$this->id    = $id;
		$this->text  = $text;
		$this->group = $group ? $group : $this->default_group_name;
	}

	public function add_action( WPML_Notice_Action $action ) {
		$this->actions[] = $action;

		if ( $action->can_dismiss() ) {
			$this->dismissible = true;
		}
		if ( ! $action->can_dismiss_different_text() ) {
			$this->dismissible_for_different_text = false;
		}
		if ( $action->can_hide() ) {
			$this->hideable = true;
		}
	}

	public function add_exclude_from_page( $page ) {
		$this->exclude_from_pages[] = $page;
	}

	public function add_restrict_to_page( $page ) {
		$this->restrict_to_pages[] = $page;
	}

	/** @param int $user_id */
	public function add_user_restriction( $user_id ) {
		$user_id = (int) $user_id;
		$this->restricted_to_user_ids[ $user_id ] = $user_id;
	}

	/** @param int $user_id */
	public function remove_user_restriction( $user_id ) {
		unset( $this->restricted_to_user_ids[ (int) $user_id ] );
	}

	/** @return array */
	public function get_restricted_user_ids() {
		return $this->restricted_to_user_ids;
	}

	/** @return bool */
	public function is_user_restricted() {
		return (bool) $this->restricted_to_user_ids;
	}

	/** @return bool */
	public function is_for_current_user() {
		return ! $this->restricted_to_user_ids
		       || array_key_exists( get_current_user_id(), $this->restricted_to_user_ids );
	}

	/**
	 * @return bool
	 */
	public function is_user_cap_allowed() {
		$user_can = true;
		foreach ( $this->capabilities as $cap ) {
			$user_can = current_user_can( $cap );

			if ( $user_can ) {
				break;
			}
		}

		return $user_can;
	}

	public function can_be_dismissed() {
		return $this->dismissible;
	}

	public function can_be_dismissed_for_different_text() {
		return $this->dismissible_for_different_text;
	}

	public function can_be_hidden() {
		return $this->hideable;
	}

	/**
	 * @return bool
	 */
	public function can_be_collapsed() {
		return $this->collapsable;
	}

	/**
	 * As the notice is supposed to be serialized and stored into the DB,
	 * the callback should be only a function or a static method.
	 *
	 * Before to use a callback, please check the existing options with:
	 * - add_exclude_from_page
	 * - add_restrict_to_page
	 * - add_user_restriction
	 * - add_capability_check
	 *
	 * @param callable $callback
	 */
	public function add_display_callback( $callback ) {
		if ( ! is_callable( $callback ) ) {
			throw new UnexpectedValueException( '\WPML_Notice::add_display_callback expects a callable', 1 );
		}
		$this->display_callbacks[] = $callback;
	}

	public function add_capability_check( array $cap ) {
		$this->capabilities = $cap;
	}

	public function get_display_callbacks() {
		return $this->display_callbacks;
	}

	public function get_actions() {
		return $this->actions;
	}

	public function get_css_classes() {
		return $this->css_classes;
	}

	/**
	 * @param string|array $css_classes
	 */
	public function set_css_classes( $css_classes ) {
		if ( ! is_array( $css_classes ) ) {
			$css_classes = explode( ' ', $css_classes );
		}
		$this->css_classes = $css_classes;
	}

	public function get_exclude_from_pages() {
		return $this->exclude_from_pages;
	}

	/**
	 * @return string
	 */
	public function get_group() {
		return $this->group;
	}

	/**
	 * @return int|string
	 */
	public function get_id() {
		return $this->id;
	}

	public function set_restrict_to_page_prefixes( array $page_prefixes ) {
		$this->restrict_to_page_prefixes = $page_prefixes;
	}

	/**
	 * @return array
	 */
	public function get_restrict_to_page_prefixes() {
		return $this->restrict_to_page_prefixes;
	}

	public function get_restrict_to_pages() {
		return $this->restrict_to_pages;
	}

	public function set_restrict_to_screen_ids( array $screens ) {
		$this->restrict_to_screen_ids = $screens;
	}

	/**
	 * @return array
	 */
	public function get_restrict_to_screen_ids() {
		return $this->restrict_to_screen_ids;
	}

	public function get_nonce_action() {
		return $this->nonce_action;
	}

	/**
	 * @return string
	 */
	public function get_text() {
		$notice = array( 'id' => $this->get_id(), 'group' => $this->get_group() );
		$this->text = apply_filters( 'wpml_notice_text', $this->text, $notice );
		return $this->text;
	}

	public function get_css_class_types() {
		return $this->css_class_types;
	}

	/**
	 * @return string
	 */
	public function get_collapsed_text() {
		return $this->collapsed_text;
	}

	/**
	 * Use this to set the look of the notice.
	 * WordPress recognize these values:
	 * - notice-error
	 * - notice-warning
	 * - notice-success
	 * - notice-info
	 * You can use the above values with or without the "notice-" prefix:
	 * the prefix will be added automatically in the HTML, if missing.
	 * @see https://codex.wordpress.org/Plugin_API/Action_Reference/admin_notices for more details
	 *
	 * @param string|array $types Accepts either a space separated values string, or an array of values.
	 */
	public function set_css_class_types( $types ) {
		if ( ! is_array( $types ) ) {
			$types = explode( ' ', $types );
		}
		$this->css_class_types = $types;
	}

	/**
	 * @param bool $dismissible
	 */
	public function set_dismissible( $dismissible ) {
		$this->dismissible = $dismissible;
	}

	public function set_exclude_from_pages( array $pages ) {
		$this->exclude_from_pages = $pages;
	}

	public function set_hide_if_notice_exists( $notice_id, $notice_group = null ) {
		$this->hide_if_notice_exists = array(
			'id'    => $notice_id,
			'group' => $notice_group,
		);
	}

	public function get_hide_if_notice_exists( ) {
		return $this->hide_if_notice_exists;
	}

	/**
	 * @param bool $hideable
	 */
	public function set_hideable( $hideable ) {
		$this->hideable = $hideable;
	}

	/**
	 * @param bool $collapsable
	 */
	public function set_collapsable( $collapsable ) {
		$this->collapsable = $collapsable;
	}

	/**
	 * @param string $action
	 */
	public function set_nonce_action( $action ) {
		$this->nonce_action = $action;
	}

	/**
	 * @param string $collapsed_text
	 */
	public function set_collapsed_text( $collapsed_text ) {
		$this->collapsed_text = $collapsed_text;
	}

	public function set_restrict_to_pages( array $pages ) {
		$this->restrict_to_pages = $pages;
	}

	public function reset_dismiss() {
		$this->dismiss_reset = true;
	}

	public function must_reset_dismiss() {
		return $this->dismiss_reset;
	}

	public function is_different( WPML_Notice $other_notice ) {
		return serialize( $this ) !== serialize( $other_notice );
	}

	/**
	 * @param bool $flash
	 * @since 4.1.0
	 */
	public function set_flash( $flash = true ){
		$this->flash = (bool) $flash;
	}

	/**
	 * @return bool
	 * @since 4.1.0
	 */
	public function is_flash(){
		return $this->flash;
	}

}
