<?php

/**
 * Class WCML_Tab_Manager
 */
class WCML_Tab_Manager {
	/**
	 * @var WPML_Element_Translation_Package
	 */
	private $tp;

	/**
	 * @var SitePress
	 */
	private $sitepress;

	/**
	 * @var WooCommerce
	 */
	private $woocommerce;

	/**
	 * @var woocommerce_wpml
	 */
	private $woocommerce_wpml;

	/**
	 * @var wpdb
	 */
	private $wpdb;

	/**
	 * WCML_Tab_Manager constructor.
	 *
	 * @param SitePress $sitepress
	 * @param WooCommerce $woocommerce
	 * @param woocommerce_wpml $woocommerce_wpml
	 * @param wpdb $wpdb
	 * @param WPML_Element_Translation_Package $tp
	 */
	function __construct( SitePress $sitepress, WooCommerce $woocommerce, woocommerce_wpml $woocommerce_wpml, wpdb $wpdb, WPML_Element_Translation_Package $tp ) {
		$this->sitepress        = $sitepress;
		$this->woocommerce      = $woocommerce;
		$this->woocommerce_wpml = $woocommerce_wpml;
		$this->wpdb             = $wpdb;
		$this->tp               = $tp;
	}

	public function add_hooks(){
		add_action( 'wcml_update_extra_fields', array( $this, 'sync_tabs' ), 10, 4 );
		add_action( 'wcml_gui_additional_box_html', array( $this, 'custom_box_html' ), 10, 3 );
		add_filter( 'wcml_gui_additional_box_data', array( $this, 'custom_box_html_data' ), 10, 4 );
		add_filter( 'wpml_duplicate_custom_fields_exceptions', array( $this, 'duplicate_custom_fields_exceptions' ) );
		add_action( 'wcml_after_duplicate_product', array( $this, 'duplicate_product_tabs' ) , 10, 2 );

		add_filter( 'wc_tab_manager_tab_id', array( $this, 'wc_tab_manager_tab_id' ), 10, 1 );

		if ( $this->sitepress->get_wp_api()->version_compare( $this->sitepress->get_wp_api()->constant( 'WCML_VERSION' ), '3.7.2', '>' ) ) {
			add_filter( 'option_wpml_config_files_arr', array( $this, 'make__product_tabs_not_translatable_by_default' ), 0 );
		}

		if ( is_admin() ) {

			add_action( 'save_post', array( $this, 'force_set_language_information_on_product_tabs' ), 10, 2 );
			add_action( 'save_post', array( $this, 'sync_product_tabs' ), 10, 2 );

			add_filter( 'wpml_tm_translation_job_data', array( $this, 'append_custom_tabs_to_translation_package' ), 10, 2 );
			add_action( 'wpml_translation_job_saved',   array( $this, 'save_custom_tabs_translation' ), 10, 3 );
			add_action( 'woocommerce_product_data_panels', array( $this, 'show_pointer_info' ) );

			add_filter( 'wcml_do_not_display_custom_fields_for_product', array( $this, 'replace_tm_editor_custom_fields_with_own_sections' ) );

			add_filter( 'wpml_duplicate_custom_fields_exceptions', array( $this, 'duplicate_categories_exception' ) );
			add_action( 'wpml_after_copy_custom_field', array( $this, 'translate_categories' ), 10, 3 );

		}else{
			add_filter( 'option_wc_tab_manager_default_layout', array( $this, 'filter_default_layout' ) );
		}

	}

	/**
	 * @param $wpml_config_array
	 *
	 * @return mixed
	 */
	function make__product_tabs_not_translatable_by_default( $wpml_config_array ) {

		if ( isset( $wpml_config_array->plugins['WooCommerce Tab Manager'] ) ) {
			$wpml_config_array->plugins['WooCommerce Tab Manager'] =
				str_replace( '<custom-field action="translate">_product_tabs</custom-field>',
					'<custom-field action="nothing">_product_tabs</custom-field>',
					$wpml_config_array->plugins['WooCommerce Tab Manager']
				);
		}

		return $wpml_config_array;
	}

