<?php
/**
 * YITH
 * 
 * @package WordPress
 * @subpackage YITH
 * @author YITH <plugins@yithemes.com>
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */

if( ! class_exists( 'YITH_Dashboard' ) ){
	/**
	 * Wordpress Admin Dashboard Management
	 *
	 * @since 1.0.0
	 */
	class YITH_Dashboard {
		/**
		 * Products URL
		 *
		 * @var string
		 * @access protected
		 * @since 1.0.0
		 */
		static protected $_products_feed = 'https://yithemes.com/feed/?post_type=product';
		static protected $_blog_feed     = 'https://yithemes.com/feed/';

		/**
		 * Dashboard widget setup
		 *
		 * @return void
		 * @since 1.0.0
		 * @access public
		 */
		public static function dashboard_widget_setup() {
			wp_add_dashboard_widget( 'yith_dashboard_products_news', __( 'New YITH products' , 'yith-plugin-fw' ), 'YITH_Dashboard::dashboard_products_news' );
			wp_add_dashboard_widget( 'yith_dashboard_blog_news', __( 'News from the YITH Blog' , 'yith-plugin-fw' ), 'YITH_Dashboard::dashboard_blog_news' );
		}


		/**
		 * Product news Widget
		 *
		 * @return void
		 * @since 1.0.0
		 * @access public
		 */
		public static function dashboard_products_news() {
			$args = array( 'show_author' => 0, 'show_date' => 1, 'show_summary' => 1, 'items'=> 3 );
			wp_widget_rss_output( static::$_products_feed, $args );
		}


		/**
		 * Blog news Widget
		 *
		 * @return void
		 * @since 1.0.0
		 * @access public
		 */
		public static function dashboard_blog_news() {
			$args = array( 'show_author' => 0, 'show_date' => 1, 'show_summary' => 1, 'items'=> 3 );
			wp_widget_rss_output( static::$_blog_feed, $args );
		}
	}
	if( apply_filters( 'yith_plugin_fw_show_dashboard_widgets', true ) ){
		add_action( 'wp_dashboard_setup', 'YITH_Dashboard::dashboard_widget_setup' );
	}
}

