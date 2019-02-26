<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// New postmeta allowed
return array(
	'downloadable' 	=> array( 'yes', 'no' ),
	'virtual' 		=> array( 'yes', 'no' ),
	'visibility'	=> array( 'visible', 'catalog', 'search', 'hidden' ),
	'stock_status'	=> array( 'instock', 'outofstock' ),
	'backorders'	=> array( 'yes', 'no', 'notify' ),
	'manage_stock'	=> array( 'yes', 'no' ),
	'tax_status'	=> array( 'taxable', 'shipping', 'none' ),
	'featured'		=> array( 'yes', 'no' ),
);