<?php

class WPML_Rewrite_Rule_Filter_Factory {

	/**
	 * @return WPML_Rewrite_Rule_Filter
	 */
	public function create() {
		/** @var SitePress */
		global $sitepress;

		$slug_records_factory = new WPML_Slug_Translation_Records_Factory();

		$filters = array(
			new WPML_ST_Post_Rewrite_Rule_Filter(
				$slug_records_factory->create( WPML_Slug_Translation_Factory::POST ),
				$sitepress
			),
			new WPML_ST_Tax_Rewrite_Rule_Filter(
				$slug_records_factory->create( WPML_Slug_Translation_Factory::TAX ),
				$sitepress,
				new WPML_ST_Tax_Slug_Translation_Settings()
			),
		);

		return new WPML_Rewrite_Rule_Filter( $filters );
	}
}
