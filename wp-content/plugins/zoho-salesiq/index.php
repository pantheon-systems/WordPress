<?php

ob_start();

/*
Plugin Name: Zoho SalesIQ
Plugin URI: http://wordpress.org/plugins/zoho-salesiq/
Description: Convert Website Visitors into Customers
Author: Zoho SalesIQ Team
Version: 1.0.8
Author URI: http://zoho.com/salesiq
*/



add_action('admin_menu', 'ld_menu');   


function ld_menu() {
   add_menu_page('Account Configuration', 'Zoho SalesIQ', 'administrator', 'LD_dashboard', 'LD_dashboard',plugins_url().'/zoho-salesiq/favicon.png', '79');
    

  }


function LD_dashboard() {
include ('salesiq.php');
}




function ld_embedchat()
{
    $ldcode_str = trim(get_option('ldcode'));

    if ( !preg_match( "/^<script[^>]*>\s*.+\s*(float\.ls|.+widgetcode.+\/widget)\s*.+\s*<\/script>$/s", $ldcode_str ) )
    {
         return;
    }
    
    $ldcode_str .= '<script> $zoho.salesiq.internalready = function() { ';

    if ( is_user_logged_in() ) # wp method
    {
         $current_user = wp_get_current_user();
         if ( $current_user instanceof WP_User )
         {
              $ldcode_str .= '$zoho.salesiq.visitor.name("' . $current_user->user_login . '");';
              $ldcode_str .= '$zoho.salesiq.visitor.email("' . $current_user->user_email . '");';
         }
    }

    if (trim(get_option('hidecode')) == 1)
    {
         $ldcode_str .= '$zoho.salesiq.floatbutton.visible("hide");';
    }
    $ldcode_str .= '}</script>';
    echo $ldcode_str;
}


add_action("wp_footer","ld_embedchat", 5);

?>
