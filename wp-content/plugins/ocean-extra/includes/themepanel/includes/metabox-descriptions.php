<?php

function oe_get_metabox_descriptions() {
	$list = apply_filters(
		'oe_metabox_descriptions_text',
		array(
			'post'                => __( 'Single blog post page', 'ocean-extra' ),
			'page'                => __( 'Single page', 'ocean-extra' ),
			'ae_global_templates' => __( 'Default AnyWhere Elementor template item', 'ocean-extra' ),
			'product'             => __( 'Single product item (WooCommerce & Easy Digital Downloads)', 'ocean-extra' ),
			'elementor_library'   => __( 'Default Elementor template item', 'ocean-extra' ),
			'oceanwp_library'     => __( 'OceanWP My Library template item', 'ocean-extra' ),
			'ocean_portfolio'     => __( 'Single Ocean Portfolio item', 'ocean-extra' ),
		)
	);
	return $list;
}
