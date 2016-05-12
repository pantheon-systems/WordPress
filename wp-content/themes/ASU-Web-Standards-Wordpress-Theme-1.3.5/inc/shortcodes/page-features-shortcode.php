<?php
/**
 * Page Feature Shortcode used for simplifying Bootstrap code
 *
 * @author Global Insititue of Sustainability
 * @author Ivan Montiel
 *
 * @package asu-wordpress-web-standards
 */
if ( ! function_exists( 'page_feature' ) ) :
  /**
   * Display hero section (page feature)
   */
  function page_feature() {
    // TODO migrate this to a config file of some sort
    $supported_colors = [
      'black'  => '#000000',
      'white'  => '#ffffff',
      'gold'   => '#ffb310',
      'maroon' => '#990033'
    ];

    $custom_fields = get_post_custom();
    $title         = null;
    $alt           = '';
    $count         = null;
    $image         = null;
    $video         = null;
    $description   = null;
    $color         = false;
    $type          = 'standard';
    $hide_on_small = false;

    // If is blog page, check for theme customizer options
    if ( is_home() ) {
      if ( is_array( get_option( 'wordpress_asu_theme_options' ) ) ) {
        $c_options = get_option( 'wordpress_asu_theme_options' );

        // Do we have a blog_header_image?
        if ( isset( $c_options ) &&
             array_key_exists( 'blog_image', $c_options ) &&
             $c_options['blog_image'] !== '' ) {
          $image = $c_options['blog_image'];
        }

        // Do we have a title?
        if ( isset( $c_options ) &&
             array_key_exists( 'blog_title', $c_options ) &&
             $c_options['blog_title'] !== '' ) {
          $title = $c_options['blog_title'];
          $alt   = htmlentities( strip_tags( $title ) );
        }

        // Do we have a description?
        if ( isset( $c_options ) &&
             array_key_exists( 'blog_description', $c_options ) &&
             $c_options['blog_description'] !== '' ) {
          $description = $c_options['blog_description'];
        }

        // Do we have a type?
        if ( isset( $c_options ) &&
             array_key_exists( 'blog_type', $c_options ) &&
             $c_options['blog_type'] !== '' ) {
          $type = $c_options['blog_type'];
        }

        // Do we have a color?
        if ( isset( $c_options ) &&
             array_key_exists( 'blog_color', $c_options ) &&
             $c_options['blog_color'] !== '' ) {
          $color = $c_options['blog_color'];

          // Filter the color, only the approved colors are allowed
          // Default to white
          if ( array_key_exists( $color, $supported_colors ) ) {
            $color = $supported_colors[ $color ];
          } else {
            $color = $support_colors['white'];
          }
        }
      }
    }

    if ( $custom_fields ) {
      if ( array_key_exists( 'page_feature_title', $custom_fields ) ) {
        $title = $custom_fields['page_feature_title'][0];
      }

      if ( array_key_exists( 'page_feature_image', $custom_fields ) ) {
        $count = count( $custom_fields['page_feature_image'] );

        if ( 0 == $count ) {
          $image = $custom_fields['page_feature_image'][0];
        } else {
          $index = rand( 0, $count - 1 );
          $image = $custom_fields['page_feature_image'][ $index ];
        }
      }

      if ( array_key_exists( 'page_feature_video', $custom_fields ) ) {
        $video = [];

        foreach ( $custom_fields['page_feature_video'] as $_ => $value ) {
          $video[] = $value;
        }
      }

      if ( array_key_exists( 'page_feature_description', $custom_fields ) ) {
        $description = $custom_fields['page_feature_description'][0];
      }

      if ( array_key_exists( 'page_feature_type', $custom_fields ) ) {
        $type = $custom_fields['page_feature_type'][0];
      }

      if ( array_key_exists( 'page_feature_color', $custom_fields ) ) {
        $color = $custom_fields['page_feature_color'][0];

        if ( $color ) {
          // Filter the color, only the approved colors are allowed
          // Default to white
          if ( array_key_exists( $color, $supported_colors ) ) {
            $color = $supported_colors[ $color ];
          } else {
            $color = $support_colors['white'];
          }
        }
      }

      if ( array_key_exists( 'page_feature_image_alt', $custom_fields ) ) {
        $alt = $custom_fields['page_feature_image_alt'][0];
      } else {
        $alt = htmlentities( strip_tags( $title ) );
      }

      if ( array_key_exists( 'page_feature_hide_on_small', $custom_fields ) ) {
        $hide_on_small = $custom_fields['page_feature_hide_on_small'][0];

        if ( 'true' === $hide_on_small ) {
          $hide_on_small = true;
        }
      }
    }

    // Check to see if anyone has overriden the page feature

    $override = apply_filters(
        'page_feature',
        array(
          'title' => $title,
          'description' => $description,
          'image' => $image,
          'video' => $video,
          'type' => $type,
          'color' => $color,
          'hide_on_small' => $hide_on_small,
          'alt' => $alt,
        )
    );

    if ( has_filter( 'page_feature' ) && $override ) {
      return $override;
    }

    // =====================
    // Standard Page Feature
    // =====================

    if ( isset( $title ) ||
       isset( $image ) ||
       isset( $video ) ||
       isset( $description ) ) {
      $html  = '<div class="column">';
      $html .= '  <div class="region region-content">';
      $html .= '    <div class="block block-system">';
      $html .= '      <div class="content">';
      $html .= '        <div class="panel-display clearfix">';

      // Section
      $section_start = '<section class="hero hero-bg-img hero-action-call %2$s" style="%1$s">';

      if ( isset( $video ) ) {
        $section_start = sprintf( $section_start, '%1$s', 'hero-video' );
      } else {
        $section_start = sprintf( $section_start, '%1$s', '' );
      }

      if ( isset( $image ) ) {
        $section_start = sprintf( $section_start, 'background-image:url(' . $image . ')' );
      } else {
        $section_start = sprintf( $section_start, '' );
      }

      $html .= $section_start;

      if ( isset( $video ) ) {
        $video_container = '<video width="100%2$s" height="auto" autoplay muted="true" loop>%1$s</video>';
        $$video_part     = '<source src="%1$s" type="%2$s"/>';
        $parts           = '';

        foreach ( $video as $_ => $value ) {
          $info     = pathinfo( $value );
          $ext      = $info['extension'];
          $mimeType = isset( $mimeTypes[ $ext ] ) ? $mimeTypes[ $ext ] : 'application/octet-stream';

          $parts .= sprintf( $$video_part, $value, $mimeType );
        }

        $html .= sprintf( $video_container, $parts, '%' );
      }

      $html .= '           <div class="container">';
      $html .= '             <div class="row">';
      $html .= '               <div class="fdt-home-container fdt-home-column-content clearfix panel-panel row-fluid container">';
      $html .= '                 <div class="fdt-home-column-content-region fdt-home-row panel-panel span12">';
      $html .= '                   <div class="panel-pane pane-fieldable-panels-pane pane-fpid-12 pane-bundle-text">';

      if ( isset( $title ) ) {
        $html .= '<h1 class="pane-title" style="color:' . $color . '">';
        $html .= $title;
        $html .= '</h1>';
      }

      if ( isset( $description ) ) {
        $html .= '<div class="pane-content">';
        $html .= '  <div class="fieldable-panels-pane">';
        $html .= '    <div class="field field-name-field-basic-text-text field-type-text-long field-label-hidden">';
        $html .= '      <div class="field-items">';
        $html .= '        <div class="field-item even" style="color:' . $color . '">';
        $html .= '          <p>';
        $html .= $description;
        $html .= '          </p>';
        $html .= '        </div>';
        $html .= '      </div>';
        $html .= '    </div>';
        $html .= '  </div>';
        $html .= '</div>';
      }

      $html .= '                   </div>';
      $html .= '                 </div>';
      $html .= '               </div>';
      $html .= '             </div>';
      $html .= '           </div>';
      $html .= '         </section>';
      $html .= '        </div>';
      $html .= '      </div>';
      $html .= '    </div>';
      $html .= '  </div>';
      $html .= '</div>';

      return $html;
    }

    return '';
  }
  add_shortcode( 'page_feature', 'page_feature' );