	/**
	 * @param $original_product_id
	 * @param $trnsl_product_id
	 * @param $data
	 * @param $lang
	 *
	 * @return bool
	 */
	function sync_tabs( $original_product_id, $trnsl_product_id, $data, $lang ) {
		//check if "duplicate" product
		if ( ( isset( $_POST['icl_ajx_action'] ) && ( 'make_duplicates' === $_POST['icl_ajx_action'] ) ) || ( get_post_meta( $trnsl_product_id , '_icl_lang_duplicate_of', true ) ) ) {
			$this->duplicate_tabs( $original_product_id, $trnsl_product_id, $lang );
		}

		$orig_prod_tabs = $this->get_product_tabs( $original_product_id );

		if ( $orig_prod_tabs ) {
			$trnsl_product_tabs = array();
			$i = 0;
			foreach ( $orig_prod_tabs as $key => $orig_prod_tab ) {
				switch ( $orig_prod_tab['type'] ) {
					case 'core':
						$default_language = $this->woocommerce_wpml->products->get_original_product_language( $original_product_id );
						$current_language = $this->sitepress->get_current_language();
						$trnsl_product_tabs[ $key ] = $orig_prod_tabs[ $key ];
						$title = isset( $data[ md5( 'coretab_'.$orig_prod_tab['id'].'_title' ) ] ) ? $data[ md5( 'coretab_'.$orig_prod_tab['id'].'_title' ) ] : '';
						$heading = isset( $data[ md5( 'coretab_'.$orig_prod_tab['id'].'_heading' ) ] ) ? $data[ md5( 'coretab_'.$orig_prod_tab['id'].'_heading' ) ] : '';

						if ( $default_language !== $lang ) {

							$this->refresh_text_domain( $lang );

							if ( ! $title ) {
								$title = isset( $_POST['product_tab_title'][ $orig_prod_tab['position'] ] ) ? $_POST['product_tab_title'][ $orig_prod_tab['position'] ] : $orig_prod_tabs[ $key ]['title'];
								$title = __( $title, 'woocommerce' );
							}

							if ( ! $heading && ( isset( $orig_prod_tabs[ $key ]['heading'] ) || isset( $_POST['product_tab_heading'][ $orig_prod_tab['position'] ] ) ) ) {
								$heading = isset( $_POST['product_tab_heading'][ $orig_prod_tab['position'] ] ) ? $_POST['product_tab_heading'][ $orig_prod_tab['position'] ] : $orig_prod_tabs[ $key ]['heading'];
								$heading = __( $heading, 'woocommerce' );
							}

							$this->refresh_text_domain( $current_language );
						}

						$trnsl_product_tabs[ $key ]['title'] = $title;
						$trnsl_product_tabs[ $key ]['heading'] = $heading;
						break;
					case 'global':
						$trnsl_product_tabs = $this->set_global_tab( $orig_prod_tab, $trnsl_product_tabs, $lang );
						break;
					case 'product':
						$tab_id = false;
						$title_key = md5( 'tab_' . $orig_prod_tab['position'] . '_title' );
						$heading_key = md5( 'tab_' . $orig_prod_tab['position'] . '_heading' );
						$title = isset( $data[ $title_key ] ) ? sanitize_text_field( $data[ $title_key ] ) : '';
						$content = isset( $data[ $heading_key ] ) ? wp_kses_post( $data[ $heading_key ] ) : '';

						$trnsl_product_tabs = $this->set_product_tab( $orig_prod_tab, $trnsl_product_tabs, $lang, $trnsl_product_id, $tab_id, $title, $content );

						$i++;
						break;
				}
			}
			update_post_meta( $trnsl_product_id, '_product_tabs', $trnsl_product_tabs );

			return true;
		}

		return false;
	}

	/**
	 * @param $original_product_id
	 * @param $trnsl_product_id
	 * @param $lang
	 */
	function duplicate_tabs( $original_product_id, $trnsl_product_id, $lang ) {
		$orig_prod_tabs = maybe_unserialize( get_post_meta( $original_product_id, '_product_tabs', true ) );
		$prod_tabs = array();
		foreach ( $orig_prod_tabs as $key => $orig_prod_tab ) {
			switch ( $orig_prod_tab['type'] ) {
				case 'core':
					$prod_tabs[ $key ] = $orig_prod_tab;
					$this->refresh_text_domain( $lang );
					$prod_tabs[ $key ]['title'] = __( $orig_prod_tab['title'], 'woocommerce' );
					if ( isset( $orig_prod_tab['heading'] ) ) {
						$prod_tabs[ $key ]['heading'] = __( $orig_prod_tab['heading'], 'woocommerce' );
					}
					$orig_lang = $this->sitepress->get_language_for_element( $original_product_id, 'post_product' );
					$this->refresh_text_domain( $orig_lang );
				break;
				case 'global':
					$prod_tabs = $this->set_global_tab( $orig_prod_tab, $prod_tabs, $lang );
				break;
				case 'product':
					$original_tab = get_post( $orig_prod_tab['id'] );
					$prod_tabs = $this->set_product_tab( $orig_prod_tab, $prod_tabs, $lang, $trnsl_product_id, false, $original_tab->post_title , $original_tab->post_content );
				break;
			}
		}

		update_post_meta( $trnsl_product_id, '_product_tabs', $prod_tabs );
	}

