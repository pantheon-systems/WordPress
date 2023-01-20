<?php

class Blocksy_Meta_Boxes {
	public function __construct() {
		add_action('init', [$this, 'init_taxonomies'], 999);

		add_action('load-post.php', [$this, 'init_metabox']);
		add_action('load-post-new.php', [$this, 'init_metabox']);

		add_action(
			'woocommerce_save_product_variation',
			function ($variation_id, $i) {
				if (! isset($_POST['blocksy_post_meta_options'])) {
					return;
				}

				if (! isset($_POST['blocksy_post_meta_options'][$variation_id])) {
					return;
				}

				update_post_meta(
					$variation_id,
					'blocksy_post_meta_options',
					json_decode(
						sanitize_text_field(
							wp_unslash(
								$_POST['blocksy_post_meta_options'][
									$variation_id
								]
							)
						),
						true
					)
				);

				return true;
			},
			10, 2
		);

		add_action(
			'woocommerce_variation_header',
			function ($variation) {
				if (! blocksy_woocommerce_has_flexy_view()) {
					return;
				}

				$post_id = $variation->ID;

				$values = get_post_meta(
					$post_id, 'blocksy_post_meta_options'
				);

				if (empty($values)) {
					$values = [[]];
				}

				if (! $values[0]) {
					$values[0] = [];
				}

				echo blocksy_html_tag( 'input', [
					'type' => 'hidden',
					'class' => 'ct-options-panel-storage',
					'value' => htmlspecialchars(wp_json_encode($values[0])),
					'id' => 'blocksy_post_meta_options_' . $post_id . '',
					'name' => 'blocksy_post_meta_options[' . $post_id . ']'
				]);
			}
		);
	}

	public function init_metabox() {
		add_action('add_meta_boxes', [$this, 'setup_meta_box']);
		add_action('save_post', [$this, 'save_meta_box']);
	}

	public function init_taxonomies() {
		$post_types = apply_filters(
			'blocksy:editor:post_types_for_rest_field',
			['post', 'page']
		);

		$custom_post_types = blocksy_manager()->post_types->get_supported_post_types();

		foreach ($custom_post_types as $single_custom_post_type) {
			$post_types[] = $single_custom_post_type;
		}

		register_rest_field(
			$post_types,
			'blocksy_meta',
			array(
				'get_callback' => function ($object) {
					$post_id = $object['id'];
					return get_post_meta($post_id, 'blocksy_post_meta_options', true);
				},
				'update_callback' => function ($value, $object) {
					$post_id = $object->ID;

					$value['styles_descriptor'] = blocksy_manager()
						->dynamic_css
						->maybe_set_single_post_styles_descriptor([
							'post_id' => $post_id,
							'atts' => $value
						]);

					update_post_meta(
						$post_id,
						'blocksy_post_meta_options',
						$value
					);
				}
			)
		);
	}

	public function setup_meta_box() {
		// Get all public posts.
		$post_types = get_post_types(['public' => true]);

		foreach ($post_types as $type) {
			$options = apply_filters('blocksy_post_meta_options', null, $type);

			if (! $options) {
				continue;
			}

			add_meta_box(
				'blocksy_settings_meta_box',
				sprintf(
					// Translators: %s is the theme name.
					__( '%s Settings', 'blocksy' ),
					__( 'Blocksy', 'blocksy' )
				),
				function ($post) {
					$values = get_post_meta($post->ID, 'blocksy_post_meta_options');

					if (empty($values)) {
						$values = [[]];
					}

					$options = apply_filters(
						'blocksy_post_meta_options',
						null,
						get_post_type($post)
					);

					if (! $options) {
						return;
					}

					/**
					 * Note to code reviewers: This line doesn't need to be escaped.
					 * Function blocksy_output_options_panel() used here escapes the value properly.
					 */
					echo blocksy_output_options_panel(
						[
							'options' => $options,
							'values' => $values[0],
							'id_prefix' => 'ct-post-meta-options',
							'name_prefix' => 'blocksy_post_meta_options',
							'attr' => [
								'class' => 'ct-meta-box',
								'data-disable-reverse-button' => 'yes'
							]
						]
					);

					wp_nonce_field(basename(__FILE__), 'blocksy_settings_meta_box');
				},
				$type, 'normal', 'default'
			);
		}
	}

	public function save_meta_box($post_id) {
		// Checks save status.
		$is_autosave = wp_is_post_autosave($post_id);
		$is_revision = wp_is_post_revision($post_id);
		$is_valid_nonce = !! (
			isset($_POST['blocksy_settings_meta_box']) && wp_verify_nonce(
				sanitize_text_field(wp_unslash($_POST['blocksy_settings_meta_box'])), basename(__FILE__)
			)
		);

		if ($is_autosave || $is_revision || !$is_valid_nonce) {
			return;
		}

		$values = [];

		if (isset($_POST['blocksy_post_meta_options'][blocksy_post_name()])) {
			$values = json_decode(
				wp_unslash($_POST['blocksy_post_meta_options'][blocksy_post_name()]),
				true
			);
		}

		update_post_meta($post_id, 'blocksy_post_meta_options', $values);
	}
}

new Blocksy_Meta_Boxes();
