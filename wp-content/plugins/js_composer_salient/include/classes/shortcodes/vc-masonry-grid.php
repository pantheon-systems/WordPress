<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

require_once vc_path_dir( 'SHORTCODES_DIR', 'vc-basic-grid.php' );

class WPBakeryShortCode_VC_Masonry_Grid extends WPBakeryShortCode_VC_Basic_Grid {
	protected function getFileName() {
		return 'vc_basic_grid';
	}

	public function shortcodeScripts() {
		parent::shortcodeScripts();
		wp_register_script( 'vc_masonry', vc_asset_url( 'lib/bower/masonry/dist/masonry.pkgd.min.js' ), array(), WPB_VC_VERSION, true );
	}

	public function enqueueScripts() {
		wp_enqueue_script( 'vc_masonry' );
		parent::enqueueScripts();
	}

	public function buildGridSettings() {
		parent::buildGridSettings();
		$this->grid_settings['style'] .= '-masonry';
	}

	protected function contentAllMasonry( $grid_style, $settings, $content ) {
		return parent::contentAll( $grid_style, $settings, $content );
	}

	protected function contentLazyMasonry( $grid_style, $settings, $content ) {
		return parent::contentLazy( $grid_style, $settings, $content );
	}

	protected function contentLoadMoreMasonry( $grid_style, $settings, $content ) {
		return parent::contentLoadMore( $grid_style, $settings, $content );
	}
}
