<?php
/**
 * Advertisement widget
 *
 * @copyright 2019-present Creative Themes
 * @license   http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @package   Blocksy
 */

class Blocksy_Widget_Ct_Advertisement extends BlocksyWidgetFactory {
	protected function get_config() {
		return [
			'name' => __('Advertisement', 'blocksy-companion'),
			'description' => __('Advertisement', 'blocksy-companion'),
			'customize_selective_refresh' => true
		];
	}

	public function get_path() {
		return dirname(__FILE__);
	}
}
