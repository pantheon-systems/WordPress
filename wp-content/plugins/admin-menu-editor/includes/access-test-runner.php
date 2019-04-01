<?php

class ameAccessTestRunner implements ArrayAccess {
	const TEST_DATA_META_KEY = 'ws_ame_access_test_data';

	/**
	 * @var WPMenuEditor
	 */
	private $menuEditor;

	private $get = array();

	private $test_menu = null;
	private $test_target_item = null;
	private $test_target_parent = null;
	private $test_relevant_role = null;

	private $original_wp_die_handler = null;
	private $access_test_results = array();

	public function __construct($menuEditor, $queryParameters) {
		$this->menuEditor = $menuEditor;
		$this->get = $queryParameters;

		add_filter('admin_menu_editor-script_data', array($this, 'addEditorScriptData'));

		add_action('wp_ajax_ws_ame_set_test_configuration', array($this, 'ajax_set_test_configuration'));
		add_action('set_current_user', array($this, 'init_access_test'));
	}

	public function addEditorScriptData($scriptData) {
		$scriptData = array_merge(
			$scriptData,
			array(
				'setTestConfigurationNonce' => wp_create_nonce('ws_ame_set_test_configuration'),
				'testAccessNonce'           => wp_create_nonce('ws_ame_test_access'),
			)
		);
		return $scriptData;
	}

	public function ajax_set_test_configuration() {
		check_ajax_referer('ws_ame_set_test_configuration');
		if ( !$this->menuEditor->current_user_can_edit_menu() ) {
			exit($this->menuEditor->json_encode(array(
				'error' => 'You don\'t have permission to test menu settings.',
			)));
		}

		$post = $this->menuEditor->get_post_params();
		$menuData = strval($post['data']);

		$metaId = add_user_meta(get_current_user_id(), self::TEST_DATA_META_KEY, wp_slash($menuData), false);
		if ( $metaId === false ) {
			exit($this->menuEditor->json_encode(array(
				'error' => 'Failed to store test data. add_user_meta() returned FALSE.',
			)));
		}

		exit($this->menuEditor->json_encode(array(
			'success' => true,
			'meta_id' => $metaId,
		)));
	}

	public function init_access_test() {
		//We want to do this only once per page load: specifically, when WP authenticates
		//the user at the start of the request.
		static $is_user_already_set = false;
		if ( $is_user_already_set || $this->menuEditor->is_access_test || did_action('init') ) {
			return;
		}
		$is_user_already_set = true;

		if (
			!isset(
				$this->get['ame-test-menu-access-as'],
				$this->get['ame-test-target-item']
			)
			|| !check_admin_referer('ws_ame_test_access')
		) {
			return;
		}


		$configurations = get_user_meta(get_current_user_id(), self::TEST_DATA_META_KEY, false);
		if ( empty($configurations) ) {
			exit('Error: Test data not found.');
		}

		//Use the most recent config. It's usually the last one.
		$json = array_pop($configurations);
		//Clean up the database.
		delete_user_meta(get_current_user_id(), self::TEST_DATA_META_KEY, wp_slash($json));

		try {
			$test_menu = ameMenu::load_json($json);
		} catch (InvalidMenuException $e) {
			exit($e->getMessage());
		}
		$this->test_menu = $test_menu;

		$user = get_user_by('login', strval($this->get['ame-test-menu-access-as']));
		if ( !$user ) {
			exit('Error: User not found.');
		}

		//Everything looks good, proceed with the test.
		$this->menuEditor->is_access_test = true;

		$this->access_test_results = array();
		$this->test_target_item = strval($this->get['ame-test-target-item']);
		$this->test_target_parent = ameUtils::get($this->get, 'ame-test-target-parent', null);
		$this->test_relevant_role = ameUtils::get($this->get, 'ame-test-relevant-role', null);

		if ( $this->test_target_parent === '' ) {
			$this->test_target_parent = null;
		}
		if ( $this->test_relevant_role === null ) {
			$this->test_relevant_role = null;
		}

		wp_set_current_user($user->ID, $user->user_login);

		$this->menuEditor->set_plugin_option('security_logging_enabled', true);
		add_action('admin_print_scripts', array($this, 'output_access_test_results'));
		add_filter('wp_die_handler', array($this, 'replace_die_handler_for_access_test'), 25, 1);
	}

