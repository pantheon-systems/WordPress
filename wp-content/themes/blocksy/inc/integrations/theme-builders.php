<?php

if (! function_exists('blocksy_output_header')) {
	function blocksy_output_header() {
		global $blocksy_has_default_header;

		if (
			function_exists('blc_get_content_block_that_matches')
			&&
			blc_get_content_block_that_matches([
				'template_type' => 'header'
			])
		) {
			echo blocksy_html_tag(
				'header',
				array_merge(
					[
						'id' => 'header',
					],
					blocksy_schema_org_definitions('header', ['array' => true])
				),
				blc_render_content_block(
					blc_get_content_block_that_matches([
						'template_type' => 'header'
					])
				)
			);

			return;
		}

		if (
			function_exists('boostify_header_active')
			&&
			boostify_header_active()
		) {
			boostify_get_header_template();
			return;
		}

		if (function_exists('hfe_render_header') && hfe_header_enabled()) {
			hfe_render_header();
			return;
		}

		if (
			function_exists('elementor_theme_do_location')
			&&
			elementor_theme_do_location('header')
		) {
			return;
		}

		if (class_exists('FLThemeBuilderLayoutData')) {
			$header_ids = FLThemeBuilderLayoutData::get_current_page_header_ids();

			if (! empty($header_ids)) {
				FLThemeBuilderLayoutRenderer::render_header();
			}
		}

		$header_result = Blocksy_Manager::instance()->header_builder->render();

		if (! empty($header_result)) {
			$blocksy_has_default_header = true;
			echo $header_result;
		}
	}
}

if (! function_exists('blocksy_output_footer')) {
	function blocksy_output_footer() {
		if (
			function_exists('blc_get_content_block_that_matches')
			&&
			blc_get_content_block_that_matches([
				'template_type' => 'footer'
			])
		) {
			echo blocksy_html_tag(
				'footer',
				blocksy_schema_org_definitions('footer', [
					'array' => true
				]),
				blc_render_content_block(
					blc_get_content_block_that_matches([
						'template_type' => 'footer'
					])
				)
			);
			return;
		}

		if (
			function_exists('boostify_footer_active')
			&&
			boostify_footer_active()
		) {
			boostify_get_footer_template();
			return;
		}

		if (function_exists('hfe_footer_enabled') && hfe_footer_enabled()) {
			hfe_render_footer();
			return;
		}

		if (
			function_exists('elementor_theme_do_location')
			&&
			elementor_theme_do_location('footer')
		) {
			return;
		}

		if (class_exists('FLThemeBuilderLayoutData')) {
			$footer_ids = FLThemeBuilderLayoutData::get_current_page_footer_ids();

			if (! empty($footer_ids)) {
				FLThemeBuilderLayoutRenderer::render_footer();
			}
		}

		echo blocksy_manager()->footer_builder->render();
	}
}
