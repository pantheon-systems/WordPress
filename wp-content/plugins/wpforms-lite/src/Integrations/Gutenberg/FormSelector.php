<?php

namespace WPForms\Integrations\Gutenberg;

use WPForms\Integrations\IntegrationInterface;

/**
 * Form Selector Gutenberg block with live preview.
 *
 * @since 1.4.8
 */
class FormSelector implements IntegrationInterface {

	/**
	 * Callbacks registered for wpforms_frontend_container_class filter.
	 *
	 * @since 1.7.5
	 *
	 * @var array
	 */
	private $callbacks = [];

	/**
	 * Indicate if current integration is allowed to load.
	 *
	 * @since 1.4.8
	 *
	 * @return bool
	 */
	public function allow_load() {

		return function_exists( 'register_block_type' );
	}

	/**
	 * Load an integration.
	 *
	 * @since 1.4.8
	 */
	public function load() {

		$this->hooks();
	}

	/**
	 * Integration hooks.
	 *
	 * @since 1.4.8
	 */
	protected function hooks() {

		add_action( 'init', [ $this, 'register_block' ] );
		add_action( 'enqueue_block_editor_assets', [ $this, 'enqueue_block_editor_assets' ] );
		add_action( 'wpforms_frontend_output_container_after', [ $this, 'replace_wpforms_frontend_container_class_filter' ] );
	}

	/**
	 * Replace the filter registered for wpforms_frontend_container_class.
	 *
	 * @since 1.7.5
	 *
	 * @param array $form_data Form data.
	 *
	 * @return void
	 */
	public function replace_wpforms_frontend_container_class_filter( $form_data ) { // phpcs:ignore WPForms.PHP.HooksMethod.InvalidPlaceForAddingHooks

		if ( empty( $this->callbacks[ $form_data['id'] ] ) ) {
			return;
		}

		$callback = array_shift( $this->callbacks[ $form_data['id'] ] );

		remove_filter( 'wpforms_frontend_container_class', $callback );

		if ( ! empty( $this->callbacks[ $form_data['id'] ] ) ) {
			add_filter( 'wpforms_frontend_container_class', reset( $this->callbacks[ $form_data['id'] ] ), 10, 2 );
		}
	}

	/**
	 * Register WPForms Gutenberg block on the backend.
	 *
	 * @since 1.4.8
	 */
	public function register_block() {

		$attributes = [
			'formId'       => [
				'type' => 'string',
			],
			'displayTitle' => [
				'type' => 'boolean',
			],
			'displayDesc'  => [
				'type' => 'boolean',
			],
			'className'    => [
				'type' => 'string',
			],
		];

		$this->register_styles();

		register_block_type(
			'wpforms/form-selector',
			[
				/**
				 * Modify wpforms block attributes.
				 *
				 * @since 1.5.8.2
				 *
				 * @param array $attributes Attributes.
				 */
				'attributes'      => apply_filters( 'wpforms_gutenberg_form_selector_attributes', $attributes ), // phpcs:ignore WPForms.PHP.ValidateHooks.InvalidHookName
				'style'           => 'wpforms-gutenberg-form-selector',
				'editor_style'    => 'wpforms-integrations',
				'render_callback' => [ $this, 'get_form_html' ],
			]
		);
	}

	/**
	 * Register WPForms Gutenberg block styles.
	 *
	 * @since 1.7.4.2
	 */
	protected function register_styles() {

		if ( ! is_admin() ) {
			return;
		}

		$min = wpforms_get_min_suffix();

		wp_register_style(
			'wpforms-integrations',
			WPFORMS_PLUGIN_URL . "assets/css/admin-integrations{$min}.css",
			[],
			WPFORMS_VERSION
		);

		$disable_css_setting = (int) wpforms_setting( 'disable-css', '1' );

		if ( $disable_css_setting === 3 ) {
			return;
		}

		$css_file = $disable_css_setting === 2 ? 'base' : 'full';

		wp_register_style(
			'wpforms-gutenberg-form-selector',
			WPFORMS_PLUGIN_URL . "assets/css/wpforms-{$css_file}{$min}.css",
			[ 'wp-edit-blocks', 'wpforms-integrations' ],
			WPFORMS_VERSION
		);
	}

