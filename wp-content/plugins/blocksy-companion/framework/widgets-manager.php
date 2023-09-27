<?php

class BlocksyWidgetFactory extends WP_Widget {
	/**
	 * Options for the widget.
	 *
	 * @var array $options options.
	 */
	public $options;

	/**
	 * Widget prefix for the path.
	 *
	 * @var string $prefix
	 */
	public $prefix = 'ct_default_widget';

	public function get_path() {
		throw new Error('Implement get_path()');
	}

	/**
	 * Loop through all the folders and treat each folder as a separated widget.
	 * Based on the folder name, instantiate a specific class, treat this
	 * as convention over configuration.
	 */
	public static function register_all_widgets() {
		$all_widgets = apply_filters('blocksy_widgets_paths', []);

		foreach ($all_widgets as $widget) {
			$prefix = basename($widget);

			include_once $widget . '/widget.php';

			$class_name = explode('-', $prefix);
			$class_name = array_map('ucfirst', $class_name);
			$class_name = implode('_', $class_name);

			register_widget('Blocksy_Widget_' . $class_name);
		}
	}

	/**
	 * Extract widget path based on the class name.
	 */
	/*
	public function get_path() {
		$widget_path = strtolower( get_class( $this ) );
		$widget_path = str_replace( '_', '-', str_replace( 'blocksy_widget_', '', $widget_path ) );

		return get_template_directory() . '/inc/widgets-manager/widgets/' . $widget_path;
	}
	 */

	/**
	 * Construct the widget
	 */
	public function __construct() {
		$prefix = basename( $this->get_path() );

		$config = [
			'name' => $this->blocksy_id_to_title( $prefix ),
			'description' => __( 'Default widget description', 'blocksy' ),
		];

		$options = null;

		if ($this->get_config()) {
			$config = array_merge( $config, $this->get_config() );
		}

		$name = blocksy_akg(
			'name',
			$config,
			__('Default widget name', 'blocksy-companion')
		);

		$description = blocksy_akg(
			'description',
			$config,
			__('Display online support infomation', 'blocksy-companion')
		);

		unset($config['name']);
		unset($config['description']);

		$this->prefix = $prefix;

		parent::__construct(
			false,
			'&#10;' . $name,
			array_merge([
				'description' => $description,
				'classname' => $prefix . '-widget',
			], $config)
		);
	}

	public function widget($args, $instance) {
		$file_path = $this->get_path() . '/view.php';

		if (! file_exists($file_path)) {
			echo '<p>Default widget view. Please create a <i>view.php</i> file.</p>';
			return;
		}

		$widget_title = '';

		if (isset($instance['title'])) {
			$widget_title = $instance['title'];
		}

		$instance = blocksy_akg('ct_options', $instance, []);

		if (isset($instance['title']) && ! empty($widget_title)) {
			$instance['title'] = apply_filters('widget_title', $widget_title);
		}

		if (! $instance) {
			$instance = [];
		}

		// @codingStandardsIgnoreLine
		echo blc_call_fn(
			['fn' => 'blocksy_render_view'],
			$file_path,
			array_merge(
				[ 'atts' => $instance ],
				$args
			)
		);
	}

	public function read_options() {
		$options = null;

		if (file_exists($this->get_path() . '/options.php')) {
			$options = blocksy_akg(
				'options',
				blc_call_fn(
					['fn' => 'blocksy_get_variables_from_file'],
					$this->get_path() . '/options.php',
					['options' => []]
				)
			);
		}

		if ($options) {
			$this->options = $options;
		} else {
			$this->options = [
				'title' => [
					'type' => 'text',
					'label' => __( 'Widget Title', 'blocksy-companion' ),
					'field_attr' => [ 'id' => 'widget-title' ],
				],
			];
		}
	}

	/**
	 * Save widget values, if available.
	 *
	 * @param array $new_instance new widget values.
	 * @param array $old_instance old widget values.
	 */
	public function update($new_instance, $old_instance) {
		if (! isset($new_instance[$this->prefix])) {
			return $new_instance;
		}

		$to_save = $new_instance[$this->prefix]['ct_options'];

		if (! is_array($to_save)) {
			$to_save = json_decode($to_save, true);
		}

		$new_instance[$this->prefix]['ct_options'] = $to_save;

		if (isset($to_save['title'])) {
			$new_instance[$this->prefix]['title'] = $to_save['title'];
		}

		return $new_instance[$this->prefix];
	}

	public function form($values) {
		$this->read_options();

		$atts = blocksy_akg('ct_options', $values);

		if (isset($values['title']) && isset($atts['title'])) {
			$atts['title'] = $values['title'];
		}

		echo blc_call_fn(
			['fn' => 'blocksy_output_options_panel'],
			[
				'options' => $this->options,
				'values' => $atts,
				'id_prefix' => 'ct-widget-options-' . $this->get_field_id(
					$this->prefix
				),
				'name_prefix' => $this->get_field_name($this->prefix),
				'attr' => [
					'data-disable-reverse-button' => ''
				]
			]
		);

		return $values;
	}

	private function blocksy_id_to_title($id) {
		if (
			function_exists('mb_strtoupper')
			&&
			function_exists('mb_substr')
			&&
			function_exists('mb_strlen')
		) {
			$id = mb_strtoupper(mb_substr($id, 0, 1, 'UTF-8'), 'UTF-8') . mb_substr(
				$id,
				1,
				mb_strlen($id, 'UTF-8'),
				'UTF-8'
			);
		} else {
			$id = strtoupper(substr($id, 0, 1)) . substr($id, 1, strlen($id));
		}

		return str_replace(array('_', '-'), ' ', $id);
	}
}