endif;


if ( ! function_exists( 'page_feature_ratio_slim' ) ) :
  function page_feature_ratio_slim( $options ) {
    $title         = $options['title'];
    $image         = $options['image'];
    $description   = $options['description'];
    $type          = $options['type'];
    $color         = $options['color'];
    $hide_on_small = $options['hide_on_small'];
    $alt           = $options['alt'];

    $additional_classes = ' ';

    if ( $hide_on_small ) {
      $additional_classes .= ' hide-on-small ';
    }

    if ( ( isset( $title ) ||
         isset( $image ) ||
         isset( $description ) ) &&
         ( 'ratio' === $type || 'slim' === $type ) ) {
      $html  = '<div class="column">';
      $html .= '  <div class="region region-content">';
      $html .= '    <div class="block block-system">';
      $html .= '      <div class="content">';
      $html .= '        <div class="panel-display clearfix">';

      if ( 'ratio' === $type ) {
        // =====
        // Ratio
        // =====
        $html     .= '<section class="hero-ratio ' . $additional_classes . '">';
        $image_tag = '<img src="%s" class="image-hero" alt="' . $alt . '" />';

        if ( isset( $image ) ) {
          $html .= sprintf( $image_tag, $image );
        }

        $html_description = '';

        if ( isset( $description ) ) {
          $html_description .= '<div class="pane-content">';
          $html_description .= '  <div class="fieldable-panels-pane">';
          $html_description .= '    <div class="field field-name-field-basic-text-text field-type-text-long field-label-hidden">';
          $html_description .= '      <div class="field-items">';
          $html_description .= '        <div class="field-item even" style="color:' . $color . '">';
          $html_description .= '          <p>';
          $html_description .= $description;
          $html_description .= '          </p>';
          $html_description .= '        </div>';
          $html_description .= '      </div>';
          $html_description .= '    </div>';
          $html_description .= '  </div>';
          $html_description .= '</div>';
        }

        $html_mobile = '<div class="hero-mobile theme-color-background ' . $additional_classes . '">';
        if ( isset( $title ) ) {
          $html_mobile .= '<h1 class="pane-title" style="color:' . $color . '">';
          $html_mobile .= $title;
          $html_mobile .= '</h1>';
        }

        if ( isset( $description ) ) {
          $html_mobile .= '<div class="pane-content" style="color:' . $color . '">';
          $html_mobile .= $description;
          $html_mobile .= '</div>';
        }
        $html_mobile .= '</div>';
      } else if ( 'slim' === $type ) {
        // ====
        // Slim
        // ====
        $image_tag = '<section class="hero-slim theme-color-background ' . $additional_classes . '" style="background-image: url(%s)">';

        if ( isset( $image ) ) {
          $html .= sprintf( $image_tag, $image );
        } else {
          $html .= sprintf( $image_tag, '' );
        }
      }

      $html .= '           <div class="container">';
      $html .= '             <div class="row">';
      $html .= '               <div class="fdt-home-container fdt-home-column-content clearfix panel-panel row-fluid container">';
      $html .= '                 <div class="fdt-home-column-content-region fdt-home-row panel-panel span12">';
      $html .= '                   <div class="panel-pane pane-fieldable-panels-pane pane-fpid-12 pane-bundle-text">';

      if ( isset( $title ) ) {
        $html .= '<h1 class="pane-title" style="color:' . $color . '">';
        $html .= $title;
        $html .= '</h1>';
      }

      if ( isset( $html_description ) ) {
        $html .= '<div  style="color:' . $color . '">' . $html_description . '</div>';
      }

      $html .= '                   </div>';
      $html .= '                 </div>';
      $html .= '               </div>';
      $html .= '             </div>';
      $html .= '           </div>';
      $html .= '         </section>';
      $html .= '        </div>';
      $html .= '      </div>';
      $html .= '    </div>';
      $html .= '  </div>';
      $html .= '</div>';

      if ( isset( $html_mobile ) ) {
        $html .= $html_mobile;
      }

      return $html;
    }

    return null;
  }
  add_filter( 'page_feature', 'page_feature_ratio_slim' );

endif;