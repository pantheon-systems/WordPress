<?php
if (!defined('ABSPATH')) {
    exit;
}

Class WizardAjax {

    public function __construct() {
        add_action('wp_ajax_owp_wizard_ajax_get_demo_data', array($this, 'ajax_demo_data'));
    }

    public function ajax_demo_data() {


        if (!wp_verify_nonce($_GET['demo_data_nonce'], 'get-demo-data')) {
            die('This action was stopped for security purposes.');
        }

        // Database reset url
        if (is_plugin_active('wordpress-database-reset/wp-reset.php')) {
            $plugin_link = admin_url('tools.php?page=database-reset');
        } else {
            $plugin_link = admin_url('plugin-install.php?s=Wordpress+Database+Reset&tab=search');
        }

        // Get all demos
        $demos = OceanWP_Demos::get_demos_data();

        // Get selected demo
        $demo = $_GET['demo_name'];

        // Get required plugins
        $plugins = $demos[$demo]['required_plugins'];

        // Get free plugins
        $free = $plugins['free'];

        // Get premium plugins
        $premium = $plugins['premium'];
        ?>

        <div id="owp-demo-plugins">

            <h2 class="title"><?php echo sprintf(esc_html__('Import the %1$s demo', 'ocean-extra'), esc_attr($demo)); ?></h2>

            <div class="owp-popup-text">

                <p><?php
                    echo
                    sprintf(
                            esc_html__('Importing demo data allow you to quickly edit everything instead of creating content from scratch. It is recommended uploading sample data on a fresh WordPress install to prevent conflicts with your current content. You can use this plugin to reset your site if needed: %1$sWordpress Database Reset%2$s.', 'ocean-extra'), '<a href="' . $plugin_link . '" target="_blank">', '</a>'
                    );
                    ?></p>

                <div class="owp-required-plugins-wrap">
                    <h3><?php esc_html_e('Required Plugins', 'ocean-extra'); ?></h3>
                    <p><?php esc_html_e('For your site to look exactly like this demo, the plugins below need to be activated.', 'ocean-extra'); ?></p>
                    <div class="owp-required-plugins oe-plugin-installer">
                        <?php
                        OceanWP_Demos::required_plugins($free, 'free');
                        OceanWP_Demos::required_plugins($premium, 'premium');
                        ?>
                    </div>
                </div>

            </div>


        </div>

        <form method="post" id="owp-demo-import-form">

            <input id="owp_import_demo" type="hidden" name="owp_import_demo" value="<?php echo esc_attr($demo); ?>" />

            <div class="owp-demo-import-form-types">

                <h2 class="title"><?php esc_html_e('Select what you want to import:', 'ocean-extra'); ?></h2>

                <ul class="owp-popup-text">
                    <li>
                        <label for="owp_import_xml">
                            <input id="owp_import_xml" type="checkbox" name="owp_import_xml" checked="checked" />
                            <strong><?php esc_html_e('Import XML Data', 'ocean-extra'); ?></strong> (<?php esc_html_e('pages, posts, images, menus, etc...', 'ocean-extra'); ?>)
                        </label>
                    </li>

                    <li>
                        <label for="owp_theme_settings">
                            <input id="owp_theme_settings" type="checkbox" name="owp_theme_settings" checked="checked" />
                            <strong><?php esc_html_e('Import Customizer Settings', 'ocean-extra'); ?></strong>
                        </label>
                    </li>

                    <li>
                        <label for="owp_import_widgets">
                            <input id="owp_import_widgets" type="checkbox" name="owp_import_widgets" checked="checked" />
                            <strong><?php esc_html_e('Import Widgets', 'ocean-extra'); ?></strong>
                        </label>
                    </li>
                </ul>

            </div>

            <?php wp_nonce_field('owp_import_demo_data_nonce', 'owp_import_demo_data_nonce'); ?>
            <input type="submit" name="submit" class="owp-button owp-import" value="<?php esc_html_e('Import', 'ocean-extra'); ?>"  />

        </form>

        <div class="owp-loader">
            <h2 class="title"><?php esc_html_e('The import process could take some time, please be patient', 'ocean-extra'); ?></h2>
            <div class="owp-import-status owp-popup-text"></div>
        </div>

        <div class="owp-last">
            <svg class="checkmark" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 52 52"><circle class="checkmark-circle" cx="26" cy="26" r="25" fill="none"></circle><path class="checkmark-check" fill="none" d="M14.1 27.2l7.1 7.2 16.7-16.8"></path></svg>
            <h3><?php esc_html_e('Demo Imported!', 'ocean-extra'); ?></h3>
        </div>
        <div class="owp-error" style="display: none;">
                <p ><?php esc_html_e("The import didn't import well please contact the support.", 'ocean-extra'); ?></p>
            </div>
        </div>


        <?php
        die();
    }

}

new WizardAjax();
