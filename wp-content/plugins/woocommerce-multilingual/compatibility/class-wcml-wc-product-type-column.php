<?php

class WCML_WC_Product_Type_Column {

	public function add_hooks() {

		add_filter( 'wcml_show_type_column', array( $this, 'show_type_column' ) );

	}

	public function show_type_column( $show ) {

		wp_enqueue_style( 'wc-product-type-column-admin-styles' );

		return true;
	}

}
