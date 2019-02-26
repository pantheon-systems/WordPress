<?php

function os_init() {
	// Global parameters
	global $ProdId, $PageSize, $wpdb, $OrderCreatedTime, $LastUpdatedTime, $Page, $Action, $xml, $xmlRequest;
	//$PageSize = 100;
	// Initialise XML Response
	$xml = new SimpleXMLElement('<?xml version="1.0" encoding="utf-8"?><OneSaas></OneSaas>');	
	$xml->addAttribute('Version', '2.0.6.43');
	readParameters();
	sendHeaders();
}

function readParameters() {
	global $PageSize, $ProdId, $wpdb, $OrderCreatedTime, $LastUpdatedTime, $Page, $Action, $xml, $xmlRequest;
	
	// Subtracting 86400 s from Unix timestamp (1 day) just to ensure we are not missing anything.  The time could be based on local server time rather than UTC
	$LastUpdatedTime = ((isset($_GET['LastUpdatedTime']) && (strtotime($_GET['LastUpdatedTime'].'UTC')>0)) ? (strtotime($_GET['LastUpdatedTime'].'UTC')) : strtotime('1970-01-19T00:00:00+00:00UCT'));
	$OrderCreatedTime = ((isset($_GET['OrderCreatedTime']) && (strtotime($_GET['OrderCreatedTime'].'UTC')>0)) ? (strtotime($_GET['OrderCreatedTime'].'UTC')) : strtotime('1970-01-19T00:00:00+00:00UCT'));
	$Page = ((isset($_GET['Page']) && (is_numeric($_GET['Page']))) ? (int) $_GET['Page'] : 0);
	$PageSize = ((isset($_GET['PageSize']) && (is_numeric($_GET['PageSize']))) ? (int) $_GET['PageSize'] : 100);
	$Action = (isset($_GET['Action']) ? $_GET['Action'] : '');
	$ProdId = ((isset($_GET['Id']) && (is_numeric($_GET['Id']))) ? (int) $_GET['Id'] : NULL);
	// Parse posted xml
	if ((file_get_contents("php://input") != null) && (file_get_contents("php://input") != ""))
		$xmlRequest = new SimpleXmlElement(file_get_contents("php://input"));
}

function sendHeaders() {
	header('Content-type: application/xml', true);
	header('Pragma: public', true);
	header('Cache-control: private', true);
	header('Expires: -1', true);
}

function verifyApiKey() {
	$dbkey = get_option('wc-onesaas-apikey');
	
	if($dbkey){
		$getKey = $_GET['AccessKey'];
		if($dbkey === $getKey){
			return true;
		}
	}
	return false;
}
/*
function loadField($id, $fieldCode){
	global $wpdb;
	$tableName = $wpdb->prefix.'wpsc_submited_form_data';
	$query = "SELECT value FROM $tableName WHERE log_id = $id AND form_id = '$fieldCode'";
	$rows = $wpdb->get_row($query);
	return htmlspecialchars(strip_tags($rows->value));
}
*/
function getProductCode($product) {
	if ($product==null) {
		return null;
	}
	return ($product->get_sku()==null)?$product->id:$product->get_sku();
}

