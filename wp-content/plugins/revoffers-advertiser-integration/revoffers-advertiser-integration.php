<?php
/*
Plugin Name: RevOffers Advertiser Integration
Description: Integrate RevOffers tracking snippet to reward influencers driving customers to your site.
Version: 1.2.7
Author: RevOffers
Author URI: https://www.revoffers.com/
*/

define('REVOFFERS_TRACK_COOKIE_NAME', 'revoffers_affil');
define('REVOFFERS_META_KEY', '_revoffers_visitor');
define('REVOFFERS_ENDPOINT', 'https://db.revoffers.com/v2/_tr');

register_activation_hook(__FILE__, 'revoffers_activate');

// Run RevOffersJs at the bottom of each page
add_action('wp_footer', 'revoffersjs');

// Trigger conversion
//BAD: add_action('woocommerce_thankyou', 'revoffers_track_order');
// @see https://docs.woocommerce.com/wc-apidocs/hook-docs.html
// @see https://squelchdesign.com/web-design-newbury/woocommerce-detecting-order-complete-on-order-completion/
add_action('woocommerce_order_status_processing', 'revoffers_track_order');

// Store VID info within order
add_action('woocommerce_checkout_create_order', 'revoffers_checkout_create_order');

if (is_admin()) {
    include_once dirname(__FILE__) . '/admin_func.php';
    add_filter('plugin_action_links_' . plugin_basename(__FILE__), 'revoffers_admin_settings_link');
}


//
// Static symbols
//

function revoffers_activate() {
    //update_option('revoffers_company_key', '');
    if (!function_exists('curl_init')) {
    	throw new \RuntimeException("PHP cURL extension must be installed to use RevOffers");
    }
    if (!function_exists('wc_get_order')) {
        throw new \RuntimeException("WooCommerce 3 (or higher) is required to use RevOffers");
    }

    // needed in case of upgrade where is_admin() might not have been called
    include_once dirname(__FILE__) . '/admin_func.php';
}

/**
 * Includes the RevOffers primary tracking script 
 * and order details if was not recorded offline.
 */
function revoffersjs() {
    $params = null;

    $cookieName = '_revoffers_need_convert';
    if (!empty($_COOKIE[$cookieName])) {
        $order_id = $_COOKIE[$cookieName];
        $params = revoffers_get_order_safe($order_id);
        // Delete value
        $_COOKIE[$cookieName] = null;
        setcookie($cookieName, '', 0, '/', '', false, true);
    }

    $site_id = get_option('revoffers_site_id');
    if ($site_id) $params['site_id'] = $site_id;

    if ($params) {
        // Print params object used by _track.js
        echo "\n<script type=\"text/javascript\">\n_revoffers_track = ";
        echo json_encode($params);
        echo ";\n</script>\n";
    }

    echo "\n<script type=\"text/javascript\" src=\"https://db.revoffers.com/_track.js\" async></script>\n";
}

/**
 * @param WC_Order $order
 */
function revoffers_checkout_create_order( $order/*, $data*/ ) {
	if (isset($_COOKIE[REVOFFERS_TRACK_COOKIE_NAME])) {
	    $data = null;
        $raw = $_COOKIE[REVOFFERS_TRACK_COOKIE_NAME];
        if (strpos($raw, '{') === 0) $data = json_decode($raw, true);
        if (!$data) parse_str($raw, $data);

        if (!$data) {
            $data = [];
            error_log(__FUNCTION__ . ": unable to parse revoffers cookie: $raw");
        }
        $data['client_ip'] = revoffers_get_client_ip();
        $data['user_agent'] = $_SERVER['HTTP_USER_AGENT'];
        $data = http_build_query($data, null, '&');

		$order->add_meta_data(REVOFFERS_META_KEY, $data, true);
	}
	// $order->save(); << will be executed after this hook
}

function revoffers_get_client_ip() {
    $ip = null;
    if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        if (($_ = strpos($ip, ',')) > 5) {
            $ip = substr($ip, $_);
        }
    } elseif (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    }
    if ((!$ip || !preg_match('#^\d+\.\d+\.\d+\.\d+$#', $ip)) && !empty($_SERVER['SERVER_ADDR'])) {
        // fallback to known-good state
        $ip = $_SERVER['SERVER_ADDR'];
    }
    return $ip;
}

/**
 * Uses cURL to send to RevOffers.
 *
 * @param string $order_id
 */
