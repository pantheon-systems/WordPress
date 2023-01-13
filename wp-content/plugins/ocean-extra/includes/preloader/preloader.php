<?php
/**
 * Preloader
 *
 * @package Ocean_Extra
 * @category Core
 * @author OceanWP
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Start Class
class Ocean_Preloader {

    /**
	 * Status
	 */
    public $active = false;

    /**
	 * Type
	 */
    public $type = 'default';

    /**
     * Icon Type
     */
    public $icon_type = 'css';

    /**
     * Icon
     */
    public $icon = 'roller';

    /**
     * Elementor template id
     */
    public $template_id = '';

	/**
	 * Initialize
	 */
	public function __construct() {

        $this->preloader_setup();

        $this->active      = get_theme_mod( 'ocean_preloader_enable', false );
        $this->type        = get_theme_mod( 'ocean_preloader_type', 'default' );
        $this->icon_type   = get_theme_mod( 'ocean_preloader_icon_type', 'css' );
        $this->icon        = get_theme_mod( 'ocean_preloader_default_icon', 'roller' );
        $this->template_id = get_theme_mod( 'ocean_preloader_template' );

        if ( $this->active ) {
            add_filter( 'body_class', array( $this, 'preloader_body_class' ) );
            add_action( 'wp_head', array( $this, 'preloader_view' ), 1000 );
            add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_assets' ) );
            add_action( 'ocean_preloader', array( $this, 'render_preloader' ) );
        }
    }

    /**
     * Preloader setup
     */
    public function preloader_setup() {

        require_once OE_PATH . '/includes/preloader/helper.php';
        require_once OE_PATH . '/includes/preloader/customizer.php';
    }

    public function enqueue_assets() {

        wp_enqueue_style(
            'owp-preloader',
            OE_URL . 'includes/preloader/assets/css/preloader.min.css',
            array(),
            OE_VERSION
        );

        if ( 'default' === $this->type && 'css' === $this->icon_type && $this->icon ) {
            wp_enqueue_style(
                'owp-preloader-icon',
                OE_URL . 'includes/preloader/assets/css/styles/' . $this->icon . '.css',
                array(),
                OE_VERSION
            );
        }

        if ( is_customize_preview() ) {
            wp_enqueue_script(
                'owp-preloader',
                OE_URL . 'includes/preloader/assets/js/preloader.min.js',
                array( 'jquery' ),
                OE_VERSION,
                false
            );

            wp_localize_script(
                'owp-preloader',
                'owpPreloader',
                array(
                    'nonce' => wp_create_nonce( 'oceanwp_preloader' ),
                )
            );
        } else {
            wp_enqueue_script(
                'owp-preloader',
                OE_URL . 'includes/preloader/assets/js/preloader.min.js',
                array( 'jquery' ),
                OE_VERSION,
                false
            );

            wp_localize_script(
                'owp-preloader',
                'owpPreloader',
                array(
                    'nonce' => wp_create_nonce( 'oceanwp_preloader' ),
                )
            );
        }

        // Check if page is Elementor page.
        $elementor = get_post_meta( $this->template_id, '_elementor_edit_mode', true );

        // Elementor css load
        if ( true === get_theme_mod( 'ocean_preloader_elementor_fouc', true ) && $elementor ) {
            if ( ! class_exists( '\Elementor\Core\Files\CSS\Post' ) ) {
                return;
            }
            $css_file = new \Elementor\Core\Files\CSS\Post( $this->template_id );
            $css_file->enqueue();
        }
    }

    /**
     * Preloader view
     */
    public function preloader_body_class( $classes ) {
        $classes[] = 'ocean-preloader--active';

        return $classes;
    }

    /**
     * Preloader view
     */
    public function preloader_view() {
        ob_start();
        ?>
        <div id="ocean-preloader">
            <?php do_action( 'ocean_preloader_before' ); ?>
            <?php do_action( 'ocean_preloader' ); ?>
            <?php do_action( 'ocean_preloader_after' ); ?>
        </div>
        <?php
        $content = ob_get_clean();

        echo $content;
    }

    /**
     * Render preloader content
     */
    public function render_preloader() {

        $image    = get_theme_mod( 'ocean_preloader_icon_image', '' );
        $svg_code = '';
        $svg_path = get_theme_mod( 'ocean_preloader_icon_svg', '' );
        if ( $svg_path ) {
            $svg_code = file_get_contents( $svg_path );
        }

        $content = get_theme_mod( 'ocean_preloader_content', 'Site is Loading, Please wait...' );

        // Check if page is Elementor page.
        $elementor = get_post_meta( $this->template_id, '_elementor_edit_mode', true );

        // Get content
        if ( ! empty( $this->template_id ) ) {

            $post_data = get_post( $this->template_id );

            if ( $post_data && ! is_wp_error( $post_data ) ) {
                $get_content = $post_data->post_content;
            }

        }

        if ( 'default' === $this->type ) { ?>
            <div class="preloader-content">
                <div class="preloader-inner">
                    <?php
                    if ( 'css' === $this->icon_type ) :
                        ?>
                        <div class="preloader-icon">
                            <?php echo wp_kses_post( oe_preloader_icon( $this->icon ) ); ?>
                        </div>
                    <?php endif; ?>

                    <?php if ( 'image' === $this->icon_type ) :
                        ?>
                        <div class="preloader-image">
                            <?php oe_preloader_image(); ?>
                        </div>
                    <?php endif; ?>

                    <?php if ( 'logo' === $this->icon_type ) :
                        ?>
                        <div class="preloader-logo">
                            <?php the_custom_logo(); ?>
                        </div>
                    <?php endif; ?>

                    <?php if ( 'svg' === $this->icon_type ) :
                        ?>
                        <div class="preloader-svg">
                            <?php echo $svg_code; ?>
                        </div>
                    <?php endif; ?>

                    <?php if ( ! empty( $content ) ) :
                        ?>
                        <div class="preloader-after-content">
                            <?php echo wp_kses_post( $content ); ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        <?php
        }

        if ( 'custom' === $this->type ) {

            if ( class_exists( 'Elementor\Plugin' ) && $elementor ) {

                echo Elementor\Plugin::instance()->frontend->get_builder_content_for_display( $this->template_id );

            }

            // If Beaver Builder
            else if ( class_exists( 'FLBuilder' ) && ! empty( $this->template_id ) ) {

                echo do_shortcode( '[fl_builder_insert_layout id="' . $this->template_id . '"]' );

            }

            // Else
            else {

                // If Gutenberg.
                if ( ocean_is_block_template( $this->template_id ) ) {
                    $get_content = apply_filters( 'ocean_preloader_template_content', do_blocks( $get_content ) );
                }

                // Display template content.
                echo do_shortcode( $get_content );

            }

        }
    }

}

new Ocean_Preloader();