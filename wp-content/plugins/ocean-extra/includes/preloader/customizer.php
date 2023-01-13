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
class Ocean_Preloader_Customizer {

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

        add_action( 'customize_register', array( $this, 'customizer_options' ), 15 );

        $this->active    = get_theme_mod( 'ocean_preloader_enable', false );
        $this->type      = get_theme_mod( 'ocean_preloader_type', 'default' );
        $this->icon_type = get_theme_mod( 'ocean_preloader_icon_type', 'css' );
        $this->icon      = get_theme_mod( 'ocean_preloader_default_icon', 'roller' );

        if ( $this->active ) {
            add_filter( 'ocean_head_css', array( $this, 'head_css' ), 15 );
            add_filter( 'ocean_typography_settings', array( $this, 'typography_settings' ), 15 );
            add_action( 'customize_preview_init', array( $this, 'customize_preview_js' ) );
        }
    }

    /**
	 * Binds JS handlers to make Theme Customizer preview reload changes asynchronously.
	 */
	public function customize_preview_js() {
		wp_enqueue_script(
			'preloader-customizer',
			OE_URL . 'includes/preloader/assets/js/customize-preview.min.js',
			array( 'customize-preview' ),
			OE_VERSION,
			true
		);
	}

    /**
	 * Customizer options
	 */
	public function customizer_options( $wp_customize ) {

		/**
		 * Add a new section
		 */
		$section = 'oceanwp_preloader';

		$wp_customize->add_section(
			$section,
			array(
                'title'    => esc_html__('Preloader', 'ocean-extra'),
                'priority' => 15,
                'panel'    => 'ocean_general_panel',
			)
		);

		/**
         * Enable Preloader
         */
        $wp_customize->add_setting( 'ocean_preloader_enable', array(
            'default'           	=> false,
            'sanitize_callback' 	=> 'oceanwp_sanitize_checkbox',
        ) );

        $wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'ocean_preloader_enable', array(
            'label'	   				=> esc_html__( 'Enable OceanWP Preloader', 'ocean-extra' ),
            'type' 					=> 'checkbox',
            'section'  				=> $section,
            'priority' 				=> 10,
        ) ) );

        /**
         * Preloader Type
         */
        $wp_customize->add_setting( 'ocean_preloader_type', array(
            'default'           	=> 'default',
            'sanitize_callback' 	=> 'oceanwp_sanitize_select',
        ) );

        $wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'ocean_preloader_type', array(
            'label'	   				=> esc_html__( 'Preloader Type', 'ocean-extra' ),
            'type' 					=> 'select',
            'section'  				=> $section,
            'priority' 				=> 10,
            'active_callback' 		=> 'oe_cac_has_preloader',
            'choices' 				=> array(
                'default' => esc_html__( 'Default', 'ocean-extra' ),
                'custom'  => esc_html__( 'Custom', 'ocean-extra' ),
            ),
        ) ) );

        /**
         * Preloader Icon Type
         */
        $wp_customize->add_setting( 'ocean_preloader_icon_type', array(
            'default'           	=> 'css',
            'sanitize_callback' 	=> 'oceanwp_sanitize_select',
        ) );

        $wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'ocean_preloader_icon_type', array(
            'label'	   				=> esc_html__( 'Icon Type', 'ocean-extra' ),
            'type' 					=> 'select',
            'section'  				=> $section,
            'priority' 				=> 10,
            'active_callback' 		=> 'oe_cac_has_preloader_default',
            'choices' 				=> array(
                'css'   => esc_html__( 'CSS', 'ocean-extra' ),
                'image' => esc_html__( 'Image', 'ocean-extra' ),
                'logo'  => esc_html__( 'Logo', 'ocean-extra' ),
                'svg'   => esc_html__( 'SVG', 'ocean-extra' ),
            ),
        ) ) );

        /**
         * Preloader icon
         */
        $wp_customize->add_setting( 'ocean_preloader_default_icon', array(
            'default'           	=> 'roller',
            'sanitize_callback' 	=> 'oceanwp_sanitize_select',
        ) );

        $wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'ocean_preloader_default_icon', array(
            'label'	   				=> esc_html__( 'Preloader Icon', 'ocean-extra' ),
            'type' 					=> 'select',
            'section'  				=> $section,
            'priority' 				=> 10,
            'active_callback' 		=> 'oe_cac_has_preloader_icon_css',
            'choices' 				=> array(
                'roller'        => esc_html__( 'Roller', 'ocean-extra' ),
                'circle'        => esc_html__( 'Circle', 'ocean-extra' ),
                'ring'          => esc_html__( 'Ring', 'ocean-extra' ),
                'dual-ring'     => esc_html__( 'Dual Ring', 'ocean-extra' ),
                'ripple-plain'  => esc_html__( 'Ripple Plain', 'ocean-extra' ),
                'ripple-circle' => esc_html__( 'Ripple Circle', 'ocean-extra' ),
                'heart'         => esc_html__( 'Heart', 'ocean-extra' ),
                'ellipsis'      => esc_html__( 'Ellipsis', 'ocean-extra' ),
                'spinner-line'  => esc_html__( 'Spinner Line', 'ocean-extra' ),
                'spinner-dot'   => esc_html__( 'Spinner Dot', 'ocean-extra' ),
            ),
        ) ) );

        /**
         * Image
         */
        $wp_customize->add_setting( 'ocean_preloader_icon_image', array(
            'default'           	=> '',
            'sanitize_callback' 	=> 'oceanwp_sanitize_image',
        ) );

        $wp_customize->add_control( new WP_Customize_Image_Control( $wp_customize, 'ocean_preloader_icon_image', array(
            'label'	   				=> esc_html__( 'Image', 'ocean-extra' ),
            'description'	 		=> esc_html__( 'Upload svg, gif, png, jpg.', 'ocean-extra' ),
            'section'  				=> $section,
            'priority' 				=> 10,
            'active_callback' 		=> 'oe_cac_has_preloader_icon_image',
        ) ) );

        /**
         * SVG image
         */
        $wp_customize->add_setting( 'ocean_preloader_icon_svg', array(
            'default'           	=> '',
            'sanitize_callback' 	=> 'oceanwp_sanitize_image',
        ) );

        $wp_customize->add_control( new WP_Customize_Image_Control( $wp_customize, 'ocean_preloader_icon_svg', array(
            'label'	   				=> esc_html__( 'Upload SVG', 'ocean-extra' ),
            'description'	 		=> esc_html__( 'Upload svg file here', 'ocean-extra' ),
            'section'  				=> $section,
            'priority' 				=> 10,
            'active_callback' 		=> 'oe_cac_has_preloader_icon_svg',
        ) ) );

        /**
		 * Image size
		 */
		$wp_customize->add_setting(
			'ocean_preloader_image_size',
			array(
                'transport'         => 'postMessage',
				'default'           => '100',
				'sanitize_callback' => false,
			)
		);

		$wp_customize->add_control(
			new OceanWP_Customizer_Range_Control(
				$wp_customize,
				'ocean_preloader_image_size',
				array(
					'label'           => esc_html__( 'Size (px)', 'ocean-extra' ),
					'section'         => $section,
					'priority'        => 10,
                    'active_callback' => 'oe_cac_has_not_preloader_icon_css',
					'input_attrs' => array(
						'min'  => 0,
						'max'  => 500,
						'step' => 1,
					),
				)
			)
		);

        /**
         * Custom content.
         */
        $wp_customize->add_setting( 'ocean_preloader_content', array(
            'default'           	=> 'Site is Loading, Please wait...',
            'sanitize_callback' 	=> 'wp_kses_post',
        ) );

        $wp_customize->add_control( 'ocean_preloader_content', array(
            'label'                 => esc_html__( 'Content', 'ocean-extra' ),
            'type'                  => 'textarea',
            'section'  				=> $section,
            'priority' 				=> 10,
            'active_callback' 		=> 'oe_cac_has_preloader_default',
        ) );

        /**
         * Container Size
         */
        $wp_customize->add_setting( 'ocean_preloader_container_width', array(
            'transport' 			=> 'postMessage',
            'sanitize_callback' 	=> 'oceanwp_sanitize_number_blank',
        ) );

        $wp_customize->add_setting( 'ocean_preloader_container_width_tablet', array(
            'transport' 			=> 'postMessage',
            'sanitize_callback' 	=> 'oceanwp_sanitize_number_blank',
        ) );

        $wp_customize->add_setting( 'ocean_preloader_container_width_mobile', array(
            'transport' 			=> 'postMessage',
            'sanitize_callback' 	=> 'oceanwp_sanitize_number_blank',
        ) );

        $wp_customize->add_control( new OceanWP_Customizer_Slider_Control( $wp_customize, 'ocean_preloader_container_width', array(
            'label' 			=> esc_html__( 'Container Width (px)', 'ocean-extra' ),
            'description'	    => esc_html__( 'Set "0" to unset or set your custom container width.', 'ocean-extra' ),
            'section'  			=> $section,
            'settings' => array(
                'desktop' 	=> 'ocean_preloader_container_width',
                'tablet' 	=> 'ocean_preloader_container_width_tablet',
                'mobile' 	=> 'ocean_preloader_container_width_mobile',
            ),
            'priority' 				=> 10,
            'active_callback' 		=> 'oe_cac_has_preloader_default',
            'input_attrs' 			=> array(
                'min'   => 0,
                'max'   => 2000,
                'step'  => 1,
            ),
        ) ) );

        /**
         * Preloader template
         */
        $wp_customize->add_setting( 'ocean_preloader_template', array(
            'default'           	=> '0',
            'sanitize_callback' 	=> 'oceanwp_sanitize_select',
        ) );

        $wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'ocean_preloader_template', array(
            'label'	   				=> esc_html__( 'Select Template', 'ocean-extra' ),
            'type' 					=> 'select',
            'section'  				=> $section,
            'priority' 				=> 10,
            'active_callback' 		=> 'oe_cac_has_preloader_custom',
            'choices' 				=> oe_library_template_selector( 'library' ),
        ) ) );

        /**
         * Dealing with Elementor FOUC issue - Experimental
         */
        $wp_customize->add_setting( 'ocean_preloader_elementor_fouc', array(
            'default'           	=> true,
            'sanitize_callback' 	=> 'oceanwp_sanitize_checkbox',
        ) );

        $wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'ocean_preloader_elementor_fouc', array(
            'label'	   				=> esc_html__( 'Elementor Flickers/FOUC', 'ocean-extra' ),
            'description'	 		=> esc_html__( 'Experimental (beta) feature which could potentially help resolve Elementor flicker / FOUC issues. No guarantee on resolution at this point.', 'ocean-extra' ),
            'type' 					=> 'checkbox',
            'section'  				=> $section,
            'priority' 				=> 10,
            'active_callback' 		=> 'oe_cac_has_preloader_custom',
        ) ) );

        /**
         * Overlay color
         */
        $wp_customize->add_setting( 'ocean_preloader_overlay_color', array(
            'default' 				=> '#000000',
            'transport' 			=> 'postMessage',
            'sanitize_callback' 	=> 'oceanwp_sanitize_color',
        ) );

        $wp_customize->add_control( new OceanWP_Customizer_Color_Control( $wp_customize, 'ocean_preloader_overlay_color', array(
            'label'	   				=> esc_html__( 'Overlay Color', 'ocean-extra' ),
            'section'  				=> $section,
            'priority' 				=> 10,
            'active_callback' 		=> 'oe_cac_has_preloader',
        ) ) );

        /**
         * Icon color
         */
        $wp_customize->add_setting( 'ocean_preloader_icon_color', array(
            'default' 				=> '#fff',
            'transport' 			=> 'postMessage',
            'sanitize_callback' 	=> 'oceanwp_sanitize_color',
        ) );

        $wp_customize->add_control( new OceanWP_Customizer_Color_Control( $wp_customize, 'ocean_preloader_icon_color', array(
            'label'	   				=> esc_html__( 'Icon Color', 'ocean-extra' ),
            'section'  				=> $section,
            'priority' 				=> 10,
            'active_callback' 		=> 'oe_cac_has_preloader_icon_css',
        ) ) );
    }

    /**
     * Add typography options
     */
    public function typography_settings( $settings ) {
        $settings['preloader_after_content'] = array(
            'label'    => esc_html__( 'Preloader Content', 'ocean-extra' ),
            'target'   => '.ocean-preloader--active .preloader-after-content',
            'defaults' => array(
                'font-size'      => '20',
                'color'          => '#333333',
                'line-height'    => '1.8',
                'letter-spacing' => '0.6',
            ),
            'active_callback' => 'oe_cac_has_preloader',
        );

        return $settings;
    }

    /**
	 * Get CSS
	 *
	 * @param obj $output CSS Output.
	 */
	public function head_css( $output ) {

        $container_width          = get_theme_mod( 'ocean_preloader_container_width' );
        $container_width_tablet   = get_theme_mod( 'ocean_preloader_container_width_tablet' );
        $container_width_mobile   = get_theme_mod( 'ocean_preloader_container_width_mobile' );
		$overlay_color            = get_theme_mod( 'ocean_preloader_overlay_color', '#000000' );
		$icon_color               = get_theme_mod( 'ocean_preloader_icon_color', '#fff' );
		$image_size               = get_theme_mod( 'ocean_preloader_image_size', '100' );

		$css = '';

		if ( ! empty( $overlay_color ) && '#000000' !== $overlay_color ) {
			$css .= '.ocean-preloader--active #ocean-preloader{background-color:' . $overlay_color . ';}';
		}

        if ( 'default' === $this->type ) {

            if ( ! empty( $container_width ) ) {
                $css .= '.ocean-preloader--active .preloader-inner{width:' . $container_width . 'px;}';
            }
            if ( ! empty( $container_width_tablet ) ) {
                $css .= '@media (max-width: 768px){.ocean-preloader--active .preloader-inner{width:' . $container_width_tablet . 'px;}}';
            }
            if ( ! empty( $container_width_mobile ) ) {
                $css .= '@media (max-width: 480px){.ocean-preloader--active .preloader-inner{width:' . $container_width_mobile . 'px;}}';
            }


            if ( 'css' === $this->icon_type ) {
                if ( ! empty( $icon_color ) && '#fff' !== $icon_color ) {
                    if ( 'roller' === $this->icon ) {
                        $css .= '.ocean-preloader--active .preloader-roller div:after{background:' . $icon_color . ';}';
                    }
                    if ( 'circle' === $this->icon ) {
                        $css .= '.ocean-preloader--active .preloader-circle > div{background:' . $icon_color . ';}';
                    }
                    if ( 'ripple-plain' === $this->icon ) {
                        $css .= '.ocean-preloader--active .preloader-ripple-plain div{background:' . $icon_color . ';}';
                    }
                    if ( 'ripple-circle' === $this->icon ) {
                        $css .= '.ocean-preloader--active .preloader-ripple-circle div{border-color:' . $icon_color . ';}';
                    }
                    if ( 'ring' === $this->icon ) {
                        $css .= '.ocean-preloader--active .preloader-ring div{border-top-color:' . $icon_color . ';}';
                    }
                    if ( 'dual-ring' === $this->icon ) {
                        $css .= '.ocean-preloader--active .preloader-dual-ring:after{border-top-color:' . $icon_color . ';}';
                        $css .= '.ocean-preloader--active .preloader-dual-ring:after{border-bottom-color:' . $icon_color . ';}';
                    }
                    if ( 'heart' === $this->icon ) {
                        $css .= '.ocean-preloader--active .preloader-heart div, .ocean-preloader--active .preloader-heart div::after, .ocean-preloader--active .preloader-heart div::before{background:' . $icon_color . ';}';
                    }
                    if ( 'ellipsis' === $this->icon ) {
                        $css .= '.ocean-preloader--active .preloader-ellipsis div{background:' . $icon_color . ';}';
                    }
                    if ( 'spinner-dot' === $this->icon ) {
                        $css .= '.ocean-preloader--active .preloader-spinner-dot div{background:' . $icon_color . ';}';
                    }
                    if ( 'spinner-line' === $this->icon ) {
                        $css .= '.ocean-preloader--active .preloader-spinner-line div:after{background:' . $icon_color . ';}';
                    }
                }
            }

            if ( ! empty( $image_size ) && '100' !== $image_size ) {
                if ( 'image' === $this->icon_type ) {
                    $css .= '.ocean-preloader--active .preloader-image {max-width:' . $image_size . 'px;}';
                }
                if ( 'logo' === $this->icon_type ) {
                    $css .= '.ocean-preloader--active .preloader-logo {max-width:' . $image_size . 'px;}';
                }
                if ( 'svg' === $this->icon_type ) {
                    $css .= '.ocean-preloader--active .preloader-svg svg {width:' . $image_size . 'px; height:' . $image_size . 'px}';
                }
            }
        }



		// Return CSS.
		if ( ! empty( $css ) ) {
			$output .= '/* OceanWP Preloader CSS */' . $css;
		}

		// Return output css.
		return $output;
	}



}

new Ocean_Preloader_Customizer();