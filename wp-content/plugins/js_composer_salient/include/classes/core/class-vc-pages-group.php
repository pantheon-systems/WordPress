<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

/**
 * Class Vs_Pages_Group Show the groups of the pages likes pages with tabs.
 *
 * @since 4.5
 */
class Vc_Pages_Group extends Vc_Page {
	protected $activePage;
	protected $pages;
	protected $templatePath;

	/**
	 * @return mixed
	 */
	public function getActivePage() {
		return $this->activePage;
	}

	/**
	 * @param Vc_Page $activePage
	 *
	 * @return $this
	 */
	public function setActivePage( Vc_Page $activePage ) {
		$this->activePage = $activePage;

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getPages() {
		return $this->pages;
	}

	/**
	 * @param mixed $pages
	 *
	 * @return $this
	 */
	public function setPages( $pages ) {
		$this->pages = $pages;

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getTemplatePath() {
		return $this->templatePath;
	}

	/**
	 * @param mixed $templatePath
	 *
	 * @return $this
	 */
	public function setTemplatePath( $templatePath ) {
		$this->templatePath = $templatePath;

		return $this;
	}

	/**
	 * Render html output for current page.
	 */
	public function render() {
		vc_include_template( $this->getTemplatePath(),
			array(
				'pages' => $this->getPages(),
				'active_page' => $this->activePage,
				'page' => $this,
			) );
	}
}
