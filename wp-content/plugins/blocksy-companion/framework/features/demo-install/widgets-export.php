<?php

namespace Blocksy;

class DemoInstallWidgetsExport {
	public function export() {
		$available_widgets = $this->available_widgets();

		$widget_instances = array();

		foreach ( $available_widgets as $widget_data ) {
			$instances = get_option('widget_' . $widget_data['id_base']);

			if (! empty($instances)) {
				foreach ($instances as $instance_id => $instance_data) {
					if (is_numeric($instance_id)) {
						$unique_instance_id = $widget_data['id_base'] . '-' . $instance_id;
						$widget_instances[ $unique_instance_id ] = $instance_data;
					}
				}
			}
		}

		$sidebars_widgets = get_option('sidebars_widgets');
		$sidebars_widget_instances = [];

		foreach ($sidebars_widgets as $sidebar_id => $widget_ids) {
			if ('wp_inactive_widgets' === $sidebar_id) {
				continue;
			}

			if (! is_array($widget_ids) || empty($widget_ids)) {
				continue;
			}

			foreach ($widget_ids as $widget_id) {
				if (isset($widget_instances[$widget_id])) {
					$sidebars_widget_instances[$sidebar_id][$widget_id] = $widget_instances[$widget_id];
				}
			}
		}

		return wp_json_encode($sidebars_widget_instances);
	}

	public function available_widgets() {
		global $wp_registered_widget_controls;

		$widget_controls = $wp_registered_widget_controls;

		$available_widgets = array();

		foreach ($widget_controls as $widget) {
			// No duplicates.
			if (
				! empty($widget['id_base'])
				&&
				! isset($available_widgets[$widget['id_base']])
			) {
				$available_widgets[$widget['id_base']]['id_base'] = $widget['id_base'];
				$available_widgets[$widget['id_base']]['name'] = $widget['name'];
			}
		}

		return $available_widgets;
	}
}
