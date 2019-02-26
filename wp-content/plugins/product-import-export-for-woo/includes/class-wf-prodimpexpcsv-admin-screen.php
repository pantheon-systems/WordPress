<?php
if (!defined('ABSPATH')) {
    exit;
}

class WF_ProdImpExpCsv_Admin_Screen {

    /**
     * Constructor
     */
    public function __construct() {

        add_action('admin_menu', array($this, 'admin_menu'));
        add_action('admin_print_styles', array($this, 'admin_scripts'));
        add_action('admin_notices', array($this, 'admin_notices'));

        add_action('in_admin_header', array($this, 'render_review_text'), 1);
        add_action('wp_ajax_review_rated', array($this, 'review_rated'));
    }

    /**
     * Notices in admin
     */
    public function admin_notices() {
        if (!function_exists('mb_detect_encoding')) {
            echo '<div class="error"><p>' . __('Product CSV Import Export requires the function <code>mb_detect_encoding</code> to import and export CSV files. Please ask your hosting provider to enable this function.', 'wf_csv_import_export') . '</p></div>';
        }
    }

    /**
     * Admin Menu
     */
    public function admin_menu() {
        $page1 = add_submenu_page('edit.php?post_type=product', __('Product Im-Ex', 'wf_csv_import_export'), __('Product Im-Ex', 'wf_csv_import_export'), apply_filters('woocommerce_csv_product_role', 'manage_woocommerce'), 'wf_woocommerce_csv_im_ex', array($this, 'output'));
        $page = add_submenu_page('woocommerce', __('Product Import-Export', 'wf_csv_import_export'), __('Product Import-Export', 'wf_csv_import_export'), apply_filters('woocommerce_csv_product_role', 'manage_woocommerce'), 'wf_woocommerce_csv_im_ex', array($this, 'output'));
        
    }

    /**
     * Admin Scripts
     */
    public function admin_scripts() {
        wp_enqueue_style('woocommerce_admin_styles', WC()->plugin_url() . '/assets/css/admin.css');
        wp_enqueue_style('woocommerce-product-csv-importer', plugins_url(basename(plugin_dir_path(WF_ProdImpExpCsv_FILE)) . '/styles/wf-style.css', basename(__FILE__)), '', '1.4.4', 'screen');
    }

    /**
     * Admin Screen output
     */
    public function output() {

        $tab = '';
        if (!empty($_GET['tab'])) {
            if ($_GET['tab'] == 'help') {
                $tab = 'help';
            }
        }

        include( 'views/html-wf-admin-screen.php' );
    }

    /**
     * Admin Page for exporting
     */
    public function admin_export_page() {
        $post_columns = include( 'exporter/data/data-wf-post-columns.php' );
        include( 'views/export/html-wf-export-products.php' );
    }

    /**
     * Admin Page for help
     */
    public function admin_help_page() {
        include('views/html-wf-help-guide.php');
    }

    /**
     * Change the admin header text on WooCommerce admin pages.
     *
     * @since  1.4.3
     * @param  string $header_text
     * @return string
     */
    public function render_review_text($header_text) {

        if (strtotime(get_option('xa_pipe_plugin_installed_date')) < strtotime('-7 days')) {

            if (!current_user_can('manage_woocommerce') || !function_exists('wc_get_screen_ids')) {
                return $header_text;
            }

            $current_screen = get_current_screen();

            $xa_pages = array('product_page_wf_woocommerce_csv_im_ex');

            if (isset($current_screen->id) && apply_filters('xa_display_admin_footer_text', in_array($current_screen->id, $xa_pages))) {

                if (!get_option('xa_pipe_plugin_review_rated')) {
                    $header_text = sprintf(
                            __('<div class="updated"><p>You have been using %1$s for a while. If you like the plugin please leave us a %2$s review!<p></div>', 'wf_csv_import_export'), sprintf('<strong>%s</strong>', esc_html__('Product Import Export for WooCommerce', 'wf_csv_import_export')), '<a href="https://wordpress.org/support/plugin/product-import-export-for-woo/reviews?rate=5#new-post" target="_blank" class="xa-pipe-rating-link" data-reviewed="' . esc_attr__('Thanks for the review.', 'wf_csv_import_export') . '">&#9733;&#9733;&#9733;&#9733;&#9733;</a>'
                    );
                    wc_enqueue_js("
					jQuery( 'a.xa-pipe-rating-link' ).click( function() {
						jQuery.post( '" . admin_url("admin-ajax.php") . "', { action: 'review_rated' } );
						jQuery( this ).parent().text( jQuery( this ).data( 'reviewed' ) );
					});
				");
                } else {
                    $header_text = '';
                }
            }

            echo $header_text;
        }
    }

    /**
     * Fired when clicking the rating header.
     */
    public function review_rated() {

        if (!current_user_can('manage_woocommerce')) {
            wp_die(-1);
        }
        update_option('xa_pipe_plugin_review_rated', 1);
        wp_die();
    }

}

new WF_ProdImpExpCsv_Admin_Screen();