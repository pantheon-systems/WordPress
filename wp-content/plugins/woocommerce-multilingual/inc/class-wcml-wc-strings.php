<?php

class WCML_WC_Strings {

	private $translations_from_mo_file = array();
	private $mo_files = array();
	private $current_language;

	/** @var woocommerce_wpml */
	private $woocommerce_wpml;
	/** @var  Sitepress */
	private $sitepress;

	public $settings = array();

	/**
	 * WCML_WC_Strings constructor.
	 *
	 * @param woocommerce_wpml $woocommerce_wpml
	 * @param SitePress $sitepress
	 */
	public function __construct( woocommerce_wpml $woocommerce_wpml, SitePress $sitepress ) {
		$this->woocommerce_wpml = $woocommerce_wpml;
		$this->sitepress        = $sitepress;
	}

	function add_hooks() {

		add_action( 'init', array( $this, 'add_on_init_hooks' ) );
		add_action( 'registered_taxonomy', array( $this, 'translate_attributes_label_in_wp_taxonomies' ), 100, 3 );
	}

	function add_on_init_hooks() {
		global $pagenow;

		$this->current_language = $this->sitepress->get_current_language();
		if ( $this->current_language == 'all' ) {
			$this->current_language = $this->sitepress->get_default_language();
		}

		//translate attribute label
		add_filter( 'woocommerce_attribute_label', array( $this, 'translated_attribute_label' ), 10, 3 );
		add_filter( 'woocommerce_checkout_product_title', array( $this, 'translated_checkout_product_title' ), 10, 2 );
		add_filter( 'woocommerce_cart_item_name', array( $this, 'translated_cart_item_name' ), -1, 2 );

		if ( is_admin() ) {

			if ( 'edit.php' !== $pagenow && ! wpml_is_ajax() ) {
				add_filter( 'woocommerce_attribute_taxonomies', array(
					$this,
					'translate_attribute_taxonomies_labels'
				) );
			}
			if ( 'options-permalink.php' === $pagenow ) {
				add_filter( 'gettext_with_context', array( $this, 'category_base_in_strings_language' ), 99, 3 );
				add_action( 'admin_footer', array( $this, 'show_custom_url_base_translation_links' ) );
				add_action( 'admin_footer', array( $this, 'show_custom_url_base_language_requirement' ) );
			}
		}

		add_action( 'woocommerce_product_options_attributes', array(
			$this,
			'notice_after_woocommerce_product_options_attributes'
		) );

		add_filter( 'woocommerce_get_breadcrumb', array( $this, 'filter_woocommerce_breadcrumbs' ), 10, 2 );
	}

	function translated_attribute_label( $label, $name, $product_obj = false ) {
		global $product, $sitepress_settings;

		$product_id = false;
		$lang       = $this->sitepress->get_current_language();

		if ( isset( $_GET['post'] ) && get_post_type( $_GET['post'] ) == 'shop_order' ) {
			$lang = $this->sitepress->get_user_admin_language( get_current_user_id(), true );
		}

		if ( $product && is_object( $product ) ) {
			$product_id = WooCommerce_Functions_Wrapper::get_product_id( $product );
		} elseif ( is_numeric( $product_obj ) ) {
			$product_id = $product_obj;
		} elseif ( $product_obj ) {
			$product_id = WooCommerce_Functions_Wrapper::get_product_id( $product_obj );
		}

		$name = $this->woocommerce_wpml->attributes->filter_attribute_name( $name, $product_id, true );

		if ( $product_id ) {

			$custom_attr_translation = $this->woocommerce_wpml->attributes->get_attr_label_translations( $product_id, $lang );

			if ( $custom_attr_translation ) {
				if ( isset( $custom_attr_translation[ $name ] ) ) {
					return $custom_attr_translation[ $name ];
				}
			}

		}

		$trnsl_label = apply_filters( 'wpml_translate_single_string', $label, 'WordPress', 'taxonomy singular name: ' . $label, $lang );

		if ( $label != $trnsl_label ) {
			return $trnsl_label;
		}

		if ( is_admin() && ! wpml_is_ajax() ) {

			$string_language = $this->get_string_language( 'taxonomy singular name: ' . $label, 'WordPress' );

			if ( $this->sitepress->get_user_admin_language( get_current_user_id(), true ) != $string_language ) {
				$string_id = icl_get_string_id( 'taxonomy singular name: ' . $label, 'WordPress' );
				$strings   = icl_get_string_translations_by_id( $string_id );
				if ( $strings ) {
					return $strings[ $this->sitepress->get_user_admin_language( get_current_user_id(), true ) ]['value'];
				}
			} else {
				return $label;
			}

		}

		// backward compatibility for WCML < 3.6.1
		$trnsl_labels = get_option( 'wcml_custom_attr_translations' );

		if ( isset( $trnsl_labels[ $lang ][ $name ] ) && ! empty( $trnsl_labels[ $lang ][ $name ] ) ) {
			return $trnsl_labels[ $lang ][ $name ];
		}

		return $label;
	}

