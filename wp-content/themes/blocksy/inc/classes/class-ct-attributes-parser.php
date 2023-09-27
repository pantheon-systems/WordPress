<?php
/**
 * Parse attributes in images
 *
 * @copyright 2019-present Creative Themes
 * @license   http://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @package   Blocksy
 */

/**
 * Simple parser for images attributes.
 */
class Blocksy_Attributes_Parser {
	/**
	 * Add attribute to images with tag.
	 *
	 * @param string $content content to replaces images into.
	 * @param string $attribute_name - attribute name.
	 * @param string $attribute_value - attribute value.
	 */
	public function add_attribute_to_images(
		$content,
		$attribute_name,
		$attribute_value
	) {
		$new_content = $this->add_attribute_to_images_with_tag(
			$content,
			$attribute_name,
			$attribute_value,
			'img'
		);

		return $this->add_attribute_to_images_with_tag(
			$new_content,
			$attribute_name,
			$attribute_value,
			'source'
		);
	}

	/**
	 * Rename attribute from images with tag.
	 *
	 * @param string $content content to replaces images into.
	 * @param string $attribute - attribute name.
	 */
	public function remove_attribute_from_images( $content, $attribute ) {
		$new_content = $this->remove_attribute_from_images_with_tag(
			$content,
			$attribute,
			'img'
		);

		return $this->remove_attribute_from_images_with_tag(
			$new_content,
			$attribute,
			'source'
		);
	}

	/**
	 * Rename attribute from images with tag.
	 *
	 * @param string $content content to replaces images into.
	 * @param string $old_attribute_name - attribute name.
	 * @param string $new_attribute_name - attribute name.
	 */
	public function rename_attribute_from_images(
		$content,
		$old_attribute_name,
		$new_attribute_name
	) {
		$new_content = $this->rename_attribute_from_images_with_tag(
			$content,
			$old_attribute_name,
			$new_attribute_name,
			'img'
		);

		return $this->rename_attribute_from_images_with_tag(
			$new_content,
			$old_attribute_name,
			$new_attribute_name,
			'source'
		);
	}

	/**
	 * Rename attribute from images with tag.
	 *
	 * @param string $content content to replaces images into.
	 * @param string $old_attribute_name - attribute name.
	 * @param string $new_attribute_name - attribute name.
	 * @param string $tag             - img | source.
	 */
	private function rename_attribute_from_images_with_tag(
		$content,
		$old_attribute_name,
		$new_attribute_name,
		$tag = 'img'
	) {
		if ( ! preg_match_all( '/<' . $tag . ' [^>]+>/', $content, $matches ) ) {
			return $content;
		}

		$selected_images = array();

		foreach ( $matches[0] as $image ) {
			$selected_images[] = $image;
		}

		foreach ( $selected_images as $image ) {
			$content = str_replace(
				$image,
				$this->rename_attribute_for_single_image(
					$image,
					$old_attribute_name,
					$new_attribute_name,
					$tag
				),
				$content
			);
		}

		return $content;
	}

	/**
	 * Add specific attribute to an image. Tag that has to be parsed is specified.
	 *
	 * @param string $content content to replace images into.
	 * @param string $attribute_name attribute name.
	 * @param string $attribute_value attribute value.
	 * @param string $tag             img | source.
	 */
	public function add_attribute_to_images_with_tag(
		$content,
		$attribute_name,
		$attribute_value,
		$tag = 'img'
	) {
		if ( ! preg_match_all( '/<' . $tag . ' [^>]+>/', $content, $matches ) ) {
			return $content;
		}

		$selected_images = array();

		foreach ( $matches[0] as $image ) {
			$selected_images[] = $image;
		}

		foreach ( $selected_images as $image ) {
			$content = str_replace(
				$image,
				$this->add_attribute_to_single_image(
					$image,
					$attribute_name,
					$attribute_value,
					$tag
				),
				$content
			);
		}

		return $content;
	}

	/**
	 * Remove attribute from a specific HTML tag.
	 *
	 * @param string $content content to replaces images into.
	 * @param string $attribute attribute name.
	 * @param string $tag       - img | source.
	 */
	public function remove_attribute_from_images_with_tag(
		$content,
		$attribute,
		$tag = 'img'
	) {
		if ( ! preg_match_all( '/<' . $tag . ' [^>]+>/', $content, $matches ) ) {
			return $content;
		}

		$selected_images = array();

		foreach ( $matches[0] as $image ) {
			$selected_images[] = $image;
		}

		foreach ( $selected_images as $image ) {
			$content = str_replace(
				$image,
				$this->remove_attribute_from_single_image(
					$image,
					$attribute,
					$tag
				),
				$content
			);
		}

		return $content;
	}

	/**
	 * Remove existing $attribute from <img> html, if it exists.
	 *
	 * @param string $image     - image HTML.
	 * @param string $attribute - attribute name.
	 * @param string $tag       - img | source.
	 */
	public function remove_attribute_from_single_image(
		$image,
		$attribute,
		$tag = 'img'
	) {
		return preg_replace(
			'/(\\<' .
			$tag .
			'[^>]+)(\\s?' .
			$attribute .
			'\\="[^"]+"\\s?)([^>]+)(>)/',
			'${1}${3}${4}',
			$image
		);
	}

	/**
	 * Remove existing $attribute from <img> html, if it exists.
	 *
	 * @param string $image     - image HTML.
	 * @param string $old_attribute_name - attribute name.
	 * @param string $new_attribute_name - attribute name.
	 * @param string $tag       - img | source.
	 */
	public function rename_attribute_for_single_image(
		$image,
		$old_attribute_name,
		$new_attribute_name,
		$tag = 'img'
	) {
		$old_attribute_value = ltrim(
			rtrim(
				trim(
					preg_replace(
						'/(\\<' .
						$tag .
						'[^>]+)(\\s?' .
						$old_attribute_name .
						'\\="[^"]+"\\s?)([^>]+)(>)/',
						'${2}',
						$image
					)
				),
				'"'
			),
			$old_attribute_name . '="'
		);

		$removed = $this->remove_attribute_from_images(
			$image,
			$old_attribute_name
		);

		$res = $this->add_attribute_to_single_image(
			$removed,
			$new_attribute_name,
			$old_attribute_value,
			$tag
		);

		return $res;
	}

	/**
	 * Add an attribute with a specific value an img element. Remove the
	 * attribute if it exists already.
	 *
	 * @param string $image           - image HTML.
	 * @param string $attribute_name attribute name that will be set.
	 * @param string $attribute_value value for the attribute.
	 * @param string $tag             - img | source.
	 */
	public function add_attribute_to_single_image(
		$image,
		$attribute_name,
		$attribute_value,
		$tag = 'img'
	) {
		$attr = sprintf(
			' %s="%s"',
			esc_attr( $attribute_name ),
			esc_attr( $attribute_value )
		);

		$val = preg_replace(
			'/<' . $tag . ' ([^>]+?)[\\/ ]*>/',
			'<' . $tag . ' $1' . $attr . ' />',
			$this->remove_attribute_from_images( $image, $attribute_name )
		);

		return $val;
	}
}