	/**
	 * @param $lang
	 */
	function refresh_text_domain( $lang ) {
		unload_textdomain( 'woocommerce' );
		$this->sitepress->switch_lang( $lang );
		$this->woocommerce->load_plugin_textdomain();
	}

	/**
	 * @param $orig_prod_tab
	 * @param $trnsl_product_tabs
	 * @param $lang
	 *
	 * @return mixed
	 */
	function set_global_tab( $orig_prod_tab, $trnsl_product_tabs, $lang ) {
		$tr_tab_id = apply_filters( 'translate_object_id', $orig_prod_tab['id'], 'wc_product_tab', true, $lang );
		$trnsl_product_tabs[ $orig_prod_tab['type'].'_tab_'. $tr_tab_id ] = array(
			'position' => $orig_prod_tab['position'],
			'type'     => $orig_prod_tab['type'],
			'id'       => $tr_tab_id,
			'name'     => get_post( $tr_tab_id )->post_name,
		);
		return $trnsl_product_tabs;
	}

	/**
	 * @param $orig_prod_tab
	 * @param $trnsl_product_tabs
	 * @param $lang
	 * @param $trnsl_product_id
	 * @param $tab_id
	 * @param $title
	 * @param $content
	 *
	 * @return mixed
	 */
	function set_product_tab( $orig_prod_tab, $trnsl_product_tabs, $lang, $trnsl_product_id, $tab_id, $title, $content ) {
		if ( ! $tab_id ) {
			$tr_tab_id = apply_filters( 'translate_object_id', $orig_prod_tab['id'], 'wc_product_tab', false, $lang );

			if ( ! is_null( $tr_tab_id ) ) {
				$tab_id = $tr_tab_id;
			}
		}

		if ( $tab_id ) {
			//update existing tab
			$args = array();
			$args['post_title'] = $title;
			$args['post_content'] = $content;
			$this->wpdb->update( $this->wpdb->posts, $args, array( 'ID' => $tab_id ) );
		} else {
			//tab not exist creating new
			$args = array();
			$args['post_title']   = $title;
			$args['post_content'] = $content;
			$args['post_author']  = get_current_user_id();
			$args['post_name']    = sanitize_title( $title );
			$args['post_type']    = 'wc_product_tab';
			$args['post_parent']  = $trnsl_product_id;
			$args['post_status']  = 'publish';
			$this->wpdb->insert( $this->wpdb->posts, $args );

			$tab_id = $this->wpdb->insert_id;
			$tab_trid = $this->sitepress->get_element_trid( $orig_prod_tab['id'], 'post_wc_product_tab' );
			if ( ! $tab_trid ) {
				$this->sitepress->set_element_language_details( $orig_prod_tab['id'], 'post_wc_product_tab', false, $this->sitepress->get_default_language() );
				$tab_trid = $this->sitepress->get_element_trid( $orig_prod_tab['id'], 'post_wc_product_tab' );
			}
			$this->sitepress->set_element_language_details( $tab_id, 'post_wc_product_tab', $tab_trid, $lang );
		}


		if ( empty( $title ) || strlen( $title ) != strlen( utf8_encode( $title ) ) ) {
			$tab_name = "product-tab-". $tab_id;
		} else {
			$tab_name = sanitize_title( $title );
		}

		$trnsl_product_tabs[ $orig_prod_tab['type'] . '_tab_' . $tab_id ] = array(
			'position' => $orig_prod_tab['position'],
			'type'     => $orig_prod_tab['type'],
			'id'       => $tab_id,
			'name'     => $tab_name,
		);

		return $trnsl_product_tabs;
	}

