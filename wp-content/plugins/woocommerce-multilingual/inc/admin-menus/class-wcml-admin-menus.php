<?php

class WCML_Admin_Menus{

    private static $woocommerce_wpml;
    private static $sitepress;
    private static $wpdb;

    public static function set_up_menus( &$woocommerce_wpml, &$sitepress, &$wpdb, $check_dependencies ){
        self::$woocommerce_wpml =& $woocommerce_wpml;
        self::$sitepress =& $sitepress;
        self::$wpdb =& $wpdb;

        if( self::is_page_without_admin_language_switcher() ){
            self::remove_wpml_admin_language_switcher();
        }

        if( is_admin() && !is_null( $sitepress ) && $check_dependencies ){

	        add_action( 'admin_menu', array(__CLASS__, 'register_menus' ), 80 );

            add_action( 'admin_footer', array(__CLASS__, 'documentation_links' ) );
            add_action( 'admin_head', array( __CLASS__, 'hide_multilingual_content_setup_box' ) );
            add_action( 'admin_init', array( __CLASS__, 'restrict_admin_with_redirect' ) );
        }

        add_filter( 'woocommerce_prevent_admin_access', array( __CLASS__, 'check_user_admin_access' ) );

        add_action( 'admin_head', array( __CLASS__, 'add_menu_warning' ) );
    }

    public static function register_menus(){
        global $WPML_Translation_Management;

        if( self::$woocommerce_wpml->check_dependencies || class_exists( 'WooCommerce' ) ) {

            add_submenu_page(
                'woocommerce',
                __( 'WooCommerce Multilingual', 'woocommerce-multilingual' ),
                __( 'WooCommerce Multilingual', 'woocommerce-multilingual' ),
                'wpml_operate_woocommerce_multilingual',
                'wpml-wcml',
                array( __CLASS__, 'render_menus' )
            );

            if( !current_user_can('wpml_manage_woocommerce_multilingual') && current_user_can('wpml_operate_woocommerce_multilingual') ) {
                //force add translations-queue page for shop manager
                $wp_api = self::$sitepress->get_wp_api();
                if (!$wp_api->current_user_can('wpml_manage_translation_management')) {
	                $menu               = array();
	                $menu['order']      = 400;
	                $menu['page_title'] = __( 'Translations', 'wpml-translation-management' );
	                $menu['menu_title'] = __( 'Translations', 'wpml-translation-management' );
	                $menu['capability'] = 'wpml_operate_woocommerce_multilingual';
	                $menu['menu_slug']  = WPML_TM_FOLDER . '/menu/translations-queue.php';
	                $menu['function']   = array( $WPML_Translation_Management, 'translation_queue_page' );
	                $menu['icon_url']   = ICL_PLUGIN_URL . '/res/img/icon16.png';
	                do_action( 'wpml_admin_menu_register_item', $menu );
                }
            }
        } else {

            add_menu_page(
                __( 'WooCommerce Multilingual', 'woocommerce-multilingual' ),
                __( 'WooCommerce Multilingual', 'woocommerce-multilingual' ),
                'wpml_manage_woocommerce_multilingual',
                'wpml-wcml',
                array( __CLASS__, 'render_menus' ),
                WCML_PLUGIN_URL . '/res/images/icon16.png'
            );

        }
    }

    public static function render_menus(){

        if( self::$woocommerce_wpml->check_dependencies ){
            $menus_wrap = new WCML_Menus_Wrap( self::$woocommerce_wpml );
            $menus_wrap->show();
        }else{
            global $sitepress;
            $plugins_wrap = new WCML_Plugins_Wrap( self::$woocommerce_wpml, $sitepress );
            $plugins_wrap->show();
        }

    }

    private static function is_page_without_admin_language_switcher(){
        global $pagenow;

        $get_post_type = isset( $_GET['post_type'] ) ? $_GET['post_type'] : false;
        $get_post      = isset( $_GET['post'] ) ? $_GET['post'] : false;
        $get_page      = isset( $_GET['page'] ) ? $_GET['page'] : false;

        $is_page_wpml_wcml          = isset($_GET['page']) && $_GET['page'] == 'wpml-wcml';
        $is_new_order_or_coupon     = in_array( $pagenow, array( 'edit.php', 'post-new.php' ) ) &&
                                        $get_post_type &&
                                        in_array( $get_post_type, array( 'shop_coupon', 'shop_order' ) );
        $is_edit_order_or_coupon    = $pagenow == 'post.php' && $get_post &&
                                        in_array( get_post_type( $get_post ), array( 'shop_coupon', 'shop_order' ) );
        $is_shipping_zones          = $get_page == 'shipping_zones';
        $is_attributes_page          =  apply_filters( 'wcml_is_attributes_page', $get_page == 'product_attributes' );


        return is_admin() && (
                $is_page_wpml_wcml ||
                $is_new_order_or_coupon ||
                $is_edit_order_or_coupon ||
                $is_shipping_zones ||
                $is_attributes_page
              );

    }