	/**
	 * Load WPForms Gutenberg block scripts.
	 *
	 * @since 1.4.8
	 */
	public function enqueue_block_editor_assets() {

		$i18n = [
			'title'             => esc_html__( 'WPForms', 'wpforms-lite' ),
			'description'       => esc_html__( 'Select and display one of your forms.', 'wpforms-lite' ),
			'form_keywords'     => [
				esc_html__( 'form', 'wpforms-lite' ),
				esc_html__( 'contact', 'wpforms-lite' ),
				esc_html__( 'survey', 'wpforms-lite' ),
				'the dude',
			],
			'form_select'       => esc_html__( 'Select a Form', 'wpforms-lite' ),
			'form_settings'     => esc_html__( 'Form Settings', 'wpforms-lite' ),
			'form_selected'     => esc_html__( 'Form', 'wpforms-lite' ),
			'show_title'        => esc_html__( 'Show Title', 'wpforms-lite' ),
			'show_description'  => esc_html__( 'Show Description', 'wpforms-lite' ),
			'panel_notice_head' => esc_html__( 'Heads up!', 'wpforms-lite' ),
			'panel_notice_text' => esc_html__( 'Do not forget to test your form.', 'wpforms-lite' ),
			'panel_notice_link' => esc_html__( 'Check out our complete guide!', 'wpforms-lite' ),
		];

		if ( version_compare( $GLOBALS['wp_version'], '5.1.1', '<=' ) ) {
			array_pop( $i18n['form_keywords'] );
		}

		wp_enqueue_style( 'wpforms-integrations' );

		wp_enqueue_script(
			'wpforms-gutenberg-form-selector',
			// The unminified version is not supported by the browser.
			WPFORMS_PLUGIN_URL . 'assets/js/components/admin/gutenberg/formselector.min.js',
			[ 'wp-blocks', 'wp-i18n', 'wp-element' ],
			WPFORMS_VERSION,
			true
		);

		$forms = wpforms()->form->get( '', [ 'order' => 'DESC' ] );
		$forms = ! empty( $forms ) ? $forms : [];
		$forms = array_map(
			static function( $form ) {

				$form->post_title = htmlspecialchars_decode( $form->post_title, ENT_QUOTES );

				return $form;
			},
			$forms
		);

		wp_localize_script(
			'wpforms-gutenberg-form-selector',
			'wpforms_gutenberg_form_selector',
			[
				'logo_url'          => WPFORMS_PLUGIN_URL . 'assets/images/sullie-alt.png',
				'block_preview_url' => WPFORMS_PLUGIN_URL . 'assets/images/integrations/gutenberg/block-preview.png',
				'wpnonce'           => wp_create_nonce( 'wpforms-gutenberg-form-selector' ),
				'forms'             => $forms,
				'i18n'              => $i18n,
			]
		);
	}

