<?php

class WCML_Dependencies {

	const MIN_WPML = '3.4';
	const MIN_WPML_TM = '1.9';
	const MIN_WPML_ST = '2.0';
	const MIN_WOOCOMMERCE = '2.1';

	private $missing = array();
	private $err_message = '';
	private $allok = true;

	/**
	 * @var WCML_Tracking_Link
	 */
	private $tracking_link;

	function __construct() {

		if ( is_admin() ) {
			add_action( 'wp_ajax_wcml_fix_strings_language', array(
				$this,
				'fix_strings_language'
			) ); // TODO: remove after WPML release  with support strings in different languages

			add_action( 'init', array( $this, 'check_wpml_config' ), 100 );
		}

		$this->tracking_link = new WCML_Tracking_Link();
	}

	function check() {
		global $woocommerce_wpml, $sitepress, $woocommerce;

		if ( ! defined( 'ICL_SITEPRESS_VERSION' ) || ICL_PLUGIN_INACTIVE || is_null( $sitepress ) || ! class_exists( 'SitePress' ) ) {
			$this->missing['WPML'] = $this->tracking_link->generate( 'https://wpml.org/' );
			$this->allok           = false;
		} elseif ( version_compare( ICL_SITEPRESS_VERSION, self::MIN_WPML, '<' ) ) {
			add_action( 'admin_notices', array( $this, '_old_wpml_warning' ) );
			$this->allok = false;
		} elseif ( ! $sitepress->setup() ) {
			if ( ! ( isset( $_GET['page'] ) && ICL_PLUGIN_FOLDER . '/menu/languages.php' === $_GET['page'] ) ) {
				add_action( 'admin_notices', array( $this, '_wpml_not_installed_warning' ) );
			}
			$this->allok = false;
		}

		if ( ! class_exists( 'WooCommerce' ) || ! function_exists( 'WC' ) ) {
			$this->missing['WooCommerce'] = 'http://www.woothemes.com/woocommerce/';
			$this->allok                  = false;
		} elseif (
			defined( 'WC_VERSION' ) && version_compare( WC_VERSION, self::MIN_WOOCOMMERCE, '<' ) ||
			isset( $woocommerce->version ) && version_compare( $woocommerce->version, self::MIN_WOOCOMMERCE, '<' )
		) {
			add_action( 'admin_notices', array( $this, '_old_wc_warning' ) );
			$this->allok = false;
		}

		if ( ! defined( 'WPML_TM_VERSION' ) ) {
			$this->missing['WPML Translation Management'] = $this->tracking_link->generate( 'https://wpml.org/' );
			$this->allok                                  = false;
		} elseif ( version_compare( WPML_TM_VERSION, self::MIN_WPML_TM, '<' ) ) {
			add_action( 'admin_notices', array( $this, '_old_wpml_tm_warning' ) );
			$this->allok = false;
		}

		if ( ! defined( 'WPML_ST_VERSION' ) ) {
			$this->missing['WPML String Translation'] = $this->tracking_link->generate( 'https://wpml.org/' );
			$this->allok                              = false;
		} elseif ( version_compare( WPML_ST_VERSION, self::MIN_WPML_ST, '<' ) ) {
			add_action( 'admin_notices', array( $this, '_old_wpml_st_warning' ) );
			$this->allok = false;
		}

		if ( $this->missing ) {
			add_action( 'admin_notices', array( $this, '_missing_plugins_warning' ) );
		}

		if ( $this->allok ) {
			$this->check_for_incompatible_permalinks();
			$this->check_for_transaltable_default_taxonomies();
		}

		if ( isset( $sitepress ) ) {
			$this->allok = $this->allok & $sitepress->setup();
		} else {
			$this->load_twig_support();
		}

		return $this->allok;
	}

	/**
	 * Adds admin notice.
	 */
	public function _old_wpml_warning() {
		?>
		<div class="message error">
			<p><?php printf( __( 'WooCommerce Multilingual is enabled but not effective. It is not compatible with  <a href="%s">WPML</a> versions prior %s.',
					'woocommerce-multilingual' ), $this->tracking_link->generate( 'https://wpml.org/' ), self::MIN_WPML ); ?></p>
		</div>
	<?php }

	function _wpml_not_installed_warning() {
		?>
		<div class="message error">
			<p><?php printf( __( 'WooCommerce Multilingual is enabled but not effective. Please finish the installation of WPML first.', 'woocommerce-multilingual' ) ); ?></p>
		</div>
		<?php
	}

	function _old_wc_warning() {
		?>
		<div class="message error">
			<p><?php printf( __( 'WooCommerce Multilingual is enabled but not effective. It is not compatible with  <a href="%s">Woocommerce</a> versions prior %s.',
					'woocommerce-multilingual' ), 'http://www.woothemes.com/woocommerce/', self::MIN_WOOCOMMERCE ); ?></p>
		</div>
	<?php }

