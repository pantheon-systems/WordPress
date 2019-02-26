<?php 
if ( ! defined( 'ABSPATH' ) ) exit;
/**
 * WooCommercePoorGuysSwissKnife About Class
 *
 * @class 		WCPGSK_About
 * @version		1.1
 * @package		WooCommerce-Poor-Guys-Swiss-Knife/Classes
 * @category	Class
 * @author 		Uli Hake
 * @since 		1.1.1
 */
 
if ( ! class_exists ( 'WCPGSK_About' ) ) {

    class WCPGSK_About {
		private $dir;
		private $assets_dir;
		private $assets_url;
		private $file;
		
		/**
		 * Constructor function.
		 *
		 * @access public
		 * @since 1.1.1
		 * @return void
		 */
		public function __construct( $file ) {
			global $wcpgsk;
			$this->dir = dirname( $file );
			$this->file = $file;
			$this->assets_dir = trailingslashit( $this->dir ) . 'assets';
			$this->assets_url = esc_url( trailingslashit( plugins_url( '/assets/', $file ) ) );
			$active_plugins = (array) get_option( 'active_plugins', array() );
			if ( is_multisite() ) :
				$active_plugins = array_merge( $active_plugins, get_site_option( 'active_sitewide_plugins', array() ) );
			endif;
			if ( !in_array( 'woocommerce-rich-guys-swiss-knife/woocommerce-rich-guys-swiss-knife.php', $active_plugins ) || array_key_exists( 'woocommerce-rich-guys-swiss-knife/woocommerce-rich-guys-swiss-knife.php', $active_plugins ) ) :
				add_action( 'wcpgsk_settings_page_about', array($this, 'wcpgsk_settings_page_about'), 10, 1 );
			endif;
		}
		
		/**
		 * WCPGSK About .
		 *
		 * @access public
		 * @param $options (contains wcpgsk settings)
		 * @since 1.1.1
		 * @output html
		 */
		public function wcpgsk_settings_page_about($options) {
			global $wcpgsk;
		?>
		
			<h3 class="wcpgsk_acc_header"><?php echo __('About WooCommerce Poor Guys Swiss Knife',WCPGSK_DOMAIN); ?></h3>
			<div>
			
				<div class="postbox" style="display:block;width:100%;margin:10px;clear:left;">
					WooCommerce Poor Guys Swiss Knife <?php _e('Version', WCPGSK_DOMAIN); ?>: <strong><?php echo $wcpgsk->version; ?></strong>
					<br /><br />
					<h3 style="padding:5px;"><span><?php _e("Useful links:", WCPGSK_DOMAIN); ?></span></h3>
					<div class="inside">
						<a href="http://wordpress.org/support/plugin/<?php echo WCPGSK_SLUG; ?>" class="button-secondary button-large"><?php _e("Support Forum", WCPGSK_DOMAIN); ?></a>
						<a href="http://wordpress.org/extend/plugins/<?php echo WCPGSK_SLUG; ?>" class="button-secondary" button-large><?php _e("Rate this plugin", WCPGSK_DOMAIN); ?></a>
						<a href="http://wordpress.org/support/view/plugin-reviews/<?php echo WCPGSK_SLUG; ?>" class="button-secondary button-large"><?php _e("Write a review about this plugin", WCPGSK_DOMAIN); ?></a>
						<a href="http://profiles.wordpress.org/ulih/"  class="button-secondary button-large" title="on WordPress.org"><?php _e("Wordpress Profile of the author", WCPGSK_DOMAIN); ?></a>
					</div>
				</div>	
				<div class="postbox" style="display:block;width:100%;margin:10px;clear:left;">
					<h3 style="padding:5px;"><span><?php _e("Like this plugin?", WCPGSK_DOMAIN); ?></span></h3>
					<div class="inside">
						<p><a href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=KJ4K2X953H8CC" target="_blank"><img title="Thank you in advance!" src="https://www.paypalobjects.com/en_US/ES/i/btn/btn_donateCC_LG.gif" /></a></p>
					</div>
				</div>	
				<div class="postbox" style="display:block;width:100%;margin:10px;clear:left;">
					<h3 style="padding:5px;"><span><?php _e("Check out these themes and plugins too:", WCPGSK_DOMAIN); ?></span></h3>
					<div class="inside">
						<ul>
							<li><a href="http://takebarcelona.com/woocommerce-rich-guys-swiss-knife/">WooCommerce Rich Guys Swiss Knife</a> - <em><?php _e("The big brother of WooCommerce Poor Guys Swiss Knife", WCPGSK_DOMAIN); ?></em></li>
							<li><a href="http://takebarcelona.com/tessa-authorship/">Tessa Authorship</a> - <em><?php _e("User independent authorship reflection for WordPress", WCPGSK_DOMAIN); ?></em></li>
							<li><a href="http://takebarcelona.com/tessa-theme">Tessa</a> - <em><?php _e("A gallery, exposition and portfolio theme with built-in support for WooCommerce", WCPGSK_DOMAIN); ?></em></li>
						</ul>
					</div>
				</div>		
				<div class="postbox" style="display:block;width:100%;margin:10px;clear:left;">
					<h3 style="padding:5px;"><span><?php _e("Sponsors:", WCPGSK_DOMAIN); ?></span></h3>
					<div class="inside">
						<ul>
							<li><a href="http://nicestay.net/">Nicestay</a> - <em><?php _e("Company that offers short term rentals of apartments and accommodations for holidays and business, based in Barcelona, Catalonia, Spain", WCPGSK_DOMAIN); ?></em></li>
						</ul>
					</div>
				</div>					
			</div>
		<?php
		}
	}
}