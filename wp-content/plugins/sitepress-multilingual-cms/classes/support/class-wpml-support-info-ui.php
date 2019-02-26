<?php

/**
 * @author OnTheGo Systems
 */
class WPML_Support_Info_UI {
	/** @var WPML_Support_Info */
	private $support_info;
	/** @var IWPML_Template_Service */
	private $template_service;

	function __construct( WPML_Support_Info $support_info, IWPML_Template_Service $template_service ) {
		$this->support_info     = $support_info;
		$this->template_service = $template_service;
	}

	/**
	 * @return string
	 */
	public function show() {
		$model = $this->get_model();

		return $this->template_service->show( $model, 'main.twig' );
	}

	/** @return array */
	private function get_model() {
		$minimum_required_memory         = '128M';
		$minimum_required_php_version    = '5.3';
		$minimum_recommended_php_version = '5.6';
		$minimum_required_wp_version     = '3.9.0';

		$php_version        = $this->support_info->get_php_version();
		$php_memory_limit   = $this->support_info->get_php_memory_limit();
		$memory_usage       = $this->support_info->get_memory_usage();
		$max_execution_time = $this->support_info->get_max_execution_time();
		$max_input_vars     = $this->support_info->get_max_input_vars();

		$blocks = array(
			'php' => array(
				'strings' => array(
					'title' => __( 'PHP', 'sitepress' ),
				),
				'data'    => array(
					'version'            => array(
						'label'      => __( 'Version', 'sitepress' ),
						'value'      => $php_version,
						'url'        => 'http://php.net/supported-versions.php',
						'messages'   => array(
							__( 'PHP 5.6 and above are recommended. PHP 5.3 is the minimum requirement.', 'sitepress' ) => 'https://wpml.org/home/minimum-requirements/',
							__( 'Find how you can update PHP.', 'sitepress' )                                           => 'http://www.wpupdatephp.com/update/',
						),
						'is_error'   => $this->support_info->is_version_less_than( $minimum_required_php_version, $php_version ),
						'is_warning' => $this->support_info->is_version_less_than( $minimum_recommended_php_version, $php_version ),
					),
					'memory_limit'       => array(
						'label'    => __( 'Memory limit', 'sitepress' ),
						'value'    => $php_memory_limit,
						'url'      => 'http://php.net/manual/ini.core.php#ini.memory-limit',
						'messages' => array(
							__( 'A memory limit of at least 128MB is required.', 'sitepress' ) => 'https://wpml.org/home/minimum-requirements/',
						),
						'is_error' => $this->support_info->is_memory_less_than( $minimum_required_memory, $php_memory_limit ),
					),
					'memory_usage'       => array(
						'label' => __( 'Memory usage', 'sitepress' ),
						'value' => $memory_usage,
						'url'   => 'http://php.net/memory-get-usage',
					),
					'max_execution_time' => array(
						'label' => __( 'Max execution time', 'sitepress' ),
						'value' => $max_execution_time,
						'url'   => 'http://php.net/manual/info.configuration.php#ini.max-execution-time',
					),
					'max_input_vars'     => array(
						'label' => __( 'Max input vars', 'sitepress' ),
						'value' => $max_input_vars,
						'url'   => 'http://php.net/manual/info.configuration.php#ini.max-input-vars',
					),
					'utf8mb4_charset' => array(
						'label'      => __( 'Utf8mb4 charset', 'sitepress' ),
						'value' => __( $this->support_info->is_utf8mb4_charset_supported() ? 'Yes' : 'No', 'sitepress' ),
						'url'        => 'https://dev.mysql.com/doc/refman/5.5/en/charset-unicode-utf8mb4.html',
						'messages'   => array(
							__( 'Some features related to String Translations may not work correctly without utf8mb4 character.', 'sitepress' ) => 'https://wpml.org/home/minimum-requirements/',
						),
						'is_error' => ! $this->support_info->is_utf8mb4_charset_supported(),
					) ,
				),
			),
			'wp'  => array(
				'strings' => array(
					'title' => __( 'WordPress', 'sitepress' ),
				),
				'data'    => array(
					'wp_version'       => array(
						'label'    => __( 'Version', 'sitepress' ),
						'value'    => $this->support_info->get_wp_version(),
						'messages' => array(
							__( 'WordPress 3.9 or later is required.', 'sitepress' ) => 'https://wpml.org/home/minimum-requirements/',
						),
						'is_error' => $this->support_info->is_version_less_than( $minimum_required_wp_version, $this->support_info->get_wp_version() ),
					),
					'multisite'        => array(
						'label' => __( 'Multisite', 'sitepress' ),
						'value' => $this->support_info->get_wp_multisite() ? __( 'Yes' ) : __( 'No' ),
					),
					'memory_limit'     => array(
						'label'    => __( 'Memory limit', 'sitepress' ),
						'value'    => $this->support_info->get_wp_memory_limit(),
						'url'      => 'https://codex.wordpress.org/Editing_wp-config.php#Increasing_memory_allocated_to_PHP',
						'messages' => array(
							__( 'A memory limit of at least 128MB is required.', 'sitepress' ) => 'https://wpml.org/home/minimum-requirements/',
						),
						'is_error' => $this->support_info->is_memory_less_than( $minimum_required_memory, $this->support_info->get_wp_memory_limit() ),
					),
					'max_memory_limit' => array(
						'label'    => __( 'Max memory limit', 'sitepress' ),
						'value'    => $this->support_info->get_wp_max_memory_limit(),
						'url'      => 'https://codex.wordpress.org/Editing_wp-config.php#Increasing_memory_allocated_to_PHP',
						'messages' => array(
							__( 'A memory limit of at least 128MB is required.', 'sitepress' ) => 'https://wpml.org/home/minimum-requirements/',
						),
						'is_error' => $this->support_info->is_memory_less_than( $minimum_required_memory, $this->support_info->get_wp_max_memory_limit() ),
					),
				),
			),
		);

		if ( $this->support_info->is_suhosin_active() ) {
			$blocks['php']['data']['eval_suhosin'] = array(
				'label'    => __( 'eval() availability from Suhosin', 'sitepress' ),
				'value'    => $this->support_info->eval_disabled_by_suhosin() ? __( 'Not available', 'sitepress' ) : __( 'Available', 'sitepress' ),
				'url'      => 'https://suhosin.org/stories/configuration.html#suhosin-executor-disable-eval',
				'messages' => array(
					__( 'The eval() PHP function must be enabled.', 'sitepress' ) => 'https://wpml.org/home/minimum-requirements/#eval-usage',
				),
				'is_error' => $this->support_info->eval_disabled_by_suhosin(),
			);
		}

		/**
		 * Allows to extend the data shown in the WPML > Support > Info
		 *
		 * This filter is for internal use.
		 * You can add items to the `$blocks` array, however, it is strongly
		 * recommended to not modify existing data.
		 *
		 * You can see how `$block` is structured by scrolling at the beginning of this method.
		 *
		 * The "messages" array can contain just a string (the message) or a string (the message)
		 * and an URL (message linked to that URL).
		 * That is, you can have:
		 * ```
		 * 'messages' => array(
		 *    'Some message A' => 'https://domain.tld',
		 *    'Some message B' => 'https://domain.tld',
		 *    'Some message C',
		 * ),
		 * ```
		 *
		 * @since 3.8.0
		 *
		 * @param array $blocks
		 */
		$blocks = apply_filters( 'wpml_support_info_blocks', $blocks );

		$this->set_has_messages( $blocks, 'is_error' );
		$this->set_has_messages( $blocks, 'is_warning' );

		$model = array(
			'title'  => __( 'Info', 'sitepress' ),
			'blocks' => $blocks,
		);

		return $model;
	}

	/**
	 * @param array $blocks
	 * @param       $type
	 */
	private function set_has_messages( array &$blocks, $type ) {
		/**
		 * @var string $id
		 * @var array  $content
		 */
		foreach ( $blocks as $id => $content ) {
			if ( ! array_key_exists( 'has_messages', $content ) ) {
				$content['has_messages'] = false;
			}
			foreach ( (array) $content['data'] as $key => $item_data ) {
				if ( array_key_exists( $type, $item_data ) && (bool) $item_data[ $type ] ) {
					$content['has_messages'] = true;
					break;
				}
			}
			$blocks[ $id ] = $content;
		}
	}
}