	public function _old_wpml_tm_warning() {
		?>
		<div class="message error">
			<p><?php printf( __( 'WooCommerce Multilingual is enabled but not effective. It is not compatible with  <a href="%s">WPML Translation Management</a> versions prior %s.',
					'woocommerce-multilingual' ), $this->tracking_link->generate( 'https://wpml.org/' ), self::MIN_WPML_TM ); ?></p>
		</div>
	<?php }

	public function _old_wpml_st_warning() {
		?>
		<div class="message error">
			<p><?php printf( __( 'WooCommerce Multilingual is enabled but not effective. It is not compatible with  <a href="%s">WPML String Translation</a> versions prior %s.',
					'woocommerce-multilingual' ), $this->tracking_link->generate( 'https://wpml.org/' ), self::MIN_WPML_ST ); ?></p>
		</div>
	<?php }

	/**
	 * Adds default taxonomies notice.
	 */
	public function check_for_transaltable_default_taxonomies() {

		$default_taxonomies = array( 'product_cat', 'product_tag', 'product_shipping_class' );
		$show_error         = false;

		foreach ( $default_taxonomies as $taxonomy ) {
			if ( ! is_taxonomy_translated( $taxonomy ) ) {
				$show_error = true;
				break;
			}
		}

		if ( $show_error ) {
			$support_link = '<a href="https://wpml.org/forums/forum/english-support/">' . __( 'WPML support', 'woocommerce-multilingual' ) . '</a>';

			/* translators: Part 1/6 of a message telling users that some taxonomies, required for WCML to work, are not set as translatable when they should */
			$sentences[] = _x( "Some taxonomies in your site are forced to be untranslatable. This is causing a problem when you're trying to run a multilingual WooCommerce site.", 'Default taxonomies must be translatable: 1/6', 'woocommerce-multilingual' );
			/* translators: Part 2/6 of a message telling users that some taxonomies, required for WCML to work, are not set as translatable when they should */
			$sentences[] = _x( 'A plugin or the theme are probably doing this.', 'Default taxonomies must be translatable: 2/6', 'woocommerce-multilingual' );
			/* translators: Part 3/6 of a message telling users that some taxonomies, required for WCML to work, are not set as translatable when they should */
			$sentences[] = _x( 'What you can do:', 'Default taxonomies must be translatable: 3/6', 'woocommerce-multilingual' );
			/* translators: Part 4/6 of a message telling users that some taxonomies, required for WCML to work, are not set as translatable when they should */
			$sentences[] = _x( '1. Temporarily disable plugins and see if this message disappears.', 'Default taxonomies must be translatable: 4/6', 'woocommerce-multilingual' );
			/* translators: Part 5/6 of a message telling users that some taxonomies, required for WCML to work, are not set as translatable when they should */
			$sentences[] = _x( '2. Temporarily switch the theme and see if this message disappears.', 'Default taxonomies must be translatable: 5/6', 'woocommerce-multilingual' );
			/* translators: Part 6/6 of a message telling users that some taxonomies, required for WCML to work, are not set as translatable when they should */
			$sentences[] = sprintf( _x( "It's best to contact %s, tell that you're getting this message and offer to send a Duplicator copy of the site. We will work with the theme/plugin author and fix the problem for good. In the meanwhile, we'll give you a temporary solution, so you're not stuck.", 'Default taxonomies must be translatable: 6/6', 'woocommerce-multilingual' ), $support_link );

			$this->err_message = '<div class="message error"><p>' . implode( '</p><p>', $sentences ) . '</p></div>';
			add_action( 'admin_notices', array( $this, 'plugin_notice_message' ) );
		}
	}

	/**
	 * Adds admin notice.
	 */
	public function _missing_plugins_warning() {

		$missing = '';
		$counter = 0;
		foreach ( $this->missing as $title => $url ) {
			$counter ++;
			if ( $counter == sizeof( $this->missing ) ) {
				$sep = '';
			} elseif ( $counter == sizeof( $this->missing ) - 1 ) {
				$sep = ' ' . __( 'and', 'woocommerce-multilingual' ) . ' ';
			} else {
				$sep = ', ';
			}
			$missing .= '<a href="' . $url . '">' . $title . '</a>' . $sep;
		} ?>

		<div class="message error">
			<p><?php printf( __( 'WooCommerce Multilingual is enabled but not effective. It requires %s in order to work.', 'woocommerce-multilingual' ), $missing ); ?></p>
		</div>
		<?php
	}