    public static function remove_wpml_admin_language_switcher(){

        remove_action( 'wp_before_admin_bar_render', array(self::$sitepress, 'admin_language_switcher') );

    }

    public static function documentation_links() {
        global $post, $pagenow;

        if ( is_null( $post ) )
            return;

	    $tracking_link = new WCML_Tracking_Link();

        $get_post_type = get_post_type( $post->ID );

        if ( $get_post_type == 'product' && $pagenow == 'edit.php' ) {
            $quick_edit_notice = '<div id="quick_edit_notice" style="display:none;"><p>' .
                sprintf( __( "Quick edit is disabled for product translations. It\'s recommended to use the %s for editing products translations. %s",
                    'woocommerce-multilingual' ), '<a href="' . admin_url( 'admin.php?page=wpml-wcml&tab=products' ) . '" >' .
                    __( 'WooCommerce Multilingual products editor', 'woocommerce-multilingual' ) . '</a>',
                    '<a href="" class="quick_product_trnsl_link" >' . __( 'Edit this product translation', 'woocommerce-multilingual' ) . '</a>'
                ) . '</p></div>';
            $quick_edit_notice_prod_link = '<input type="hidden" id="wcml_product_trnsl_link" value="' . admin_url( 'admin.php?page=wpml-wcml&tab=products&prid=' ) . '">';
            ?>
            <script type="text/javascript">
                jQuery(".subsubsub").append('<?php echo $quick_edit_notice ?>');
                jQuery(".subsubsub").append('<?php echo $quick_edit_notice_prod_link ?>');
                jQuery(".quick_hide a").on('click', function () {
                    jQuery(".quick_product_trnsl_link").attr('href', jQuery("#wcml_product_trnsl_link").val() + jQuery(this).closest('tr').attr('id').replace(/post-/, ''));
                });

                //lock feature for translations
                jQuery(document).on('click', '.featured a', function () {
                    if (jQuery(this).closest('tr').find('.quick_hide').size() > 0) {
                        return false;
                    }

                });
            </script>
            <?php
        }

        $template = '
            <span class="button" style="padding:4px;margin-top:0px; float: left;">
                <img align="baseline" src="' . ICL_PLUGIN_URL . '/res/img/icon16.png" width="16" height="16" style="margin-bottom:-4px" /> 
                <a href="%1$s" target="_blank" style="text-decoration: none;">%2$s</a>
            </span><br /><br />
        ';

        if ( isset($_GET['taxonomy']) ) {
            $pos = strpos( $_GET['taxonomy'], 'pa_' );

            if ( $pos !== false && $pagenow == 'edit-tags.php' ) {
	            $href = $tracking_link->generate(
		            'https://wpml.org/documentation/related-projects/woocommerce-multilingual/',
		            'woocommerce-multilingual',
		            'documentation',
		            '#3'
	            );
	            $prot_link = sprintf( $template, $href, __( 'How to translate attributes', 'woocommerce-multilingual' ) );
                ?>
                <script type="text/javascript">
                    jQuery("table.widefat").before('<?php echo $prot_link ?>');
                </script>
                <?php
            }
        }

        if ( isset($_GET['taxonomy']) && $_GET['taxonomy'] == 'product_cat' ) {
	        $href = $tracking_link->generate(
		        'https://wpml.org/documentation/related-projects/woocommerce-multilingual/',
		        'woocommerce-multilingual',
		        'documentation',
		        '#3'
	        );
	        $prot_link = sprintf( $template, $href, __( 'How to translate product categories', 'woocommerce-multilingual' ) );
            ?>
            <script type="text/javascript">
                jQuery("table.widefat").before('<?php echo $prot_link ?>');
            </script>
            <?php
        }
    }

    public static function hide_multilingual_content_setup_box(){
        remove_meta_box('icl_div_config', convert_to_screen('shop_order'), 'normal');
        remove_meta_box('icl_div_config', convert_to_screen('shop_coupon'), 'normal');
    }

