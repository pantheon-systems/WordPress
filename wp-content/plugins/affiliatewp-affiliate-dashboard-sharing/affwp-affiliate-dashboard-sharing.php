<?php
/**
 * Plugin Name: AffiliateWP - Affiliate Dashboard Sharing
 * Plugin URI: https://affiliatewp.com/add-ons/pro/affiliate-dashboard-sharing/
 * Description: Easily allow your affiliates to share referral URLs generated from the affiliate dashboard
 * Author: AffiliateWP
 * Author URI: https://affiliatewp.com
 * Version: 1.1.6
 * Text Domain: affwp-affiliate-dashboard-sharing
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
 *
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'AffiliateWP_Affiliate_Dashboard_Sharing' ) ) {

	final class AffiliateWP_Affiliate_Dashboard_Sharing {

		/**
		 * Holds the instance
		 *
		 * Ensures that only one instance of Affiliate Sharing exists in memory at any one
		 * time and it also prevents needing to define globals all over the place.
		 *
		 * TL;DR This is a static property property that holds the singleton instance.
		 *
		 * @var object
		 * @static
		 * @since 1.0
		 */
		private static $instance;

		private static $plugin_dir;

		/**
		 * Plugin Version
		 */
		private static $version = '1.1.6';

		/**
		 * Main Instance
		 *
		 * Ensures that only one instance exists in memory at any one
		 * time. Also prevents needing to define globals all over the place.
		 *
		 * @since 1.0
		 *
		 */
		public static function get_instance() {

			if ( ! isset( self::$instance ) && ! ( self::$instance instanceof AffiliateWP_Affiliate_Dashboard_Sharing ) ) {

				self::$instance = new AffiliateWP_Affiliate_Dashboard_Sharing;

				self::$plugin_dir = plugin_dir_path( __FILE__ );

				self::$instance->setup_constants();
				self::$instance->includes();
				self::$instance->init();
				self::$instance->hooks();

			}

			return self::$instance;
		}

		/**
		 * Constructor Function
		 *
		 * @since 1.0
		 * @access private
		 */
		private function __construct() {
			self::$instance = $this;
		}

		/**
		 * Reset the instance of the class
		 *
		 * @since 1.0
		 * @access public
		 * @static
		 */
		public static function reset() {
			self::$instance = null;
		}

		/**
		 * Setup plugin constants
		 *
		 * @access private
		 * @since 1.0
		 * @return void
		 */
		private function setup_constants() {

			// Plugin Folder Path
			if ( ! defined( 'AFFWP_AFFILIATE_SHARING_PLUGIN_DIR' ) ) {
				define( 'AFFWP_AFFILIATE_SHARING_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
			}

		}

		/**
		 * Setup the default hooks and actions
		 *
		 * @since 1.0
		 *
		 * @return void
		 */
		private function hooks() {

			// text domain
			add_action( 'after_setup_theme', array( $this, 'load_textdomain' ) );

			// css
			add_action( 'wp_head', array( $this, 'affiliate_dashboard_css' ) );

			// js
			add_action( 'wp_footer', array( $this, 'affiliate_dashboard_js' ), 100 );

			// ajax
			add_action( 'wp_ajax_affiliate_share', array( $this, 'sharing_ajax' ) );
		}

		/**
		 * Init
		 *
		 * @access private
		 * @since 1.1
		 * @return void
		 */
		private function init() {

			if ( is_admin() ) {
				self::$instance->updater();
			}

		}

		/**
		 * Include required files
		 *
		 * @access private
		 * @since 1.0
		 * @return void
		 */
		private function includes() {

			if ( is_admin() ) {

				require_once self::$plugin_dir . 'includes/admin.php';

			}

		}

		/**
		 * Loads the plugin language files
		 *
		 * @access public
		 * @since 1.0
		 * @return void
		 */
		public function load_textdomain() {
			// Set filter for plugin's languages directory
			$lang_dir = dirname( plugin_basename( AFFWP_AFFILIATE_SHARING_PLUGIN_DIR ) ) . '/languages/';
			$lang_dir = apply_filters( 'affwp_affiliate_sharing_languages_directory', $lang_dir );

			// Traditional WordPress plugin locale filter
			$locale        = apply_filters( 'plugin_locale',  get_locale(), 'affwp_affiliate_dashboard_sharing' );
			$mofile        = sprintf( '%1$s-%2$s.mo', 'affwp_affiliate_dashboard_sharing', $locale );

			// Setup paths to current locale file
			$mofile_local  = $lang_dir . $mofile;
			$mofile_global = WP_LANG_DIR . '/affwp-affiliate-dashboard-sharing/' . $mofile;

			if ( file_exists( $mofile_global ) ) {
				load_textdomain( 'affwp_affiliate_dashboard_sharing', $mofile_global );
			} elseif ( file_exists( $mofile_local ) ) {
				load_textdomain( 'affwp_affiliate_dashboard_sharing', $mofile_local );
			} else {
				// Load the default language files
				load_plugin_textdomain( 'affwp_affiliate_dashboard_sharing', false, $lang_dir );
			}
		}

		/**
		 * Load the custom plugin updater
		 *
		 * @access private
		 * @since 1.1
		 * @return void
		 */
		public function updater() {

			if ( class_exists( 'AffWP_AddOn_Updater' ) ) {
				$updater = new AffWP_AddOn_Updater( 807, __FILE__, self::$version );
			}
		}

		/**
		 * Basic upgrade routine
		 *
		 * @since  1.1
		 */
		public static function install() {

		    // get options
		    $options = get_option( 'affwp_settings' );

		    // get old options
		    $old_dashboard_sharing_options = isset( $options['dashboard_sharing'] ) ? $options['dashboard_sharing'] : '';

		    // get old sharing text for twitter
		    $old_sharing_text = isset( $old_dashboard_sharing_options['sharing_text'] ) ? $old_dashboard_sharing_options['sharing_text'] : '';

			if ( $old_sharing_text ) {

		    	// update the new option with the old text
		    	$options['ads_twitter_sharing_text'] = $old_sharing_text;

		    	// delete old option
		    	unset( $options['dashboard_sharing'] );

		    	// update options
		    	update_option( 'affwp_settings', $options );
		    }

		    // and we're done
		}

		/**
		 * Sharing
		 *
		 * @since  1.1
		 */
		public function social_networks() {

			$networks = array(
				'twitter'    => __( 'Twitter', 'affwp-affiliate-dashboard-sharing' ),
				'facebook'   => __( 'Facebook', 'affwp-affiliate-dashboard-sharing' ),
				'googleplus' => __( 'Google+', 'affwp-affiliate-dashboard-sharing' ),
				'linkedin'   => __( 'LinkedIn', 'affwp-affiliate-dashboard-sharing' ),
				'email'      => __( 'Email', 'affwp-affiliate-dashboard-sharing' ),
			);

			return $networks;

		}

		/**
		 * Check that each social network is enabled
		 * @param  string  $network
		 * @return boolean
		 * @since  1.1
		 */
		public function is_enabled( $network = '' ) {

			$networks = affiliate_wp()->settings->get( 'ads_social_networks' );

			// if network is passed as parameter
			if ( $network ) {
				switch ( $network ) {

					case 'twitter':
						return isset( $networks[$network] );
						break;

					case 'facebook':
						return isset( $networks[$network] );
						break;

					case 'googleplus':
						return isset( $networks[$network] );
						break;

					case 'linkedin':
						return isset( $networks[$network] );
						break;

					case 'email':
						return isset( $networks[$network] );
						break;

				}
			} elseif ( $networks ) {
				return true;
			}

			return false;

		}

		/**
		 * Sharing
		 */
		public function sharing( $share_url = '' ) {

			$twitter_default_text = affiliate_wp()->settings->get( 'ads_twitter_sharing_text' ) ? affiliate_wp()->settings->get( 'ads_twitter_sharing_text' ) : get_bloginfo( 'name' );

			ob_start();
		?>

		<h2><?php _e( 'Share this URL', 'affwp-affiliate-dashboard-sharing' ); ?></h2>

			<div class="affwp-sharing">

			<?php
				if ( $this->is_enabled( 'twitter') ) :

					$twitter_count_box 		= 'vertical';
					$twitter_button_size 	= 'medium';

					if ( affiliate_wp()->settings->get( 'ads_campaign_tracking' ) ) {
						$twitter_share_url = esc_url( add_query_arg( 'utm_source', 'twitter', $share_url ) );
					} else {
						$twitter_share_url = $share_url;
					}
				?>
				<div class="share twitter">
					<a href="https://twitter.com/share" data-text="<?php echo $twitter_default_text; ?>" data-lang="en" class="twitter-share-button" data-count="<?php echo $twitter_count_box; ?>" data-size="<?php echo $twitter_button_size; ?>" data-counturl="<?php echo $twitter_share_url; ?>" data-url="<?php echo $twitter_share_url; ?>">
						<?php _e( 'Share', 'affwp-affiliate-dashboard-sharing' ); ?>
					</a>
				</div>
			<?php endif; ?>

			<?php
			if ( $this->is_enabled( 'facebook') ) :

				$data_share             = affiliate_wp()->settings->get( 'ads_facebook_share_button' ) ? 'true' : 'false';
				$facebook_button_layout = 'box_count';

				if ( affiliate_wp()->settings->get( 'ads_campaign_tracking' ) ) {
					$facebook_share_url = esc_url( add_query_arg( 'utm_source', 'facebook', $share_url ) );
				} else {
					$facebook_share_url = $share_url;
				}
			?>
				<div class="share facebook">
					<div class="fb-like" data-href="<?php echo $facebook_share_url; ?>" data-send="true" data-action="like" data-layout="<?php echo $facebook_button_layout; ?>" data-share="<?php echo $data_share; ?>" data-width="" data-show-faces="false"></div>
				</div>

			<?php endif; ?>

			<?php
				if ( $this->is_enabled( 'googleplus') ) :

				$googleplus_button_size = 'tall';
				$google_button_annotation = 'bubble';
				$google_button_recommendations = 'false';

				if ( affiliate_wp()->settings->get( 'ads_campaign_tracking' ) ) {
					$googleplus_share_url = esc_url( add_query_arg( 'utm_source', 'googleplus', $share_url ) );
				} else {
					$googleplus_share_url = $share_url;
				}

			?>

				<div class="share googleplus">
					<div class="g-plusone" data-recommendations="<?php echo $google_button_recommendations; ?>" data-annotation="<?php echo $google_button_annotation;?>" data-callback="plusOned" data-size="<?php echo $googleplus_button_size; ?>" data-href="<?php echo $googleplus_share_url; ?>"></div>
				</div>
			<?php endif; ?>

			<?php
				if ( $this->is_enabled( 'linkedin') ) :

				$linkedin_counter = 'top';

				if ( affiliate_wp()->settings->get( 'ads_campaign_tracking' ) ) {
					$linkedin_share_url = esc_url( add_query_arg( 'utm_source', 'linkedin', $share_url ) );
				} else {
					$linkedin_share_url = $share_url;
				}
			?>

				<div class="share linkedin">
					<script type="IN/Share" data-counter="<?php echo $linkedin_counter; ?>" data-onSuccess="share" data-url="<?php echo $linkedin_share_url; ?>"></script>
				</div>

			<?php endif; ?>

			<?php
				if ( $this->is_enabled( 'email') ) :

					if ( affiliate_wp()->settings->get( 'ads_campaign_tracking' ) ) {
						$email_share_url = add_query_arg( 'utm_source', 'email', $share_url );
					} else {
						$email_share_url = $share_url;
					}

					$email_share_url = rawurlencode( $email_share_url );

					// email subject
					$email_subject = affiliate_wp()->settings->get( 'ads_email_subject' ) ? affiliate_wp()->settings->get( 'ads_email_subject' ) : get_bloginfo( 'name' );
					$email_subject = apply_filters( 'affwp_ads_subject', rawurlencode( $email_subject ) );

					// email body
					$email_body = affiliate_wp()->settings->get( 'ads_email_body' ) ? rawurlencode( affiliate_wp()->settings->get( 'ads_email_body' ) ) : __( 'I thought you might be interested in this:', 'affwp-affiliate-dashboard-sharing' );
					$email_body = apply_filters( 'affwp_ads_body', $email_body . ' ' . $email_share_url, $email_body, $email_share_url );

					$email_share_text = apply_filters( 'affwp_ads_email_share_text', __( 'Share via email', 'affwp-affiliate-dashboard-sharing' ) );

			?>

				<div class="share email">
					<p><a href="mailto:?subject=<?php echo $email_subject; ?>&amp;body=<?php echo $email_body; ?>"><?php echo $email_share_text; ?></a></p>
				</div>

			<?php endif; ?>

			</div>

		<?php
			$html = ob_get_clean();
			return apply_filters( 'affwp_sharing_html', $html );
		}

		/**
		 * Is Affiliate Dashboard
		 * @return boolean true if on dashboard, false otherwise
		 */
		private function is_affiliate_dashboard() {
			global $post;

			if ( has_shortcode( $post->post_content, 'affiliate_area' ) )
				return true;

			return false;
		}

		/**
		 * JS
		 * @return [type] [description]
		 */
		public function affiliate_dashboard_js() {
			
			if ( ! $this->is_enabled() ) {
				return;
			}

			$post = get_post();

			if( ! $post ) {
				return;
			}

			if ( is_page( affiliate_wp()->settings->get( 'affiliates_page' ) ) || has_shortcode( $post->post_content, 'affiliate_area_urls' ) ) :

			?>
			<script>
				var affwpds_ajaxurl = "<?php echo admin_url( 'admin-ajax.php' ); ?>";
				jQuery(document).ready(function ($) {

				  $( '#affwp-generate-ref-url' ).append( '<div id="affwp-affiliate-dashboard-sharing"></div>' );

				   $( '#affwp-generate-ref-url' ).submit( function() {

				      var url    = $('#affwp-referral-url').val();
				      console.log( url );

				      var data   = {
				          action: 'affiliate_share',
				          url : url
				      };

				      $.ajax({
				          type: "POST",
				          data: data,
				          dataType: "json",
				          url: affwpds_ajaxurl,
				          success: function (response) {

				            if ( response.sharing ) {

				               $( '#affwp-affiliate-dashboard-sharing' ).empty().append( response.sharing );

				               <?php if ( $this->is_enabled( 'linkedin' ) ) : ?>

                				// LinkedIn
                				if ( typeof (IN) != 'undefined' ) {
                				    IN.parse();
                				}
                				else {
                				   $.getScript("https://platform.linkedin.com/in.js");
                				}
                				<?php endif; ?>

                				<?php
                				// Twitter
                				if ( $this->is_enabled( 'twitter' ) ) : ?>

								if ( typeof (twttr) != 'undefined' ) {
									twttr.widgets.load();
								}
								else {
									$.getScript('//platform.twitter.com/widgets.js');
								}
								<?php endif; ?>

								<?php
								// Facebook
								if ( $this->is_enabled( 'facebook' ) ) : ?>

								if ( typeof (FB) != 'undefined' ) {
									FB.init({
										status: true,
										cookie: true,
										xfbml: true,
										version: 'v2.6' // https://developers.facebook.com/docs/apps/changelog#versions
									});
								}
								else {
									$.getScript("//connect.facebook.net/en_US/all.js#xfbml=1", function () {
										FB.init({
											status: true,
											cookie: true,
											xfbml: true,
											version: 'v2.6'
										});
									});
								}
								<?php endif; ?>

								 <?php
								  // Google
								 if ( $this->is_enabled( 'googleplus' ) ) : ?>

        				       if ( typeof (gapi) != 'undefined' ) {
        				           $(".g-plusone").each(function () {
        				           		gapi.plusone.render( $(this).get(0), { "size": "tall" } );
        				           });
        				       } else {
        				           $.getScript('https://apis.google.com/js/plusone.js');
        				       }
        				       <?php endif; ?>

				            }

				          }
				      }).fail(function (response) {
				          console.log(response);
				      }).done(function (response) {

				      });

				   });

				});

			</script>
			<?php endif;
		}

		/**
		 * Sharing ajax
		 *
		 * @since  1.0
		 */
		public function sharing_ajax() {

			// get URL
			$url = isset( $_POST['url'] ) ? $_POST['url'] : null;

			$return = array(
				'sharing' => html_entity_decode( $this->sharing( $url ), ENT_COMPAT, 'UTF-8' )
			);

			echo json_encode( $return );

			wp_die();
		}

		/**
		 * Load scripts
		 *
		 * @return void
		 * @since  1.0
		 */
		public function affiliate_dashboard_css() {

			global $post;

			if ( ! $this->is_enabled() ) {
				return;
			}

			if ( is_page( affiliate_wp()->settings->get( 'affiliates_page' ) ) || ( isset( $post ) && has_shortcode( $post->post_content, 'affiliate_area_urls' ) ) ) :

			?>
			<style>
				#affwp-affiliate-dashboard-sharing { margin: 2em 0 4em 0; }
				.affwp-sharing .share { display: inline-block; vertical-align: top; padding: 0 0.5em 0.5em 0; }
				.affwp-sharing .share.email { display: block; }
				.affwp-sharing .share iframe { max-width: none; }
			</style>
		<?php endif;
		}

	}

	register_activation_hook( __FILE__, array( 'AffiliateWP_Affiliate_Dashboard_Sharing', 'install' ) );

	/**
	 * The main function responsible for returning the one true AffiliateWP_Affiliate_Dashboard_Sharing
	 * Instance to functions everywhere.
	 *
	 * Use this function like you would a global variable, except without needing
	 * to declare the global.
	 *
	 * Example: <?php $affwp_affiliate_dashboard_sharing = affwp_affiliate_dashboard_sharing(); ?>
	 *
	 * @since 1.0
	 * @return object The one true AffiliateWP_Affiliate_Dashboard_Sharing Instance
	 */
	function affwp_affiliate_dashboard_sharing() {

	    if ( ! class_exists( 'Affiliate_WP' ) ) {

	        if ( ! class_exists( 'AffiliateWP_Activation' ) ) {
	            require_once 'includes/class-activation.php';
	        }

	        // AffiliateWP activation
			if ( ! class_exists( 'Affiliate_WP' ) ) {
				$activation = new AffiliateWP_Activation( plugin_dir_path( __FILE__ ), basename( __FILE__ ) );
				$activation = $activation->run();
			}
	    } else {
	        return AffiliateWP_Affiliate_Dashboard_Sharing::get_instance();
	    }

	}
	add_action( 'plugins_loaded', 'affwp_affiliate_dashboard_sharing', apply_filters( 'affwp_affiliate_dashboard_sharing_action_priority', 10 ) );

}