function revoffers_track_order( $order_id ) {
	$params = revoffers_get_order_safe($order_id, true);
    if (!$params) return;

    // The currently-connected browser must run JavaScript to have this cookie be set
    // WAIT! We might be sending a 301 response instead of echoing a response!
    //if (!empty($_COOKIE[REVOFFERS_TRACK_COOKIE_NAME])) goto set_cookie;

    list($wpVersion, $wooVersion) = revoffers_get_woocommerce_params($params);

	// Send tracking data
    $ch = curl_init(REVOFFERS_ENDPOINT);
    curl_setopt_array($ch, array(
    	CURLOPT_RETURNTRANSFER => true,
    	CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_MAXREDIRS => 2,
        CURLOPT_SSL_VERIFYPEER => false,// safe-guard against bad SSL
        CURLOPT_TIMEOUT => 30,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => http_build_query($params, null, '&'),
        CURLOPT_USERAGENT => "WordPress/$wpVersion WooCommerce/$wooVersion (RevOffers PHP plugin 1.1)",
    ));
    /*$output = */curl_exec($ch);
    $info = curl_getinfo($ch);
    curl_close($ch);

    if ($info['http_code'] == 200) return;// done!

    set_cookie:
    // Attempt to handle on-line later
    error_log(__FUNCTION__ . ": RevOffers tracking failed, attempting to use JavaScript");
    // this happens "offline" so mark the next page load to trigger conversion
    setcookie('_revoffers_need_convert', $order_id, 0, '/', '', false, true);
    $_COOKIE['_revoffers_need_convert'] = $order_id;
}

/**\
 * @param array $params
 * @return array
 */
