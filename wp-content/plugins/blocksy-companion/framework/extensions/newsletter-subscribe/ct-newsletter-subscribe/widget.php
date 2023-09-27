<?php
/**
 * Newsletter Subscribe widget
 *
 * @copyright 2019-present Creative Themes
 * @license   http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @package   Blocksy
 */

class Blocksy_Widget_Ct_Newsletter_Subscribe extends BlocksyWidgetFactory {
	protected function get_config() {
		return [
			'name' => __('Newsletter Subscribe', 'blocksy-companion'),
			'description' => __('Newsletter subscribe form', 'blocksy-companion'),
		];
	}

	public function get_path() {
		return dirname(__FILE__);
	}
}

