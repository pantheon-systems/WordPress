<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

return apply_filters('woocommerce_csv_product_post_columns', array(
	'post_title'		=> 'post_title',
	'post_name'		=> 'post_name',
	'ID' 			=> 'ID',
	'post_excerpt'		=> 'post_excerpt',
	'post_content'		=> 'post_content',
	'post_status'		=> 'post_status',
	'menu_order'		=> 'menu_order',
	'post_date'		=> 'post_date',
	'post_author'		=> 'post_author',
	'comment_status'	=> 'comment_status',

	// Meta
	'_sku'			=> 'sku',
	'_downloadable' 	=> 'downloadable',
	'_virtual'		=> 'virtual',
    	'_stock'		=> 'stock',
    	'_regular_price'	=> 'regular_price',
	'_sale_price'		=> 'sale_price',
	'_weight'		=> 'weight',
	'_length'		=> 'length',
	'_width'		=> 'width',
	'_height'		=> 'height',
        '_tax_class'		=> 'tax_class',
    
	'_visibility'		=> 'visibility',
	'_stock_status'		=> 'stock_status',
	'_backorders'		=> 'backorders',
	'_manage_stock'		=> 'manage_stock',
	'_tax_status'		=> 'tax_status',
	'_upsell_ids'		=> 'upsell_ids',
	'_crosssell_ids'	=> 'crosssell_ids',
	'_featured'		=> 'featured',

	'_sale_price_dates_from' => 'sale_price_dates_from',
	'_sale_price_dates_to' 	 => 'sale_price_dates_to',

	// Downloadable products
	'_download_limit'	=> 'download_limit',
	'_download_expiry'	=> 'download_expiry',

	// Virtual products
	'_product_url'		=> 'product_url',
	'_button_text'		=> 'button_text',
    
        '_sold_individually'    => 'sold_individually',
        '_low_stock_amount'     => 'low_stock_amount'
) );