	/**
	 * For all the urls to work we need either:
	 * 1) the shop page slug must be the same in all languages
	 * 2) or the shop prefix disabled in woocommerce settings
	 * one of these must be true for product urls to work
	 * if none of these are true, display a warning message
	 */
	private function check_for_incompatible_permalinks() {
		global $sitepress, $sitepress_settings, $pagenow;

		// WooCommerce 2.x specific checks
		$permalinks = get_option( 'woocommerce_permalinks', array( 'product_base' => '' ) );
		if ( empty( $permalinks['product_base'] ) ) {
			return;
		}

		$message = __( 'Because this site uses the default permalink structure, you cannot use slug translation for product permalinks.', 'woocommerce-multilingual' );
		$message .= '<br /><br />';
		$message .= __( 'Please choose a different permalink structure or disable slug translation.', 'woocommerce-multilingual' );
		$message .= '<br /><br />';
		$message .= '<a href="' . admin_url( 'options-permalink.php' ) . '">' . __( 'Permalink settings', 'woocommerce-multilingual' ) . '</a>';
		$message .= ' | ';
		$message .= '<a href="' . admin_url( 'admin.php?page=' . WPML_TM_FOLDER . '/menu/main.php&sm=mcsetup#icl_custom_posts_sync_options' ) . '">' . __( 'Configure products slug translation', 'woocommerce-multilingual' ) . '</a>';


		// Check if translated shop pages have the same slug (only 1.x)
		$allsame = true;
		if ( version_compare( WOOCOMMERCE_VERSION, "2.0.0" ) >= 0 ) {
		} else {
			$shop_page_id = get_option( 'woocommerce_shop_page_id', false );
			if ( ! empty( $shop_page_id ) ) {
				$slug      = @get_post( $shop_page_id )->post_name;
				$languages = $sitepress->get_active_languages();
				if ( sizeof( $languages ) < 2 ) {
					return;
				}
				foreach ( $languages as $language ) {
					if ( $language['code'] != $sitepress->get_default_language() ) {
						$translated_shop_page_id = apply_filters( 'translate_object_id', $shop_page_id, 'page', false, $language['code'] );
						if ( ! empty( $translated_shop_page_id ) ) {
							$translated_slug = get_post( $translated_shop_page_id )->post_name;
							if ( ! empty( $translated_slug ) && $translated_slug != $slug ) {
								$allsame = false;
								break;
							}
						}
					}
				}
			}
		}

		// Check if slug translation is enabled
		$compatible          = true;
		$permalink_structure = get_option( 'permalink_structure' );
		if ( empty( $permalink_structure )
		     && ! empty( $sitepress_settings['posts_slug_translation']['on'] )
		     && ! empty( $sitepress_settings['posts_slug_translation']['types'] )
		     && $sitepress_settings['posts_slug_translation']['types']['product'] ) {
			$compatible = false;
		}

		// display messages
		if ( ! $allsame ) {
			$this->err_message = '<div class="message error"><p>' . printf( __( 'If you want different slugs for shop pages (%s/%s), you need to disable the shop prefix for products in <a href="%s">WooCommerce Settings</a>', 'woocommerce-multilingual' ),
					$slug, $translated_slug, admin_url( "admin.php?page=woocommerce_settings&tab=pages" ) ) . '</p></div>';
			add_action( 'admin_notices', array( $this, 'plugin_notice_message' ) );
		}

		if ( ! $compatible && ( $pagenow == 'options-permalink.php' || ( isset( $_GET['page'] ) && $_GET['page'] == 'wpml-wcml' ) ) ) {
			$this->err_message = '<div class="message error"><p>' . $message . '    </p></div>';
			add_action( 'admin_notices', array( $this, 'plugin_notice_message' ) );
		}
	}

	public function plugin_notice_message() {
		echo $this->err_message;
	}