function xml_adopt($root, $new) {
    $node = $root->addChild($new->getName(), (string) $new);
    foreach($new->attributes() as $attr => $value) {
        $node->addAttribute($attr, $value);
    }
    foreach($new->children() as $ch) {
        xml_adopt($node, $ch);
    }
}
/*
function updateLastModified($objectType, $forceFull=false) {
	checkCreateTable();
	global $wpdb;
	switch ($objectType) {
		case 'customer':
			$wpdb->query("SET SESSION group_concat_max_len = 1000000;");
			$insert = "
			INSERT INTO `" . $wpdb->prefix . "osapi_last_modified` (object_type, id, hash, last_modified_before) 
			SELECT 'customer' as 'object_type', t.id, t.current_hash as 'hash', UTC_TIMESTAMP() as 'last_modified_before' FROM (
			SELECT u.ID AS  'id', MD5( CONCAT_WS(  ',', u.user_email, u.user_status, u.display_name, GROUP_CONCAT( CONCAT_WS(  ',', um.meta_key, um.meta_value ) ) ) ) AS  'current_hash'
			FROM  `" . $wpdb->prefix . "users` u
			LEFT JOIN  `" . $wpdb->prefix . "usermeta` um ON u.ID = um.user_id
			GROUP BY u.ID ) t
			LEFT JOIN `" . $wpdb->prefix . "osapi_last_modified` olm on olm.id = t.ID
			WHERE (olm.object_type='customer' or olm.object_type is null)
			AND (olm.hash != t.current_hash or olm.hash is null)
			ON DUPLICATE KEY UPDATE hash = t.current_hash, last_modified_before = UTC_TIMESTAMP();";
			
			$wpdb->query($insert);
			
		break;
		default:
		break;
	}
}*/

function checkCreateTable() {
	global $wpdb;
		$collate = '';
	if ($wpdb->has_cap('collation')) {
		if(!empty($wpdb->charset))
			$collate .= "DEFAULT CHARACTER SET $wpdb->charset";
		if(!empty($wpdb->collate ) )
			$collate .= " COLLATE $wpdb->collate";
	}
	$sql = "
	CREATE TABLE IF NOT EXISTS " . $wpdb->prefix . "osapi_last_modified (
		object_type ENUM('customer') NOT NULL, 
		id bigint(20) NOT NULL, 
		hash VARCHAR(255) not null, 
		last_modified_before DATETIME NOT NULL, 
		PRIMARY KEY(object_type, id)
	) $collate;";

	$wpdb->query($sql);
}

function parseSingleStockUpdateRequest (SimpleXmlElement $aRequest) {
	$stockUpdateRequest = array();
	if (!is_null($aRequest) && $aRequest->getName()==='ProductStockUpdate') {
		foreach ($aRequest->attributes() as $attr) {
			if ($attr->getName() === 'Id') {
				$stockUpdateRequest['ProductCode'] = 0 + $attr;
			}
		}
		foreach ($aRequest->children() as $child) {
			switch ($child->getName()) {
				case 'StockAtHand':
					$stockUpdateRequest['StockAtHand'] = $child;
					break;
				case 'StockAllocated':
					$stockUpdateRequest['StockAllocated'] = $child;
					break;
				case 'StockAvailable':
					$stockUpdateRequest['StockAvailable'] = (int) $child;
					break;
				default:
					// Not interested
					break;
			}
		}
		$stockUpdateRequest;
	}
	return $stockUpdateRequest;
}