	/**
	 * Get form HTML to display in a WPForms Gutenberg block.
	 *
	 * @since 1.4.8
	 *
	 * @param array $attr Attributes passed by WPForms Gutenberg block.
	 *
	 * @return string
	 */
	public function get_form_html( $attr ) { // phpcs:ignore WPForms.PHP.HooksMethod.InvalidPlaceForAddingHooks

		$id = ! empty( $attr['formId'] ) ? absint( $attr['formId'] ) : 0;

		if ( empty( $id ) ) {
			return '';
		}

		$title = ! empty( $attr['displayTitle'] );
		$desc  = ! empty( $attr['displayDesc'] );

		if ( $this->is_gb_editor() ) {
			$this->disable_fields_in_gb_editor();
		}

		if ( ! empty( $attr['className'] ) ) {
			$class_callback = static function ( $classes, $form_data ) use ( $id, $attr ) {

				if ( (int) $form_data['id'] !== $id ) {
					return $classes;
				}

				$cls = array_map( 'esc_attr', explode( ' ', $attr['className'] ) );

				return array_unique( array_merge( $classes, $cls ) );
			};

			if ( empty( $this->callbacks[ $id ] ) ) {
				add_filter( 'wpforms_frontend_container_class', $class_callback, 10, 2 );
			}

			$this->callbacks[ $id ][] = $class_callback;
		}

		ob_start();

		// phpcs:disable WPForms.PHP.ValidateHooks.InvalidHookName

		/**
		 * Fires before Gutenberg block output.
		 *
		 * @since 1.5.8.2
		 */
		do_action( 'wpforms_gutenberg_block_before' );

		/**
		 * Filter block title display flag.
		 *
		 * @since 1.5.8.2
		 *
		 * @param bool $title Title display flag.
		 * @param int  $id    Form id.
		 */
		$title = apply_filters( 'wpforms_gutenberg_block_form_title', $title, $id );

		/**
		 * Filter block description display flag.
		 *
		 * @since 1.5.8.2
		 *
		 * @param bool $desc Description display flag.
		 * @param int  $id   Form id.
		 */
		$desc = apply_filters( 'wpforms_gutenberg_block_form_desc', $desc, $id );

		if ( $this->is_gb_editor() ) {
			wpforms_display(
				$id,
				$title,
				$desc
			);
		} else {
			printf(
				'[wpforms id="%s" title="%d" description="%d"]',
				absint( $id ),
				(bool) $title,
				(bool) $desc
			);
		}

		/**
		 * Fires after Gutenberg block output.
		 *
		 * @since 1.5.8.2
		 */
		do_action( 'wpforms_gutenberg_block_after' );

		$content = ob_get_clean();

		if ( empty( $content ) ) {
			$content = '<div class="components-placeholder"><div class="components-placeholder__label"></div>' .
			           '<div class="components-placeholder__fieldset">' .
			           esc_html__( 'The form cannot be displayed.', 'wpforms-lite' ) .
			           '</div></div>';
		}

		/**
		 * Filter Gutenberg block content.
		 *
		 * @since 1.5.8.2
		 *
		 * @param string $content Block content.
		 * @param int    $id      Form id.
		 */
		return apply_filters( 'wpforms_gutenberg_block_form_content', $content, $id );
		// phpcs:enable WPForms.PHP.ValidateHooks.InvalidHookName
	}

	/**
	 * Checking if is Gutenberg REST API call.
	 *
	 * @since 1.5.7
	 *
	 * @return bool True if is Gutenberg REST API call.
	 */
	public function is_gb_editor() {

		// TODO: Find a better way to check if is GB editor API call.
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended
		return defined( 'REST_REQUEST' ) && REST_REQUEST && ! empty( $_REQUEST['context'] ) && $_REQUEST['context'] === 'edit';
	}

	/**
	 * Disable form fields if called from the Gutenberg editor.
	 *
	 * @since 1.7.5
	 *
	 * @return void
	 */
	private function disable_fields_in_gb_editor() { // phpcs:ignore WPForms.PHP.HooksMethod.InvalidPlaceForAddingHooks

		add_filter(
			'wpforms_frontend_container_class',
			function ( $classes ) {

				$classes[] = 'wpforms-gutenberg-form-selector';
				$classes[] = 'wpforms-container-full';

				return $classes;
			}
		);
		add_action(
			'wpforms_frontend_output',
			function () {

				echo '<fieldset disabled>';
			},
			3
		);
		add_action(
			'wpforms_frontend_output',
			function () {

				echo '</fieldset>';
			},
			30
		);
	}
}