	public function fix_strings_language() {
		$nonce = filter_input( INPUT_POST, 'wcml_nonce', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
		if ( ! $nonce || ! wp_verify_nonce( $nonce, 'wcml_fix_strings_language' ) ) {
			die( 'Invalid nonce' );
		}

		$ret = array();

		$ret['_wpnonce'] = wp_create_nonce( 'icl_sw_form' );

		$ret['success_1'] = '&nbsp;' . sprintf( __( 'Finished! You can visit the %sstrings translation%s screen to translate the strings now.', 'woocommerce-multilingual' ), '<a href="' . admin_url( 'admin.php?page=' . WPML_ST_FOLDER . '/menu/string-translation.php' ) . '">', '</a>' );


		echo json_encode( $ret );

		exit;

	}

	public function check_wpml_config() {
		global $sitepress_settings, $sitepress, $woocommerce_wpml;

		if ( empty( $sitepress_settings ) || ! $this->check() ) {
			return;
		}

		$file = realpath( WCML_PLUGIN_PATH . '/wpml-config.xml' );
		if ( ! file_exists( $file ) ) {
			$this->xml_config_errors[] = __( 'wpml-config.xml file missing from WooCommerce Multilingual folder.', 'woocommerce-multilingual' );
		} else {
			$config = icl_xml2array( file_get_contents( $file ) );

			if ( isset( $config['wpml-config'] ) ) {

				//custom-fields
				if ( isset( $config['wpml-config']['custom-fields'] ) ) {
					if ( isset( $config['wpml-config']['custom-fields']['custom-field']['value'] ) ) { //single
						$cfs[] = $config['wpml-config']['custom-fields']['custom-field'];
					} else {
						foreach ( $config['wpml-config']['custom-fields']['custom-field'] as $cf ) {
							$cfs[] = $cf;
						}
					}

					if ( $cfs ) {
						foreach ( $cfs as $cf ) {
							if ( ! isset( $sitepress_settings['translation-management']['custom_fields_translation'][ $cf['value'] ] ) ) {
								continue;
							}

							$effective_config_value = $sitepress_settings['translation-management']['custom_fields_translation'][ $cf['value'] ];
							$correct_config_value   = $cf['attr']['action'] == 'copy' ? 1 : ( $cf['attr']['action'] == 'translate' ? 2 : 0 );

							if ( $effective_config_value != $correct_config_value ) {
								$this->xml_config_errors[] = sprintf( __( 'Custom field %s configuration from wpml-config.xml file was altered!', 'woocommerce-multilingual' ), '<i>' . $cf['value'] . '</i>' );
							}
						}
					}

				}

				//custom-types
				if ( isset( $config['wpml-config']['custom-types'] ) ) {
					if ( isset( $config['wpml-config']['custom-types']['custom-type']['value'] ) ) { //single
						$cts[] = $config['wpml-config']['custom-types']['custom-type'];
					} else {
						foreach ( $config['wpml-config']['custom-types']['custom-type'] as $cf ) {
							$cts[] = $cf;
						}
					}

					if ( $cts ) {
						foreach ( $cts as $ct ) {
							if ( ! isset( $sitepress_settings['custom_posts_sync_option'][ $ct['value'] ] ) ) {
								continue;
							}
							$effective_config_value = $sitepress_settings['custom_posts_sync_option'][ $ct['value'] ];
							$correct_config_value   = $ct['attr']['translate'];


							if ( 'product' === $ct['value'] && $woocommerce_wpml->products->is_product_display_as_translated_post_type() ) {
								$correct_config_value = WPML_CONTENT_TYPE_DISPLAY_AS_IF_TRANSLATED;
							}


							if ( $effective_config_value != $correct_config_value ) {
								$this->xml_config_errors[] = sprintf( __( 'Custom type %s configuration from wpml-config.xml file was altered!', 'woocommerce-multilingual' ), '<i>' . $ct['value'] . '</i>' );
							}
						}
					}
				}

				//taxonomies
				if ( isset( $config['wpml-config']['taxonomies'] ) ) {
					if ( isset( $config['wpml-config']['taxonomies']['taxonomy']['value'] ) ) { //single
						$txs[] = $config['wpml-config']['taxonomies']['taxonomy'];
					} else {
						foreach ( $config['wpml-config']['taxonomies']['taxonomy'] as $cf ) {
							$txs[] = $cf;
						}
					}

					if ( $txs ) {
						foreach ( $txs as $tx ) {
							if ( ! isset( $sitepress_settings['taxonomies_sync_option'][ $tx['value'] ] ) ) {
								continue;
							}
							$effective_config_value = $sitepress_settings['taxonomies_sync_option'][ $tx['value'] ];
							$correct_config_value   = $tx['attr']['translate'];

							if ( method_exists( $sitepress, 'is_display_as_translated_taxonomy' ) && $sitepress->is_display_as_translated_taxonomy( $tx['value'] ) ) {
								$correct_config_value = WPML_CONTENT_TYPE_DISPLAY_AS_IF_TRANSLATED;
							}

							if ( $effective_config_value != $correct_config_value ) {
								$this->xml_config_errors[] = sprintf( __( 'Custom taxonomy %s configuration from wpml-config.xml file was altered!', 'woocommerce-multilingual' ), '<i>' . $tx['value'] . '</i>' );
							}
						}
					}

				}
			}
		}

	}

	public function required_plugin_install_link( $repository = 'wpml' ) {

		if ( class_exists( 'WP_Installer_API' ) ) {
			$url = WP_Installer_API::get_product_installer_link( $repository );
		} else {
			$url = $this->tracking_link->generate( 'https://wpml.org/' );
		}

		return $url;
	}

	/**
	 * The support for the Twig templates comes from WPML by default
	 * When WPML is not active, WCML will load it
	 */
	private function load_twig_support() {

		if ( ! class_exists( 'Twig_Autoloader' ) ) {
			Twig_Autoloader::register();
		}

	}

}
  