function revoffers_get_woocommerce_params(&$params) {
    global $wp_version;
    global $woocommerce;

    $wooVersion = $woocommerce ? ($woocommerce->version ?: '<active_but_unknown>') : '<inactive>';
    $wpVersion = $wp_version ?: '<unknown>';

    // Determine server state
    $isHttps = !empty($_SERVER['HTTPS']) || (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && stripos($_SERVER['HTTP_X_FORWARDED_PROTO'], 'https') === 0);
    $site_host = parse_url(get_site_url(), PHP_URL_HOST);
    $host = !empty($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : $site_host;
    $uri = !empty($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '/<offline_processing>';

    // Populate params with session info
    $params['request_uri'] = ($isHttps ? 'https' : 'http') . "://" . $host . $uri;
    $params['site_id'] = get_option('revoffers_site_id') ?: ($site_host ?: $host);
    if (!empty($_SERVER['HTTP_REFERER'])) {
        $params['referrer'] = $_SERVER['HTTP_REFERER'];
    }
    $params['document_title'] = '<WooCommerce Offline Tracking>';
    $params['type'] = 'offline_conv';// override type when coming from WooCommerce

    return [$wpVersion, $wooVersion];
}

/**
 * @param int $order_id
 * @param bool $includeMeta
 * @return array
 */
function revoffers_get_order_safe($order_id, $includeMeta = false) {
    try {
        if (!$order_id) return null;
        if (function_exists('wc_get_order')) {
            // WooCommerce 3.x and up
            $order = wc_get_order( $order_id );
        } else {
            // WooCommerce up to 2.x
            $order = new WC_Order( $order_id );
        }
        if (!$order) return null;
        $params = array( 'action' => 'convert' );
        revoffers_get_order($order, $params);

        if ($includeMeta) {
            // Attach custom meta info
            $visitorInfo = $order->get_meta(REVOFFERS_META_KEY);
            if ($visitorInfo) {
                $data = null;
                if (strpos($visitorInfo, '{') === 0) $data = json_decode($visitorInfo, true);
                if (!$data) parse_str($visitorInfo, $data);
                if ($data) {
                    $data = array_merge($data, $params);
                    if ($data) {
                        $params = $data;
                    }
                }
            }

            $clientIp = get_post_meta($order_id, '_customer_ip_address', true);
            if ($clientIp) $params['client_ip'];
        }

        return $params;

    } catch (\Exception $e) {
        error_log(__FUNCTION__ . ": Getting RevOffers tracking info failed: $e", E_USER_ERROR);
        echo "<!-- $e -->\n";
        return null;
    }
}
/**
 * Fill $params object with info about the order.
 *
 * @param WC_Order $order
 * @param array $params
 */
function revoffers_get_order($order, &$params) {
    $order_data = $order->get_data(); // The Order data
    $order_id = $order_data['id'];
    $order_email = $order->get_billing_email();


    //
    // General Order Info
    //
    $params['order_id'] = $order_id;
    //$params['order_key'] = $order->get_order_key();
    $params['email_address'] = $order_email;
    //$params['customer_id'] = $order->get_customer_id();

    // IS PII:
    //$params['customer_name'] = $order->get_billing_first_name() . ' ' . $order->get_billing_last_name();

    // @see https://stackoverflow.com/questions/39401393/how-to-get-woocommerce-order-details

    //$order_parent_id = $order_data['parent_id'];
    //$order_customer_id = $order_data['customer_id'];
    //$order_status = $order_data['status'];
    //$order_currency = $order_data['currency'];
    //order_name: {{ checkout.name | json }},
    //order_phone: {{ checkout.billing_address.phone | json }},
    ## Creation and modified WC_DateTime Object date string ##
    // Using a formatted date ( with php date() function as method)
    //$order_date_created = $order_data['date_created']->date('Y-m-d H:i:s');
    //$order_date_modified = $order_data['date_modified']->date('Y-m-d H:i:s');
    $date = $order_data['date_created'];
    if ($date instanceof WC_DateTime)
        $date = "@" . $date->getTimestamp();
    if ($date) $params['order_time'] = $date;

    // Money
    //$order_discount_total = $order_data['discount_total'];
    //$order_discount_tax = $order_data['discount_tax'];
    $params['shipping_amount'] = $order_data['shipping_total'];
    //$order_shipping_tax = $order_data['shipping_tax'];
    $params['sale_amount'] = $order_data['total'];
    $params['sale_amount_with_currency'] = $order_data['total'] . ' ' . $order_data['currency'];
    $params['tax_amount'] = $order_data['total_tax'];
    $params['subtotal_amount'] = $order->get_subtotal();

    // Addresses
    //$params['billing_address'] = $order_data['billing']['address_1'];
    //$params['billing_address2'] = $order_data['billing']['address_2'];
    $params['billing_city'] = $order_data['billing']['city'];
    $params['billing_state'] = $order_data['billing']['state'];
    $params['billing_postal'] = $order_data['billing']['postcode'];
    //$params['shipping_address'] = $order_data['shipping']['address_1'];
    //$params['shipping_address2'] = $order_data['shipping']['address_2'];
    $params['shipping_city'] = $order_data['shipping']['city'];
    $params['shipping_state'] = $order_data['shipping']['state'];
    $params['shipping_postal'] = $order_data['shipping']['postcode'];

    //
    // Previous Order Information
    //
    // @see https://github.com/woocommerce/woocommerce/wiki/wc_get_orders-and-WC_Order_Query
    $order_count = wc_get_orders(array(
        'customer' => $order_email,
        //'customer_id' => $order->get_customer_id(),
        'type' => 'shop_order',
        'status' => 'completed',
        'limit' => 500,
        'return' => 'ids',
    ));
    if ($order_count) $params['order_count'] = count($order_count);

    // Most-recent order date
    $orderQuery = array(
        'customer' => $order_email,
        //'customer_id' => $order->get_customer_id(),
        'type' => 'shop_order',
        //'date_created' => '>' . (time() - 60*60*24*365),// within last year
        // avail statuses: pending, processing, on-hold, completed, refunded, failed
        'status' => 'completed',
        'exclude' => array($order_id),
        'limit' => 1,
        'orderby' => 'date', // or "modified"?
        'order' => 'DESC',
        'return' => 'objects',
    );
    $lastOrder = wc_get_orders($orderQuery);
    if ($lastOrder) {
        $lastOrder = $lastOrder[0];
        if (is_callable(array($lastOrder, 'get_data'))) $lastOrder = $lastOrder->get_data();
        $date = $lastOrder['date_created'];
        if ($date) {
            if ($date instanceof WC_DateTime) {
                $date = $date->date_i18n();
            }
            $params['last_order_date'] = $date;
            $params['first_order_date'] = $date;
        }
    }

    // First order date
    // reverse sorting order and try again
    $orderQuery['order'] = 'ASC';
    if ($lastOrder) $orderQuery['exclude'][] = $lastOrder['id'];
    $firstOrder = wc_get_orders($orderQuery);
    if ($firstOrder) {
        $firstOrder = $firstOrder[0];
        if (is_callable(array($firstOrder, 'get_data'))) $firstOrder = $firstOrder->get_data();
        $date = $firstOrder['date_created'];
        if ($date) {
            if ($date instanceof WC_DateTime) {
                $date = $date->date_i18n();
            }
            $params['first_order_date'] = $date;
        }
    }

    //
    // Product Line Items
    //
    $i = -1;
    foreach ($order->get_items() as $itemId => $item):
        /** @var WC_Product $product */
        $product = null;
        if (is_callable(array($item, 'get_data'))) {
            $product = $item->get_product();
            $item = $item->get_data();
        }
        //if (!($item instanceof WC_Order_Item_Product)) continue;
        $i++;

        $params["line_item_{$i}_title"] = $item['name'];
        $params["line_item_{$i}_sku"] = $product ? $product->get_sku() : null;
        $params["line_item_{$i}_var"] = $item['variation_id'];
        $params["line_item_{$i}_price"] = $product ? $product->get_price() : null;
        $params["line_item_{$i}_qty"] = $item['quantity'];
    endforeach;

    //
    // Coupon Line Items
    //
    // @see https://stackoverflow.com/questions/44977174/get-coupon-discount-type-and-amount-in-woocommerce-orders
    // @see https://docs.woocommerce.com/wc-apidocs/source-class-WC_Abstract_Order.html#680
    $i = -1;
    foreach ($order->get_items('coupon') as $item):
        if (is_callable(array($item, 'get_data'))) {
            $item = $item->get_data();
        }
        //if (!($item instanceof WC_Order_Item_Coupon)) continue;
        $i++;

        // ?: if (!$item->is_type('cash_back_fixed') && !$item->is_type('cash_back_percentage')) continue;

        $params["discount_{$i}_code"] = $item['code'];
        $params["discount_{$i}_amt"] = $item['discount'];
    endforeach;

    // end revoffers_get_order()
}