	/**
	 * @param $exceptions
	 *
	 * @return array
	 */
	function duplicate_custom_fields_exceptions( $exceptions ) {
		$exceptions[] = '_product_tabs';
		return $exceptions;
	}

	/**
	 * @param $obj
	 * @param $product_id
	 * @param $data
	 *
	 * @return bool
	 */
	function custom_box_html( $obj, $product_id, $data ) {

		if ( 'yes' !== get_post_meta( $product_id, '_override_tab_layout', true ) ) {
			return false;
		}

		$orig_prod_tabs = $this->get_product_tabs( $product_id );
		if ( ! $orig_prod_tabs ) {
			return false;
		}

		$tabs_section = new WPML_Editor_UI_Field_Section( __( 'Product tabs', 'woocommerce-multilingual' ) );

		$keys = array_keys( $orig_prod_tabs );
		$last_key = end( $keys );
		$divider = true;

		foreach ( $orig_prod_tabs as $key => $prod_tab ) {
			if ( $key === $last_key ) {
				$divider = false;
			}

			if ( in_array( $prod_tab['type'], array( 'product', 'core' ) ) ) {
				if ( 'core' === $prod_tab['type'] ) {
					$group = new WPML_Editor_UI_Field_Group( $prod_tab['title'], $divider );
					$tab_field = new WPML_Editor_UI_Single_Line_Field( 'coretab_' . $prod_tab['id'].'_title', __( 'Title', 'woocommerce-multilingual' ), $data, false );
					$group->add_field( $tab_field );
					$tab_field = new WPML_Editor_UI_Single_Line_Field( 'coretab_' . $prod_tab['id'].'_heading' , __( 'Heading', 'woocommerce-multilingual' ), $data, false );
					$group->add_field( $tab_field );
					$tabs_section->add_field( $group );
				} else {
					$group = new WPML_Editor_UI_Field_Group( ucfirst( str_replace( '-', ' ', $prod_tab['name'] ) ), $divider );
					$tab_field = new WPML_Editor_UI_Single_Line_Field( 'tab_'.$prod_tab['position'].'_title', __( 'Title', 'woocommerce-multilingual' ), $data, false );
					$group->add_field( $tab_field );
					$tab_field = new WCML_Editor_UI_WYSIWYG_Field( 'tab_'.$prod_tab['position'].'_heading' , null, $data, false );
					$group->add_field( $tab_field );
					$tabs_section->add_field( $group );
				}
			}
		}
		$obj->add_field( $tabs_section );

		return true;
	}