	/**
	 * @param string $title
	 * @param array $values
	 *
	 * @return string
	 */
	function translated_cart_item_name( $title, array $values ) {

		if ( $values ) {

			$product_id = $values['variation_id'] ? $values['variation_id'] : $values['product_id'];

			$translated_product_id = apply_filters( 'translate_object_id', $product_id, 'product', true );
			$translated_title      = get_the_title( $translated_product_id );

			if ( strstr( $title, '</a>' ) ) {
				$title = sprintf( '<a href="%s">%s</a>', $values['data']->get_permalink(), $translated_title );
			} else {
				$title = $translated_title . '&nbsp;';
			}
		}

		return $title;
	}

	function translated_checkout_product_title( $title, $product ) {

		if ( $product ) {
			$tr_product_id = apply_filters( 'translate_object_id', WooCommerce_Functions_Wrapper::get_product_id( $product ), 'product', true, $this->current_language );
			$title         = get_the_title( $tr_product_id );
		}

		return $title;
	}

	// Catch the default slugs for translation
	function translate_default_slug( $translation, $text, $context, $domain ) {

		if ( $context == 'slug' || $context == 'default-slug' ) {
			$wc_slug = $this->woocommerce_wpml->url_translation->get_woocommerce_product_base();
			if ( is_admin() ) {
				$admin_language = $this->sitepress->get_admin_language();
			}
			$current_language = $this->sitepress->get_current_language();

			$strings_language = $this->get_domain_language( 'woocommerce' );

			if ( $text == $wc_slug && $domain == 'woocommerce' && $strings_language ) {
				$this->sitepress->switch_lang( $strings_language );
				$translation = _x( $text, 'URL slug', $domain );
				$this->sitepress->switch_lang( $current_language );
				if ( is_admin() ) {
					$this->sitepress->set_admin_language( $admin_language );
				}
			} else {
				$translation = $text;
			}

			if ( ! is_admin() ) {
				$this->sitepress->switch_lang( $current_language );
			}
		}

		return $translation;

	}


