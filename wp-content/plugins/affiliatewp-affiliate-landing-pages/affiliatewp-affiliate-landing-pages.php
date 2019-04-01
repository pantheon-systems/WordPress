<?php
/**
 * Plugin Name: AffiliateWP - Affiliate Landing Pages
 * Plugin URI: https://affiliatewp.com/
 * Description: Create dedicated landing pages for your affiliates, which they can promote without using an affiliate link.
 * Author: AffiliateWP, LLC
 * Author URI: https://affiliatewp.com
 * Version: 1.0.2
 * Text Domain: affiliatewp-affiliate-landing-pages
 * Domain Path: languages
 *
 * AffiliateWP is distributed under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * any later version.
 *
 * AffiliateWP is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with AffiliateWP. If not, see <http://www.gnu.org/licenses/>.
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'AffiliateWP_Affiliate_Landing_Pages' ) ) {

	/**
	 * Setup class.
	 *
	 * @since 1.0
	 */
	final class AffiliateWP_Affiliate_Landing_Pages {

		/**
		 * Holds the instance.
		 *
		 * Ensures that only one instance of AffiliateWP_Affiliate_Landing_Pages exists in memory at any one
		 * time and it also prevents needing to define globals all over the place.
		 *
		 * TL;DR This is a static property property that holds the singleton instance.
		 *
		 * @access private
		 * @var    \AffiliateWP_Affiliate_Landing_Pages
		 * @static
		 *
		 * @since 1.0
		 */
		private static $instance;

		/**
		 * The version number
		 *
		 * @access private
		 * @since 1.0
		 */
		private static $version;

		/**
		 * The filesystem directory path (with trailing slash) for the plugin __FILE__ passed in.
		 *
		 * @access private
		 * @since 1.0
		 * @static
		 */
		private static $plugin_dir;

		/**
		 * Generates the main AffiliateWP_Affiliate_Landing_Pages instance.
		 *
		 * Insures that only one instance of AffiliateWP_Affiliate_Landing_Pages exists in memory at any one
		 * time. Also prevents needing to define globals all over the place.
		 *
		 * @access	public
		 * @since	1.0
		 * @static
		 *
		 * @return \AffiliateWP_Affiliate_Landing_Pages The one true AffiliateWP_Affiliate_Landing_Pages.
		 */
		public static function instance() {
			if ( ! isset( self::$instance ) && ! ( self::$instance instanceof AffiliateWP_Affiliate_Landing_Pages ) ) {

				self::$instance = new AffiliateWP_Affiliate_Landing_Pages;
				self::$version  = '1.0.2';

				self::$instance->load_textdomain();
				self::$instance->includes();
				self::$instance->init();
				self::$instance->hooks();

				self::$plugin_dir = plugin_dir_path( __FILE__ );

			}

			return self::$instance;
		}

		/**
		 * Throws an error on object clone.
		 *
		 * The whole idea of the singleton design pattern is that there is a single
		 * object therefore, we don't want the object to be cloned.
		 *
 		 * @access protected
		 * @since  1.0
		 *
		 * @return void
		 */
		protected function __clone() {
			// Cloning instances of the class is forbidden
			_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'affiliatewp-affiliate-landing-pages' ), '1.0' );
		}

		/**
		 * Disables unserializing of the class.
		 *
		 * @access protected
		 * @since  1.0
		 *
		 * @return void
		 */
		protected function __wakeup() {
			// Unserializing instances of the class is forbidden
			_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'affiliatewp-affiliate-landing-pages' ), '1.0' );
		}

		/**
		 * Sets up the class.
		 *
		 * @access private
		 * @since  1.0
		 */
		private function __construct() {
			self::$instance = $this;
		}

		/**
		 * Resets the instance of the class.
		 *
		 * @access public
		 * @since  1.0
		 * @static
		 */
		public static function reset() {
			self::$instance = null;
		}

		/**
		 * Loads the plugin language files.
		 *
		 * @access public
		 * @since  1.0
		 *
		 * @return void
		 */
		public function load_textdomain() {

			// Set filter for plugin's languages directory.
			$lang_dir = dirname( plugin_basename( __FILE__ ) ) . '/languages/';

			/**
			 * Filters the languages directory for AffiliateWP Affiliate Landing Pages plugin.
			 *
			 * @since 1.0
			 *
			 * @param string $lang_dir Language directory.
			 */
			$lang_dir = apply_filters( 'affiliatewp_affiliate_landing_pages_languages_directory', $lang_dir );

			// Traditional WordPress plugin locale filter.
			$locale   = apply_filters( 'plugin_locale',  get_locale(), 'affiliatewp-affiliate-landing-pages' );
			$mofile   = sprintf( '%1$s-%2$s.mo', 'affiliatewp-affiliate-landing-pages', $locale );

			// Setup paths to current locale file.
			$mofile_local  = $lang_dir . $mofile;
			$mofile_global = WP_LANG_DIR . '/affiliatewp-affiliate-landing-pages/' . $mofile;

			if ( file_exists( $mofile_global ) ) {
				// Look in global /wp-content/languages/affiliatewp-affiliate-landing-pages/ folder.
				load_textdomain( 'affiliatewp-affiliate-landing-pages', $mofile_global );
			} elseif ( file_exists( $mofile_local ) ) {
				// Look in local /wp-content/plugins/affiliatewp-affiliate-landing-pages/languages/ folder.
				load_textdomain( 'affiliatewp-affiliate-landing-pages', $mofile_local );
			} else {
				// Load the default language files.
				load_plugin_textdomain( 'affiliatewp-affiliate-landing-pages', false, $lang_dir );
			}
		}

		/**
		 * Include necessary files.
		 *
		 * @access private
		 * @since  1.0
		 *
		 * @return void
		 */
		private function includes() {

			if ( is_admin() ) {
				require_once self::$plugin_dir . 'includes/class-metabox.php';
				require_once self::$plugin_dir . 'includes/class-settings.php';
			}

			require_once self::$plugin_dir . 'includes/functions.php';
			require_once self::$plugin_dir . 'includes/class-shortcodes.php';
		}

		/**
		 * Sets up the default hooks and actions.
		 *
		 * @access private
		 * @since  1.0
		 *
		 * @return void
		 */
		private function hooks() {

			// Plugin meta.
			add_filter( 'plugin_row_meta', array( $this, 'plugin_meta' ), null, 2 );

			// List an affiliate's landing pages.
			add_action( 'affwp_affiliate_dashboard_urls_top', array( $this, 'list_landing_pages' ), 10, 1 );

			if ( true === affwp_alp_is_enabled() ) {

				add_action( 'admin_enqueue_scripts', array( $this, 'load_admin_scripts' ) );
				add_action( 'wp_enqueue_scripts', array( $this, 'dequeue_tracking' ) );

				if ( ! affiliate_wp()->tracking->use_fallback_method() ) {
					add_action( 'wp_footer', array( $this, 'track_visit' ), 100 );
				} else {
					add_action( 'template_redirect', array( $this, 'fallback_track_visit' ), -9999 );
				}
			}

		}

		/**
		 * Init
		 *
		 * @access private
		 * @since  1.0
		 *
		 * @return void
		 */
		private function init() {
			if ( is_admin() ) {
				self::$instance->updater();
			}
		}

		/**
		 * Load the custom plugin updater
		 *
		 * @access private
		 * @since  1.0
		 *
		 * @return void
		 */
		public function updater() {
			if ( class_exists( 'AffWP_AddOn_Updater' ) ) {
				$updater = new AffWP_AddOn_Updater( 167098, __FILE__, self::$version );
			}
		}

		/**
		 * Dequeue AffiliateWP's tracking JS file if an affiliate link is used on a landing page.
		 *
		 * @since 1.0
		 *
		 * @return void
		 */
		public function dequeue_tracking() {

			// Remove tracking script if the landing page is assigned to an affiliate
			if ( $this->get_affiliate_id( get_the_ID() ) ) {
				wp_dequeue_script( 'affwp-tracking' );
			}

		}

		/**
		 * List an affiliate's landing pages
		 *
		 * @since  1.0
		 * @access public
		 *
		 * @param  int $affiliate_id The affiliate's ID
		 * @return void
		 */
		public function list_landing_pages( $affiliate_id = 0 ) {

			$affiliate_user_name = affwp_get_affiliate_username( $affiliate_id );
			$landing_page_ids    = affwp_alp_get_landing_page_ids( $affiliate_user_name );

			$text = count( $landing_page_ids ) === 1 ? __( 'Your landing page:', 'affiliatewp-affiliate-landing-pages' ) : __( 'Your landing pages:', 'affiliatewp-affiliate-landing-pages' );
		?>
			<?php if ( ! empty( $landing_page_ids ) ) : ?>
			<p><?php echo $text; ?></p>
			<p>
				<?php foreach ( $landing_page_ids as $id ) : ?>
					<?php echo get_permalink( $id ); ?><br>
				<?php endforeach; ?>
			</p>
			<?php endif; ?>

		<?php
		}

		/**
	     * Load the admin scripts
	     *
	     * @since  1.0
	     * @access public
	     *
	     * @param  string $hook The hook suffix
	     * @return void
	     */
		public function load_admin_scripts( $hook ) {

			if ( $hook === 'post.php' || $hook === 'post-new.php' ) {
				affwp_enqueue_admin_js();

				$ui_style = ( 'classic' == get_user_option( 'admin_color' ) ) ? 'classic' : 'fresh';
				wp_enqueue_style( 'jquery-ui-css', AFFILIATEWP_PLUGIN_URL . 'assets/css/jquery-ui-' . $ui_style . '.min.css' );
			}

		}

		/**
	     * Retrieves the affiliate ID from the post or page
	     *
	     * @since 1.0
	     * @access public
	     *
	     * @param int $post_id Post ID
	     * @return mixed bool|int Affiliate ID or false if not found.
	     */
		public function get_affiliate_id( $post_id ) {

			// Get the affiliate username.
			$user_name = get_post_meta( $post_id, 'affwp_landing_page_user_name', true );

			if ( ! empty( $user_name ) ) {
				$affiliate    = affwp_get_affiliate( $user_name );
				$affiliate_id = $affiliate->affiliate_id;

				if ( $affiliate_id ) {
					return (int) $affiliate_id;
				}
			}

			return false;

		}

		/**
	     * Store the visit
	     *
	     * @since 1.0
	     * @access public
	     *
	     * @return void
	     */
		public function track_visit() {

			$affiliate_id = $this->get_affiliate_id( get_the_ID() );

			if ( empty( $affiliate_id ) ) {
				return;
			}

		?>
			<script>
			jQuery(document).ready( function($) {

				// Affiliate ID
				var ref = "<?php echo $affiliate_id; ?>";
				var ref_cookie = $.cookie( 'affwp_ref' );
				var credit_last = AFFWP.referral_credit_last;
				var campaign = affwp_alp_get_query_vars()['campaign'];

				if ( '1' != credit_last && ref_cookie ) {
					return;
				}

				// If a referral var is present and a referral cookie is not already set
				if ( ref && ! ref_cookie ) {
					affwp_track_visit( ref, campaign );
				} else if( '1' == credit_last && ref && ref_cookie && ref !== ref_cookie ) {
					$.removeCookie( 'affwp_ref' );
					affwp_track_visit( ref, campaign );
				}

				// Track the visit
				function affwp_track_visit( affiliate_id, url_campaign ) {

					// Set the cookie and expire it after 24 hours
					affwp_set_cookie( 'affwp_ref', affiliate_id, { expires: AFFWP.expiration, path: '/' } );

					// Fire an ajax request to log the hit
					$.ajax({
						type: "POST",
						data: {
							action: 'affwp_track_visit',
							affiliate: affiliate_id,
							campaign: url_campaign,
							url: document.URL,
							referrer: document.referrer
						},
						url: affwp_scripts.ajaxurl,
						success: function (response) {
							affwp_set_cookie( 'affwp_ref_visit_id', response, { expires: AFFWP.expiration, path: '/' } );
							affwp_set_cookie( 'affwp_campaign', url_campaign, { expires: AFFWP.expiration, path: '/' } );
						}

					}).fail(function (response) {
						if ( window.console && window.console.log ) {
							console.log( response );
						}
					});

				}

				/**
				 * Gets url query variables from the current URL.
				 *
				 * @since  1.0
				 *
				 * @return {array} vars The url query variables in the current site url, if present.
				 */
				function affwp_alp_get_query_vars() {
					var vars = [], hash;
					var hashes = window.location.href.slice(window.location.href.indexOf('?') + 1).split('&');
					for(var i = 0; i < hashes.length; i++) {
						hash = hashes[i].split('=');
						vars.push(hash[0]);

						var key = typeof hash[1] == 'undefined' ? 0 : 1;

						// Remove fragment identifiers
						var n = hash[key].indexOf('#');
						hash[key] = hash[key].substring(0, n != -1 ? n : hash[key].length);
						vars[hash[0]] = hash[key];
					}
					return vars;
				}

				/**
				 * Set a cookie, with optional domain if set. Note that providing *any* domain will
				 * set the cookie domain with a leading dot, indicating it should be sent to sub-domains.
				 *
				 * example: host.tld
				 *
				 * - $.cookie( 'some_cookie', ...) = cookie domain: host.tld
				 * - $.cookie ('some_cookie', ... domain: 'host.tld' ) = .host.tld
				 *
				 * @since 2.x.x
				 *
				 * @param {string} name cookie name, e.g. affwp_ref
				 * @param {string} value cookie value
				 */
				function affwp_set_cookie( name, value ) {

					if ( 'cookie_domain' in AFFWP ) {
						$.cookie( name, value, { expires: AFFWP.expiration, path: '/', domain: AFFWP.cookie_domain } );
					} else {
						$.cookie( name, value, { expires: AFFWP.expiration, path: '/' } );
					}
				}

			});

			</script>
			<?php
		}

		/**
		 * Record referral visit via template_redirect
		 *
		 * @since 1.0
		 *
		 * @return void
		 */
		public function fallback_track_visit() {

			$affiliate_id = $this->get_affiliate_id( get_the_ID() );

			if ( empty( $affiliate_id ) ) {
				return;
			}

			$is_valid_affiliate = affiliate_wp()->tracking->is_valid_affiliate( $affiliate_id );
			$visit_id           = affiliate_wp()->tracking->get_visit_id();

			if ( $is_valid_affiliate && ! $visit_id ) {

				if ( ( ! empty( $_SERVER['HTTP_REFERER'] ) && ! affwp_is_url_banned( sanitize_text_field( $_SERVER['HTTP_REFERER'] ) ) )
					|| empty( $_SERVER['HTTP_REFERER'] )
				) {

					// Set affiliate ID
					affiliate_wp()->tracking->set_affiliate_id( $affiliate_id );

					// Store the visit in the DB
					$visit_id = affiliate_wp()->visits->add( array(
						'affiliate_id' => $affiliate_id,
						'ip'           => affiliate_wp()->tracking->get_ip(),
						'url'          => affiliate_wp()->tracking->get_current_page_url(),
						'campaign'     => affiliate_wp()->tracking->get_campaign(),
						'referrer'     => ! empty( $_SERVER['HTTP_REFERER'] ) ? $_SERVER['HTTP_REFERER'] : ''
					) );

					// Set visit
					affiliate_wp()->tracking->set_visit_id( $visit_id );

				}

			}

		}

		/**
		 * Modifies the plugin list table meta links.
		 *
		 * @access public
		 * @since  1.0
		 *
		 * @param  array  $links The current links array.
		 * @param  string $file  A specific plugin table entry.
		 * @return array The modified links array.
		 */
		public function plugin_meta( $links, $file ) {

		    if ( $file == plugin_basename( __FILE__ ) ) {

				$url = admin_url( 'admin.php?page=affiliate-wp-add-ons' );

				$plugins_link = array( '<a title="' . esc_attr__( 'Get more add-ons for AffiliateWP', 'affiliatewp-affiliate-landing-pages' ) . '" href="' . esc_url( $url ) . '">' . __( 'More add-ons', 'affiliatewp-affiliate-landing-pages' ) . '</a>' );

		        $links = array_merge( $links, $plugins_link );
		    }

		    return $links;

		}
	}

	/**
	 * The main function responsible for returning the one true AffiliateWP_Affiliate_Landing_Pages
	 * Instance to functions everywhere.
	 *
	 * Use this function like you would a global variable, except without needing
	 * to declare the global.
	 *
	 * Example: <?php $affiliatewp_affiliate_landing_pages = affiliatewp_affiliate_landing_pages(); ?>
	 *
	 * @since  1.0
	 *
	 * @return object The one true AffiliateWP_Affiliate_Landing_Pages Instance
	 */
	function affiliatewp_affiliate_landing_pages() {

	    if ( ! class_exists( 'Affiliate_WP' ) ) {
	        if ( ! class_exists( 'AffiliateWP_Activation' ) ) {
	            require_once 'includes/class-activation.php';
	        }

	        $activation = new AffiliateWP_Activation( plugin_dir_path( __FILE__ ), basename( __FILE__ ) );
	        $activation = $activation->run();
	    } else {
	        return AffiliateWP_Affiliate_Landing_Pages::instance();
	    }

	}
	add_action( 'plugins_loaded', 'affiliatewp_affiliate_landing_pages', 100 );

}