	/**
	 * @param $data
	 * @param $product_id
	 * @param $translation
	 * @param $lang
	 *
	 * @return mixed
	 */
	function custom_box_html_data( $data, $product_id, $translation, $lang ) {

		$orig_prod_tabs = $this->get_product_tabs( $product_id );

		if ( empty( $orig_prod_tabs ) ) {
			return $data;
		}

		foreach ( $orig_prod_tabs as $key => $prod_tab ) {
			if ( in_array( $prod_tab['type'], array( 'product', 'core' ) ) ) {
				if ( 'core' === $prod_tab['type'] ) {
					$data[ 'coretab_' . $prod_tab['id'] . '_title' ]   = array( 'original' => $prod_tab['title'] );
					$data[ 'coretab_' . $prod_tab['id'] . '_heading' ] = array( 'original' => isset( $prod_tab['heading'] ) ? $prod_tab['heading'] : '' );
				} else {
					$data[ 'tab_' . $prod_tab['position'] . '_title' ]   = array( 'original' => get_the_title( $prod_tab['id'] ) );
					$data[ 'tab_' . $prod_tab['position'] . '_heading' ] = array( 'original' => get_post( $prod_tab['id'] )->post_content );
				}
			}
		}

		if ( $translation ) {
			$tr_prod_tabs = $this->get_product_tabs( $translation->ID );


			if ( ! is_array( $tr_prod_tabs ) ) {
				return $data; // __('Please update original product','woocommerce-multilingual');
			}

			foreach ( $tr_prod_tabs as $key => $prod_tab ) {
				if ( in_array( $prod_tab['type'], array( 'product', 'core' ) ) ) {
					if ( 'core' === $prod_tab['type'] ) {
						$data[ 'coretab_' . $prod_tab['id'] . '_title' ]['translation'] = $prod_tab['title'];
						$data[ 'coretab_' . $prod_tab['id'] . '_heading' ]['translation'] = isset( $prod_tab['heading'] ) ? $prod_tab['heading'] : '';
					} else {
						$data[ 'tab_'.$prod_tab['position'].'_title' ]['translation'] = get_the_title( $prod_tab['id'] );
						$data[ 'tab_'.$prod_tab['position'].'_heading' ]['translation'] = get_post( $prod_tab['id'] )->post_content;
					}
				}
			}
		} else {
			$current_language = $this->sitepress->get_current_language();
			foreach ( $orig_prod_tabs as $key => $prod_tab ) {
				if ( 'core' === $prod_tab['type'] ) {
					unload_textdomain( 'woocommerce' );
					$this->sitepress->switch_lang( $lang );
					$this->woocommerce->load_plugin_textdomain();
					$title = __( $prod_tab['title'], 'woocommerce' );
					if ( $prod_tab['title'] !== $title ) {
						$data[ 'coretab_' . $prod_tab['id'] . '_title' ]['translation'] = $title;
					}

					if ( ! isset( $prod_tab['heading'] ) ) {
						$data[ 'coretab_'.$prod_tab['id'].'_heading' ]['translation'] = '';
					} else {
						$heading = __( $prod_tab['heading'], 'woocommerce' );
						if ( $prod_tab['heading'] !== $heading ) {
							$data[ 'coretab_' . $prod_tab['id'] . '_heading' ]['translation'] = $heading;
						}
					}

					unload_textdomain( 'woocommerce' );
					$this->sitepress->switch_lang( $current_language );
					$this->woocommerce->load_plugin_textdomain();
				}
			}
		}

		return $data;

	}

	/**
	 * @param $new_id
	 * @param $original_post
	 */
	function duplicate_product_tabs( $new_id, $original_post ) {
		if ( function_exists( 'wc_tab_manager_duplicate_product' ) ) {
			wc_tab_manager_duplicate_product( $new_id, $original_post );
		}
	}

	/**
	 * @param $post_id
	 * @param $post
	 */
	function force_set_language_information_on_product_tabs( $post_id, $post ) {
		if ( 'wc_product_tab' === $post->post_type ) {

			$language = $this->sitepress->get_language_for_element( $post_id, 'post_wc_product_tab' );
			if ( empty( $language ) && $post->post_parent ) {
				$parent_language = $this->sitepress->get_language_for_element( $post->post_parent, 'post_product' );
				if ( $parent_language ) {
					$this->sitepress->set_element_language_details( $post_id, 'post_wc_product_tab', null, $parent_language );
				}
			}
		}

	}

	/**
	 * @param $package
	 * @param $post
	 *
	 * @return mixed
	 */
	function append_custom_tabs_to_translation_package( $package, $post ) {

		if ( 'product' === $post->post_type ) {

			$override_tab_layout = get_post_meta( $post->ID , '_override_tab_layout', true );

			if ( 'yes' === $override_tab_layout ) {

				$meta = (array) get_post_meta( $post->ID, '_product_tabs', true );

				foreach ( $meta as $key => $value ) {

					if ( preg_match( '/product_tab_([0-9]+)/', $key, $matches ) ) {

						$wc_product_tab_id = $matches[1];
						$wc_product_tab = get_post( $wc_product_tab_id );

						$package['contents'][ 'product_tabs:product_tab:' . $wc_product_tab_id . ':title' ] = array(
							'translate' => 1,
							'data'      => $this->tp->encode_field_data( $wc_product_tab->post_title, 'base64' ),
							'format'    => 'base64',
						);

						$package['contents'][ 'product_tabs:product_tab:' . $wc_product_tab_id . ':description' ] = array(
							'translate' => 1,
							'data'      => $this->tp->encode_field_data( $wc_product_tab->post_content, 'base64' ),
							'format'    => 'base64',
						);

					} elseif ( preg_match( '/^core_tab_(.+)$/', $key, $matches ) ) {

						$package['contents'][ 'product_tabs:core_tab_title:' . $matches[1] ] = array(
							'translate' => 1,
							'data'      => $this->tp->encode_field_data( $value['title'], 'base64' ),
							'format'    => 'base64',
						);

						if ( isset( $value['heading'] ) ) {
							$package['contents'][ 'product_tabs:core_tab_heading:' . $matches[1] ] = array(
								'translate' => 1,
								'data'      => $this->tp->encode_field_data( $value['heading'], 'base64' ),
								'format'    => 'base64',
							);
						}
					}
				}
			}
		}

		return $package;
	}

