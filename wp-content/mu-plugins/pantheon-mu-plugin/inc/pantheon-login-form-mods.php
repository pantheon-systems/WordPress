<?php
/**
 * Modify the WordPress login form for Pantheon
 */

/**
 * Should we proceed with adding the return to Pantheon button?
 *
 * Only if we are on a Pantheon subdomain
 */
$show_return_to_pantheon_button = apply_filters( 'show_return_to_pantheon_button', (
    (
        false !== stripos( get_site_url(), 'pantheonsite.io') ||
        ( isset( $_SERVER['HTTP_HOST'] ) && false !== stripos( $_SERVER['HTTP_HOST'], 'pantheonsite.io') )
    )
) );

if( $show_return_to_pantheon_button ){

    /**
     * Enqueue Pantheon login styles
     *
     * @return void
     */
    function Pantheon_Enqueue_Login_style()
    {
        wp_enqueue_style('pantheon-login-mods', plugin_dir_url(__FILE__) . 'assets/css/return-to-pantheon-button.css', false); 
    }

    add_action('login_enqueue_scripts', 'Pantheon_Enqueue_Login_style', 10);

    /**
     * Enqueue Pantheon login scripts
     *
     * @return void
     */
    function Pantheon_Enqueue_Login_script()
    {
        wp_enqueue_script('pantheon-login-mods', plugin_dir_url(__FILE__) . 'assets/js/return-to-pantheon-button.js', array('jquery'), false, true);
    }

    add_action('login_enqueue_scripts', 'Pantheon_Enqueue_Login_script', 1);

    /**
     * Print return to Pantheon link HTML
     *
     * @return void
     */
    function Return_To_Pantheon_Button_HTML()
    {
        $pantheon_dashboard_url = 'https://dashboard.pantheon.io/sites/' . $_ENV['PANTHEON_SITE'] . '#' . $_ENV['PANTHEON_ENVIRONMENT'];

        $pantheon_fist_icon_url = plugin_dir_url(__FILE__) . 'assets/images/pantheon-fist-icon-black.svg';
        $login_message = apply_filters( 'pantheon_wp_login_text', __('Login to your WordPress Site', 'pantheon') );
        ?>
        <div id="return-to-pantheon" style="display: none;">
            <div class="left">
                    <?php echo $login_message; ?>
            </div>
            <div class="right">
                <a href="<?php echo esc_url( $pantheon_dashboard_url ); ?>">
                    <img class="fist-icon"  src="<?php echo esc_url( $pantheon_fist_icon_url ); ?>">
                    <?php _e('Return to Pantheon', 'pantheon'); ?>
                </a>
            </div>
        </div>
        <?php
    }

    add_action('login_header', 'Return_To_Pantheon_Button_HTML', 10);

}
