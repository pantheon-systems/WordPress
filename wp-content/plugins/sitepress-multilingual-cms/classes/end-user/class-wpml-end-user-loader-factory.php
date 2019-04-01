<?php

class WPML_End_User_Loader_Factory implements IWPML_Deferred_Action_Loader, IWPML_Backend_Action_Loader {
	/**
	 * @return WPML_End_User_Loader|null
	 */
	public function create() {
		if ( ! $this->is_end_user_feature_enabled() ) {
			return null;
		}

		$expected_roles = array( 'administrator', 'editor' );
		if ( ! $this->get_user_matching_roles( $expected_roles ) ) {
			return null;
		}

		$disabling_option = new WPML_End_User_Account_Creation_Disabled_Option();
		$disabling_loader = new WPML_End_User_Account_Creation_Disabled( $disabling_option );
		if ( $disabling_option->is_disabled() ) {
			return new WPML_End_User_Loader( array( $disabling_loader ) );
		}

		global $pagenow;

		$container = new WPML_End_User_Dependency_Container();
		$info_loader = new WPML_End_User_Info_Loader( $container );

		$action_execution = new WPML_End_User_Notice_Action_Execution();
		$notice_validator = new WPML_End_User_Notice_Validate( $action_execution );

		$notice_loader = new WPML_End_User_Notice_Loader(
			$notice_validator,
			new WPML_End_User_Notice_Collection( wpml_get_admin_notices(), $this->create_twig_service() )
		);

		$js_loader = new WPML_End_User_JS_Loader(
			$notice_validator,
			new WPML_End_User_Page_Identify( new WPML_WP_API(), $pagenow ),
			new OTGS_Installer_WP_Share_Local_Components_Setting()
		);

		return new WPML_End_User_Loader( array( $info_loader, $notice_loader, $js_loader, $disabling_loader ) );
	}

	public function is_end_user_feature_enabled() {
		return $this->is_site_registered();
	}

	/**
	 * @return WPML_Twig_Template
	 */
	private function create_twig_service() {
		$template_paths   = array(
			WPML_PLUGIN_PATH . '/templates/end-user/',
		);
		$twig_loader      = new Twig_Loader_Filesystem( $template_paths );
		$environment_args = array();
		if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
			$environment_args['debug'] = true;
		}
		$twig         = new Twig_Environment( $twig_loader, $environment_args );
		return new WPML_Twig_Template( $twig );
	}

	public function get_load_action() {
		return 'admin_init';
	}

	/**
	 * @return bool
	 */
	private function is_site_registered() {
		if ( WPML_Installer_Gateway::get_instance()->class_exists() ) {
			return false !== WPML_Installer_Gateway::get_instance()->get_site_key();
		} else {
			/** @link https://onthegosystems.myjetbrains.com/youtrack/issue/wpmlcore-4855 */
			return false;
		}
	}

	/**
	 * @param array $expected_roles
	 * @return array
	 */
	private function get_user_matching_roles( array $expected_roles ) {
		$user = wp_get_current_user();
		$roles = (array) $user->roles;

		return array_intersect( $expected_roles, $roles );
	}
}