	/**
	 * @param $post_id
	 * @param $data
	 * @param $job
	 */
	function save_custom_tabs_translation( $post_id, $data, $job ) {
		$translated_product_tabs_updated    = false;

		$original_product_tabs = get_post_meta( $job->original_doc_id, '_product_tabs', true );

		if ( $original_product_tabs ) {

			// custom tabs
			$product_tab_translations  = array();

			foreach ( $data as $value ) {

				if ( preg_match( '/product_tabs:product_tab:([0-9]+):(.+)/', $value['field_type'], $matches ) ) {

					$wc_product_tab_id = $matches[1];
					$field = $matches[2];

					$product_tab_translations[ $wc_product_tab_id ][ $field ] = $value['data'];
				}
			}

			if ( $product_tab_translations ) {

				$translated_product_tabs = get_post_meta( $post_id, '_product_tabs', true );

				foreach ( $product_tab_translations as $wc_product_tab_id => $value ) {

					$new_wc_product_tab = array(
						'post_type'    => 'wp_product_tab',
						'post_title'   => $value['title'],
						'post_content' => $value['description'],
						'post_status'  => 'publish',
					);

					$wc_product_tab_id_translated = wp_insert_post( $new_wc_product_tab );

					if ( $wc_product_tab_id_translated ) {

						$wc_product_tab_trid = $this->sitepress->get_element_trid( $wc_product_tab_id, 'post_wc_product_tab' );
						$this->sitepress->set_element_language_details( $wc_product_tab_id_translated, 'post_wc_product_tab', $wc_product_tab_trid, $job->language_code );

						$wc_product_tab_translated = get_post( $wc_product_tab_id_translated );

						$translated_product_tabs[ 'product_tab_' . $wc_product_tab_id_translated ] = array(
							'position' => $original_product_tabs[ 'product_tab_' . $wc_product_tab_id ]['position'],
							'type'     => 'product',
							'id'       => $wc_product_tab_id_translated,
							'name'     => $wc_product_tab_translated->post_name,

						);
					}
				}

				$translated_product_tabs_updated = true;
			}

			// the other tabs
			$product_tab_translations  = array();

			foreach ( $data as $value ) {

				if ( preg_match( '/product_tabs:core_tab_(.+):(.+)/', $value['field_type'], $matches ) ) {

					$tab_field  = $matches[1];
					$tab_id     = $matches[2];

					$product_tab_translations[ $tab_id ][ $tab_field ] = $value['data'];
				}
			}

			if ( $product_tab_translations ) {
				foreach ( $product_tab_translations as $id => $tab ) {

					$translated_product_tabs[ 'core_tab_' . $id ] = array(
						'type'      => 'core',
						'position'  => $original_product_tabs[ 'core_tab_' . $id ]['position'],
						'id'        => $id,
						'title'     => $tab['title'],
					);

					if ( isset( $tab['heading'] ) ) {
						$translated_product_tabs[ 'core_tab_' . $id ]['heading'] = $tab['heading'];
					}
				}

				$translated_product_tabs_updated = true;
			}

			if ( true === $translated_product_tabs_updated && isset( $translated_product_tabs ) ) {
				update_post_meta( $post_id, '_product_tabs', $translated_product_tabs );
			}
		}
	}

	/**
	 * @param $product_id
	 *
	 * @return array
	 */
	public function get_product_tabs( $product_id ) {

		$override_tab_layout = get_post_meta( $product_id, '_override_tab_layout', true );

		if ( 'yes' == $override_tab_layout ) {
			// product defines its own tab layout?
			$product_tabs = (array) get_post_meta( $product_id, '_product_tabs', true );
		} else {
			// otherwise, get the default layout if any
			$product_tabs = (array) get_option( 'wc_tab_manager_default_layout', false );
		}

		return $product_tabs;
	}