function xml_entities($string) {
    return strtr(
        $string, 
        array(
            "<" => "&lt;",
            ">" => "&gt;",
            '"' => "&quot;",
            "'" => "&apos;",
            "&" => "&amp;",
        )
    );
}
function get_rate_percent( $key_or_rate ) {
    global $wpdb;

    if ( is_object( $key_or_rate ) ) {
      $key      = $key_or_rate->tax_rate_id;
      $tax_rate = $key_or_rate->tax_rate;
    } else {
      $key      = $key_or_rate;
      $tax_rate = $wpdb->get_var( $wpdb->prepare( "SELECT tax_rate FROM {$wpdb->prefix}woocommerce_tax_rates WHERE tax_rate_id = %s", $key ) );
    }

    return apply_filters( 'woocommerce_rate_percent', floatval( $tax_rate ) , $key );
}
function get_variations($product) {

	    $available_variations = array();

		foreach ( $product->get_children() as $child_id ) {

			$variation = $product->get_child( $child_id );

			// Filter 'woocommerce_hide_invisible_variations' to optionally hide invisible variations (disabled variations and variations with empty price)
			if ( apply_filters( 'woocommerce_hide_invisible_variations', false, $product->id ) && ! $variation->variation_is_visible() ) {
				continue;
			}

			$variation_attributes = $variation->get_variation_attributes();
			$availability         = $variation->get_availability();
			$availability_html    = empty( $availability['availability'] ) ? '' : '<p class="stock ' . esc_attr( $availability['class'] ) . '">' . wp_kses_post( $availability['availability'] ) . '</p>';
			$availability_html    = apply_filters( 'woocommerce_stock_html', $availability_html, $availability['availability'], $variation );

			if ( has_post_thumbnail( $variation->get_variation_id() ) ) {
				$attachment_id = get_post_thumbnail_id( $variation->get_variation_id() );

				$attachment    = wp_get_attachment_image_src( $attachment_id, apply_filters( 'single_product_large_thumbnail_size', 'shop_single' )  );
				$image         = $attachment ? current( $attachment ) : '';

				$attachment    = wp_get_attachment_image_src( $attachment_id, 'full'  );
				$image_link    = $attachment ? current( $attachment ) : '';

				$image_title   = get_the_title( $attachment_id );
				$image_alt     = get_post_meta( $attachment_id, '_wp_attachment_image_alt', true );
			} else {
				$image = $image_link = $image_title = $image_alt = '';
			}

			$available_variations[] = apply_filters( 'woocommerce_available_variation', array(
				'variation_id'          => $child_id,
				'variation_is_visible'  => $variation->variation_is_visible(),
				'variation_is_active'   => $variation->variation_is_active(),
				'is_purchasable'        => $variation->is_purchasable(),
				'display_price'         => $variation->get_display_price(),
				'display_regular_price' => $variation->get_display_price( $variation->get_regular_price() ),
				'attributes'            => $variation_attributes,
				'image_src'             => $image,
				'image_link'            => $image_link,
				'image_title'           => $image_title,
				'image_alt'             => $image_alt,
				'price_html'            => $variation->get_price() === "" || $product->get_variation_price( 'min' ) !== $product->get_variation_price( 'max' ) ? '<span class="price">' . $variation->get_price_html() . '</span>' : '',
				'availability_html'     => $availability_html,
				'sku'                   => $variation->get_sku(),
				'weight'                => $variation->get_weight() . ' ' . esc_attr( get_option('woocommerce_weight_unit' ) ),
				'dimensions'            => $variation->get_dimensions(),
				'min_qty'               => 1,
				'max_qty'               => $variation->backorders_allowed() ? '' : $variation->get_stock_quantity(),
				'backorders_allowed'    => $variation->backorders_allowed(),
				'is_in_stock'           => $variation->is_in_stock(),
				'is_downloadable'       => $variation->is_downloadable() ,
				'is_virtual'            => $variation->is_virtual(),
				'is_sold_individually'  => $variation->is_sold_individually() ? 'yes' : 'no',
			), $product, $variation );
		}

		return $available_variations;
}

function item_subtotal( $item, $inc_tax = false, $round = true ) {
        if ( $inc_tax ) {
            $price = ( $item['line_subtotal'] + $item['line_subtotal_tax'] ) / max( 1, $item['qty'] );
        } else {
            $price = ( $item['line_subtotal'] / max( 1, $item['qty'] ) );
        }

        $price = $round ? number_format( (float) $price, wc_get_price_decimals(), '.', '' ) : $price;

        return apply_filters( 'woocommerce_order_amount_item_subtotal', $price, $item, $inc_tax, $round );
    }
	
function item_total( $item, $inc_tax = false, $round = true ) {

        $qty = ( ! empty( $item['qty'] ) ) ? $item['qty'] : 1;

        if ( $inc_tax ) {
            $price = ( $item['line_total'] + $item['line_tax'] ) / max( 1, $qty );
        } else {
            $price = $item['line_total'] / max( 1, $qty );
        }

        $price = $round ? round( $price, wc_get_price_decimals() ) : $price;

        return apply_filters( 'woocommerce_order_amount_item_total', $price, $item, $inc_tax, $round );
    }
?>