	function show_custom_url_base_language_requirement() {
		$category_base = ( $c = get_option( 'category_base' ) ) ? $c : 'category';
		?>
        <script>
            if (jQuery('#woocommerce_permalink_structure').length) {
                jQuery('#woocommerce_permalink_structure').parent().append(jQuery('#wpml_wcml_custom_base_req').html());
            }
            if (jQuery('input[name="woocommerce_product_category_slug"]').length && jQuery('input[name="woocommerce_product_category_slug"]').val() == '<?php echo $category_base ?>') {
                jQuery('input[name="woocommerce_product_category_slug"]').parent().append('<br><i class="icon-warning-sign"><?php
					_e( 'You are using the same value as for the regular category base. This is known to create conflicts resulting in urls not working properly.', 'woocommerce-multilingual' ) ?></i>');
            }
        </script>
		<?php

	}

	function show_custom_url_base_translation_links() {

		$permalink_options = get_option( 'woocommerce_permalinks' );

		$lang_selector = new WPML_Simple_Language_Selector( $this->sitepress );

		$bases = array(
			'tag_base'       => 'product_tag',
			'category_base'  => 'product_cat',
			'attribute_base' => 'attribute',
			'product_base'   => 'product'
		);

		foreach ( $bases as $key => $base ) {

			switch ( $base ) {
				case 'product_tag':
					$input_name = 'woocommerce_product_tag_slug';
					$value      = ! empty( $permalink_options['tag_base'] ) ? $permalink_options['tag_base'] : $this->woocommerce_wpml->url_translation->default_product_tag_base;
					break;
				case 'product_cat':
					$input_name = 'woocommerce_product_category_slug';
					$value      = ! empty( $permalink_options['category_base'] ) ? $permalink_options['category_base'] : $this->woocommerce_wpml->url_translation->default_product_category_base;
					break;
				case 'attribute':
					$input_name = 'woocommerce_product_attribute_slug';
					$value      = ! empty( $permalink_options['attribute_base'] ) ? $permalink_options['attribute_base'] : '';
					break;
				case 'product':
					$input_name = 'product_permalink_structure';
					if ( empty( $permalink_options['product_base'] ) ) {
						$value = _x( 'product', 'default-slug', 'woocommerce' );
					} else {
						$value = trim( $permalink_options['product_base'], '/' );
					}

					break;
			}

			$language = $this->get_string_language( trim( $value, '/' ), $this->woocommerce_wpml->url_translation->url_strings_context(), $this->woocommerce_wpml->url_translation->url_string_name( $base ) );

			if ( is_null( $language ) ) {
				$language = $this->sitepress->get_default_language();
			}

			echo $lang_selector->render( array(
				'id'                 => $key . '_language_selector',
				'name'               => $key . '_language',
				'selected'           => $language,
				'show_please_select' => false
			) ); ?>

            <script>
                var input = jQuery('input[name="<?php echo $input_name ?>"]');

                if (input.length) {

                    if ('<?php echo $input_name ?>' == 'product_permalink_structure' && jQuery('input[name="product_permalink"]:checked').val() == '') {
                        input = jQuery('input[name="product_permalink"]:checked').closest('.form-table').find('code').eq(0);
                    }

                    input.parent().append('<div class="translation_controls"></div>');

                    if ('<?php echo $input_name ?>' == 'woocommerce_product_attribute_slug' && input.val() == '') {

                        input.parent().find('.translation_controls').append('&nbsp;');

                    } else {
                        input.parent().find('.translation_controls').append('<a href="<?php
							echo admin_url( 'admin.php?page=wpml-wcml&tab=slugs' )
							?>"><?php _e( 'translations', 'woocommerce-multilingual' ) ?></a>');
                    }

                    jQuery('#<?php echo $key ?>_language_selector').prependTo(input.parent().find('.translation_controls'));
                }
            </script>
		<?php }

	}

	function category_base_in_strings_language( $text, $original_value, $context ) {
		if ( $context == 'slug' && ( $original_value == 'product-category' || $original_value == 'product-tag' ) ) {
			$text = $original_value;
		}

		return $text;
	}

	function product_permalink_slug() {
		$permalinks = get_option( 'woocommerce_permalinks' );
		$slug       = empty( $permalinks['product_base'] ) ? 'product' : trim( $permalinks['product_base'], '/' );

		return $slug;
	}

	function get_domain_language( $domain ) {

		$lang_of_domain = new WPML_Language_Of_Domain( $this->sitepress );
		$domain_lang    = $lang_of_domain->get_language( $domain );

		if ( $domain_lang ) {
			$source_lang = $domain_lang;
		} else {
			$source_lang = 'en';
		}

		return $source_lang;
	}

	function get_string_language( $value, $context, $name = false ) {
		global $wpdb;

		if ( $name !== false ) {

			$string_language = apply_filters( 'wpml_get_string_language', null, $context, $name );

		} else {

			$string_id = icl_get_string_id( $value, $context, $name );

			if ( ! $string_id ) {
				return 'en';
			}

			$string_object   = new WPML_ST_String( $string_id, $wpdb );
			$string_language = $string_object->get_language();

		}

		if ( ! $string_language ) {
			return 'en';
		}

		return $string_language;

	}

	function set_string_language( $value, $context, $name, $language ) {
		global $wpdb;

		$string_id = icl_get_string_id( $value, $context, $name );

		$string_object   = new WPML_ST_String( $string_id, $wpdb );
		$string_language = $string_object->set_language( $language );

		return $string_language;
	}


	/*
	 * Filter breadcrumbs
	 *
	 */
	function filter_woocommerce_breadcrumbs( $breadcrumbs, $object ) {

		$current_language = $this->sitepress->get_current_language();
		$default_language = $this->sitepress->get_default_language();

		$woocommerce_shop_page = wc_get_page_id( 'shop' );

		if ( isset( $woocommerce_shop_page ) ) {

			$is_shop_page_active = get_post_status( $woocommerce_shop_page );

			if ( ( $current_language != $default_language || $default_language != 'en' ) && $is_shop_page_active === 'publish' ) {

				$shop_page = get_post( $woocommerce_shop_page );

				// If permalinks contain the shop page in the URI prepend the breadcrumb with shop
				// Similar to WC_Breadcrumb::prepend_shop_page
				$trnsl_base = $this->woocommerce_wpml->url_translation->get_base_translation( 'product', $current_language );
				if ( $trnsl_base['translated_base'] === '' ) {
					$trnsl_base['translated_base'] = $trnsl_base['original_value'];
				}

				if ( is_woocommerce() && $shop_page->ID && strstr( $trnsl_base['translated_base'], urldecode( $shop_page->post_name ) ) && get_option( 'page_on_front' ) != $shop_page->ID ) {
					$breadcrumbs_buff = array();
					$i                = 0;

					foreach ( $breadcrumbs as $key => $breadcrumb ) {

						//Prepend the shop page to shop breadcrumbs
						if ( $key === 0 ) {

							if ( $breadcrumbs[1][1] != get_post_type_archive_link( 'product' ) ) {

								if ( get_home_url() === $breadcrumbs[0][1] ) {
									$breadcrumbs_buff[ $i ] = $breadcrumb;
									$i ++;
								}

								$breadcrumbs_buff[ $i ] = array(
									$shop_page->post_title,
									get_post_type_archive_link( 'product' )
								);
								$i ++;
							}
						}

						if ( ! in_array( $breadcrumb, $breadcrumbs_buff ) ) {
							$breadcrumbs_buff[ $i ] = $breadcrumb;
						}
						$i ++;
					}

					$breadcrumbs = $breadcrumbs_buff;

					$breadcrumbs = array_values( $breadcrumbs );
				}

			}
		}

		return $breadcrumbs;
	}

	/*
	 * Add notice message to users
	 */
	function notice_after_woocommerce_product_options_attributes() {

		if ( isset( $_GET['post'] ) && $this->sitepress->get_default_language() != $this->sitepress->get_current_language() ) {
			$original_product_id = apply_filters( 'translate_object_id', $_GET['post'], 'product', true, $this->sitepress->get_default_language() );

			printf( '<p>' . __( 'In order to edit custom attributes you need to use the <a href="%s">custom product translation editor</a>', 'woocommerce-multilingual' ) . '</p>', admin_url( 'admin.php?page=wpml-wcml&tab=products&prid=' . $original_product_id ) );
		}
	}

	function translate_attribute_taxonomies_labels( $attribute_taxonomies ) {

		foreach ( $attribute_taxonomies as $key => $attribute_taxonomy ) {
			$string_language = $this->get_string_language( $attribute_taxonomy->attribute_label, 'WordPress', 'taxonomy singular name: ' . $attribute_taxonomy->attribute_label );

			if ( $this->sitepress->get_current_language() == $string_language ) {
				continue;
			}

			$string_id = icl_get_string_id( $attribute_taxonomy->attribute_label, 'WordPress', 'taxonomy singular name: ' . $attribute_taxonomy->attribute_label );
			$strings   = icl_get_string_translations_by_id( $string_id );

			if ( $strings && isset( $strings[ $this->sitepress->get_current_language() ] ) ) {
				$attribute_taxonomies[ $key ]->attribute_label = $strings[ $this->sitepress->get_current_language() ]['value'];
			}
		}

		return $attribute_taxonomies;
	}

	function get_translation_from_woocommerce_mo_file( $string, $language, $return_original = true ) {

		$original_string = $string;

		if ( ! isset( $this->translations_from_mo_file[ $original_string ][ $language ] ) ) {

			if ( ! isset( $this->translations_from_mo_file[ $original_string ] ) ) {
				$this->translations_from_mo_file[ $original_string ] = array();
			}

			if ( ! isset( $this->mo_files[ $language ] ) ) {
				$mo      = new MO();
				$mo_file = WP_LANG_DIR . '/plugins/woocommerce-' . $this->sitepress->get_locale( $language ) . '.mo';
				if ( ! file_exists( $mo_file ) ) {
					return $return_original ? $string : null;
				}

				$mo->import_from_file( $mo_file );
				$this->mo_files[ $language ] = &$mo->entries;
			}

			if ( in_array( $string, array( 'product', 'product-category', 'product-tag' ) ) ) {
				$string = 'slug' . chr( 4 ) . $string;
			}

			if ( isset( $this->mo_files[ $language ][ $string ] ) ) {
				$this->translations_from_mo_file[ $original_string ][ $language ] = $this->mo_files[ $language ][ $string ]->translations[0];
			} else {
				$this->translations_from_mo_file[ $original_string ][ $language ] = $return_original ? $original_string : null;
			}
		}

		return $this->translations_from_mo_file[ $original_string ][ $language ];

	}

	function translate_attributes_label_in_wp_taxonomies( $taxonomy, $obj_type, $args ) {
		global $wp_taxonomies;
		$obj_type = array_unique( (array) $obj_type );

		$current_language = $this->sitepress->get_current_language();

		if ( $current_language != 'all' && in_array( 'product', $obj_type ) && substr( $taxonomy, 0, 3 ) == 'pa_' && isset( $wp_taxonomies[ $taxonomy ] ) ) {

			if ( is_array( $args['labels'] ) ) {
				$name = $args['labels']['singular_name'];
			} else {
				$name = $args['labels']->name;
			}

			$wp_taxonomies[ $taxonomy ]->labels->name = apply_filters( 'wpml_translate_single_string', $name, 'WordPress', 'taxonomy singular name: ' . $name, $current_language );


		}

	}

}