	public function sync_product_tabs( $post_id, $post ){

		$override_tab_layout = get_post_meta( $post_id, '_override_tab_layout', true );

		if ( $override_tab_layout && $this->woocommerce_wpml->products->is_original_product( $post_id ) ){

			$original_product_tabs = $this->get_product_tabs( $post_id );

			$trid = $this->sitepress->get_element_trid( $post_id, 'post_'  . $post->post_type );
			$translations = $this->sitepress->get_element_translations( $trid, 'post_'  . $post->post_type, true );



			foreach( $translations as $language => $translation ){

				if( empty( $translation->original ) ){

					$translated_product_tabs = $this->get_product_tabs( $translation->element_id );

					// sync tab positions for product tabs
					foreach( $original_product_tabs as $tab ){
						if( $tab['type'] == 'product' ){
							$translated_tab_product_id = apply_filters( 'translate_object_id', $tab['id'], 'wc_product_tab', false, $language );
							if( $translated_tab_product_id && is_array( $translated_product_tabs['product_tab_' . $translated_tab_product_id ] ) ){
								$translated_product_tabs['product_tab_' . $translated_tab_product_id ]['position'] = $tab['position'];
							}
						}
					}

					// sync translated core tabs with original tabs
					foreach( $translated_product_tabs as $tab_key => $tab ){
						if( $tab['type'] === 'core' && !isset( $original_product_tabs[$tab_key] ) ){
							unset( $translated_product_tabs[$tab_key] );
						}
					}

					update_post_meta( $translation->element_id, '_product_tabs', $translated_product_tabs );

				}

			}

		}

	}

	/**
	 * @param $tab_id
	 *
	 * @return mixed|void
	 */
	function wc_tab_manager_tab_id( $tab_id ) {
		return apply_filters( 'wpml_object_id', $tab_id, 'wc_product_tab', true );
	}

	public function filter_default_layout( $default_tabs ){

		if( is_array( $default_tabs ) ){
			foreach( $default_tabs as $tab_key => $default_tab ){
				if( substr( $tab_key, 0, 10 ) == 'global_tab' ){
					$trnsl_tab_id = apply_filters( 'translate_object_id', $default_tab[ 'id' ], 'wc_product_tab', true, $this->sitepress->get_current_language() );

					if( $trnsl_tab_id != $default_tab[ 'id' ] ){
						$default_tabs[ 'global_tab_'.$trnsl_tab_id ] = $default_tab;
						$default_tabs[ 'global_tab_'.$trnsl_tab_id ][ 'id' ] = $trnsl_tab_id;
						$default_tabs[ 'global_tab_'.$trnsl_tab_id ][ 'name' ] = get_post( $trnsl_tab_id )->post_name;
						unset( $default_tabs[ $tab_key ] );
					}
				}
			}
		}

		return $default_tabs;
	}

	public function show_pointer_info(){

		$pointer_ui = new WCML_Pointer_UI(
			sprintf( __( 'You can translate your custom product tabs on the %sWooCommerce product translation page%s', 'woocommerce-multilingual' ), '<a href="'.admin_url('admin.php?page=wpml-wcml').'">', '</a>' ),
			'https://wpml.org/documentation/woocommerce-extensions-compatibility/translating-woocommerce-tab-manager-woocommerce-multilingual/',
			'woocommerce_product_tabs>p'
		);

		$pointer_ui->show();

	}

	function replace_tm_editor_custom_fields_with_own_sections( $fields ){
		$fields[] = '_product_tabs';

		return $fields;
	}

	function duplicate_categories_exception( $fields ) {
		$fields[] = '_wc_tab_categories';

		return $fields;
	}

	function translate_categories( $post_id_from, $post_id_to, $meta_key ) {
		if ( '_wc_tab_categories' === $meta_key ) {
			// Saving has already been processed, remove nonce so that we dont
			// process translations too (which would overwrite _wc_tab_categories.
			unset( $_POST['wc_tab_manager_metabox_nonce'] );

			$args = array('element_id' => $post_id_to, 'element_type' => 'wc_product_tab' );
			$language = apply_filters( 'wpml_element_language_code', false, $args );

			$categories = array();
			$meta_value = get_post_meta( $post_id_from, $meta_key, true );
			foreach ( $meta_value as $category ) {
				$categories[] = apply_filters( 'wpml_object_id', $category, 'product_cat', true, $language );
			}

			update_post_meta( $post_id_to, $meta_key, $categories, $meta_value );
		}
	}

}
