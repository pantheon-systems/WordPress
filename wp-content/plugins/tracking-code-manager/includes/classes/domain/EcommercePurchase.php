<?php
if (!defined('ABSPATH')) exit;
class TCMP_EcommercePurchase {
    var $orderId;
    var $currency;

    var $userId;
    var $fullname;
    var $email;

    var $products=array();

    var $amount;
    var $total;
    var $tax;
}