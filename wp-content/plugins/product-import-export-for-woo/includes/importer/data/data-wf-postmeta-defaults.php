<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// New postmeta defaults
return apply_filters( 'woocommerce_csv_product_postmeta_defaults', array(
	'sku'                         => '',
	'downloadable'                => 'no',
	'virtual'                     => 'no',
	'price'                       => '',
	'visibility'                  => 'visible',
	'stock'                       => 0,
	'stock_status'                => 'instock',
	'backorders'                  => 'no',
	'manage_stock'                => 'no',
	'sale_price'                  => '',
	'regular_price'               => '',
	'weight'                      => '',
	'length'                      => '',
	'width'                       => '',
	'height'                      => '',
	'tax_status'                  => 'taxable',
	'tax_class'                   => '',
	'upsell_ids'                  => array(),
	'crosssell_ids'               => array(),
	'upsell_skus'                 => array(),
	'crosssell_skus'              => array(),
	'sale_price_dates_from'       => '',
	'sale_price_dates_to'         => '',
	'min_variation_price'         => '',
	'max_variation_price'         => '',
	'min_variation_regular_price' => '',
	'max_variation_regular_price' => '',
	'min_variation_sale_price'    => '',
	'max_variation_sale_price'    => '',
	'featured'                    => 'no',
	'file_path'                   => '',
	'file_paths'                  => '',
	'download_limit'              => '',
	'download_expiry'             => '',
	'product_url'                 => '',
	'button_text'                 => '',
        'sold_individually'           => 'no',
        'low_stock_amount'            =>0,
    
) );