	public function output_access_test_results() {
		echo $this->get_access_test_result_script();
	}

	private function get_access_test_result_script() {
		$response = array_merge(
			$this->access_test_results,
			array(
				'securityLog' => $this->menuEditor->get_security_log(),
			)
		);

		$script = '<script type="text/javascript">
			window.parent.postMessage((' . $this->menuEditor->json_encode($response) . '), "*");
		</script>';
		return $script;
	}

	public function replace_die_handler_for_access_test($callback = null) {
		$this->original_wp_die_handler = $callback;
		return array($this, 'die_during_an_access_test');
	}

	public function die_during_an_access_test($message, $title = '', $args = array()) {
		if ( $this->original_wp_die_handler ) {
			$script = $this->get_access_test_result_script();
			if ( $message instanceof WP_Error ) {
				$message->add('ame-access-test-response', '[Access test]' . $script);
			} else if ( is_string($message) ) {
				$message .= $script;
			}

			call_user_func($this->original_wp_die_handler, $message, $title, $args);
		} else {
			exit('Unexpected error: wp_die() was called but there is no default handler.');
		}
	}

	private function find_target_menu_item($items, $item_file, $parent_file = null, $current_parent = null) {
		foreach ($items as $item) {
			$this_file = ameMenuItem::get($item, 'file', null);
			if ( ($this_file === $item_file) && ($parent_file === $current_parent) ) {
				return $item;
			}

			if ( !empty($item['items']) ) {
				$result = $this->find_target_menu_item($item['items'], $item_file, $parent_file, $this_file);
				if ( $result !== null ) {
					return $result;
				}
			}
		}
		return null;
	}

	public function setCurrentMenuItem($menuItem) {
		$this->access_test_results['currentMenuItem'] = $menuItem;

		$this->access_test_results['currentMenuItemIsTarget'] =
			isset($this->access_test_results['currentMenuItem'])
			&& (ameMenuItem::get($this->access_test_results['currentMenuItem'], 'file', null) === $this->test_target_item)
			&& (ameMenuItem::get($this->access_test_results['currentMenuItem'], 'parent', null) === $this->test_target_parent);

		$this->access_test_results['isIdentity'] =
			($this->access_test_results['currentMenuItem'] === $this->access_test_results['targetMenuItem']);
	}

	public function onFinalTreeReady($tree) {
		//Find the target item. It might not be the same as the current item.
		$this->access_test_results['targetMenuItem'] = $this->find_target_menu_item(
			$tree,
			$this->test_target_item,
			$this->test_target_parent
		);
	}


	/**
	 * Whether a offset exists
	 *
	 * @link http://php.net/manual/en/arrayaccess.offsetexists.php
	 * @param mixed $offset <p>
	 * An offset to check for.
	 * </p>
	 * @return boolean true on success or false on failure.
	 * </p>
	 * <p>
	 * The return value will be casted to boolean if non-boolean was returned.
	 * @since 5.0.0
	 */
	public function offsetExists($offset) {
		return array_key_exists($offset, $this->access_test_results);
	}

	/**
	 * Offset to retrieve
	 *
	 * @link http://php.net/manual/en/arrayaccess.offsetget.php
	 * @param mixed $offset <p>
	 * The offset to retrieve.
	 * </p>
	 * @return mixed Can return all value types.
	 * @since 5.0.0
	 */
	public function offsetGet($offset) {
		return $this->access_test_results[$offset];
	}

	/**
	 * Offset to set
	 *
	 * @link http://php.net/manual/en/arrayaccess.offsetset.php
	 * @param mixed $offset <p>
	 * The offset to assign the value to.
	 * </p>
	 * @param mixed $value <p>
	 * The value to set.
	 * </p>
	 * @return void
	 * @since 5.0.0
	 */
	public function offsetSet($offset, $value) {
		$this->access_test_results[$offset] = $value;
	}

	/**
	 * Offset to unset
	 *
	 * @link http://php.net/manual/en/arrayaccess.offsetunset.php
	 * @param mixed $offset <p>
	 * The offset to unset.
	 * </p>
	 * @return void
	 * @since 5.0.0
	 */
	public function offsetUnset($offset) {
		unset($this->access_test_results[$offset]);
	}
}