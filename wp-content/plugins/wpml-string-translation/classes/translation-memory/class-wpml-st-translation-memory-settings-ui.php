<?php

class WPML_ST_Translation_Memory_Settings_UI implements IWPML_Action {

	/** @var SitePress $sitepress */
	private $sitepress;

	public function __construct( SitePress $sitepress ) {
		$this->sitepress = $sitepress;
	}

	public function add_hooks() {
		add_action( 'wpml_how_to_translate_posts_and_pages', array( $this, 'render' ) );
	}

	public function render() {
		$translation_memory = $this->sitepress->get_setting( 'translation_memory', 1 );
		?>
		<div class="wpml-section-content-inner">
			<h4>
				<?php esc_html_e( 'Translation memory for strings', 'wpml-string-translation' ); ?>
			</h4>
			<p>
				<label>
					<input name="translation_memory"
					       value="1" <?php checked( $translation_memory, 1 ) ?>
					       type="radio" />
					<?php esc_html_e( 'Look for translated strings and use their translations for new jobs', 'wpml-string-translation' ); ?>
				</label>
			</p>
			<p>
				<label>
					<input name="translation_memory"
					       value="0" <?php checked( $translation_memory, 0 ) ?>
					       type="radio" />
					<?php esc_html_e( "Don't reuse string translation", 'wpml-string-translation' ); ?>
				</label>
			</p>
		</div>
		<?php
	}
}