	public static function restrict_admin_with_redirect() {
		global $pagenow;

		if ( self::$woocommerce_wpml->settings['trnsl_interface'] ) {

			if (
				'post.php' === $pagenow &&
				! is_ajax() &&
				self::is_post_product_translation_screen() &&
				self::is_post_action_needs_redirect()
			) {
				$prid = (int) $_GET['post'];
				if( 'auto-draft' !== get_post_status( $prid ) ){
					wp_redirect( admin_url( 'admin.php?page=wpml-wcml&tab=products&prid=' . $prid ) );
					exit;
                }
			} elseif ( self::is_admin_duplicate_page_action( $pagenow ) && self::is_post_product_translation_screen() ) {

				wp_redirect( admin_url( 'admin.php?page=wpml-wcml&tab=products' ) );
				exit;
			}

		} elseif ( 'post.php' === $pagenow && self::is_post_product_translation_screen() ) {
			add_action( 'admin_notices', array( __CLASS__, 'inf_editing_product_in_non_default_lang' ) );
		}

	}

	private static function is_post_product_translation_screen(){
        return isset( $_GET['post'] ) && 'product' === get_post_type( $_GET['post'] ) && ! self::$woocommerce_wpml->products->is_original_product( $_GET['post'] );
    }

	private static function is_post_action_needs_redirect(){
        return ! isset( $_GET['action'] ) || ( isset( $_GET['action'] ) && ! in_array( $_GET['action'], array( 'trash', 'delete', 'untrash' ) ) );
    }

	private static function is_admin_duplicate_page_action( $pagenow ){
        return 'admin.php' === $pagenow && isset( $_GET['action'] ) && 'duplicate_product' === $_GET['action'];
    }

    public static function inf_editing_product_in_non_default_lang(){
        if( !self::$woocommerce_wpml->settings[ 'dismiss_tm_warning' ] ) {
            $url = $_SERVER['REQUEST_URI'];

            $message = '<div class="message error otgs-is-dismissible"><p>';
            $message .= sprintf(
                __('The recommended way to translate WooCommerce products is using the
                             %sWooCommerce Multilingual products translation%s page.
                             Please use this page only for translating elements that are not available in the WooCommerce Multilingual products translation table.',
                    'woocommerce-multilingual'),
                '<strong><a href="' . admin_url('admin.php?page=wpml-wcml&tab=products') . '">', '</a></strong>');
            $message .= '</p><a class="notice-dismiss" href="' . $url . '&wcml_action=dismiss_tm_warning"><span class="screen-reader-text">' . __('Dismiss', 'woocommerce-multilingual') . '</a>';
            $message .= '</div>';

            echo $message;
        }
    }

    public static function check_user_admin_access( $prevent_access ){

        if( self::$woocommerce_wpml->check_dependencies ){
            $user_lang_pairs = get_user_meta( get_current_user_id(), self::$wpdb->prefix.'language_pairs', true );
            if( current_user_can( 'wpml_manage_woocommerce_multilingual' ) || !empty( $user_lang_pairs ) ){
                return false;
            }
        }

        return $prevent_access;
    }

    public static function add_menu_warning(){
        global $submenu, $menu;

        if (
            class_exists( 'WooCommerce' ) &&
            (
                empty( self::$woocommerce_wpml->settings['set_up_wizard_run'] ) ||
                (
                    empty( self::$woocommerce_wpml->settings['set_up_wizard_run'] ) &&
                    self::$woocommerce_wpml->settings['set_up_wizard_splash']
                )
            )
        ) {
            if( isset( $submenu[ 'woocommerce' ] ) ){
                foreach( $submenu[ 'woocommerce' ] as $key => $menu_item ) {
                    if ( $menu_item[ 0 ] == __( 'WooCommerce Multilingual', 'woocommerce-multilingual' ) ) {
                        $submenu[ 'woocommerce' ][ $key ][ 0 ] .= '<span class="wcml-menu-warn"><i class="otgs-ico-warning"></i></span>';
                        break;
                    }
                }
            }

            foreach( $menu as $key => $menu_item ) {
                if ( $menu_item[ 0 ] == __( 'WooCommerce', 'woocommerce' ) ) {
                    $menu[ $key ][ 0 ] .= '<span class="wcml-menu-warn"><i class="otgs-ico-warning"></i></span>';
                    break;
                }
            }
        }
    }



}