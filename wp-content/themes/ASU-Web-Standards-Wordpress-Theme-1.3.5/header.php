<?php
/**
 * The Header for the ASU Wordpress theme.
 *
 * Displays all of the <head> section and everything up till <div id="content">
 *
 * @author Global Insititue of Sustainability
 * @author Ivan Montiel
 *
 * @package asu-wordpress-web-standards
 */

$home_url         = esc_url( home_url( '/' ) );
$theme_color      = false;
$subsite_menu     = false;
$parent_blog_name = false;
$site_title_attr  = '';
$menu_item_attr   = '';

// Check if we have options set
if ( is_array( get_option( 'wordpress_asu_theme_options' ) ) ) {
  $c_options = get_option( 'wordpress_asu_theme_options' );

  //  =============================
  //  = Title Font Size           =
  //  =============================
  // Do we have an title_font_size?
  if ( array_key_exists( 'title_font_size', $c_options ) &&
         $c_options['title_font_size'] !== '' ) {
    $title_font_size = $c_options['title_font_size'];

    if ( is_numeric( $title_font_size ) ) {
      // TODO refactor these constants
      if ( $title_font_size >= 21 && $title_font_size <= 24 ) {
        $site_title_attr .= 'font-size: ' . intval( $title_font_size ) . 'px;';
      }
    }
  }

  //  =============================
  //  = Menu Padding Size         =
  //  =============================
  // Do we have an menu_item_padding?
  if ( array_key_exists( 'menu_item_padding', $c_options ) &&
         $c_options['menu_item_padding'] !== '' ) {
    $menu_item_padding = $c_options['menu_item_padding'];

    if ( is_numeric( $menu_item_padding ) ) {
      $menu_item_attr .= '
        padding-left: ' . intval( $menu_item_padding ) . 'px !important;
        padding-right: ' . intval( $menu_item_padding ) . 'px !important;
      ';
    }
  }


  // Do we have a 404 image?
  if ( isset( $c_options ) &&
       array_key_exists( 'theme_color', $c_options ) &&
       $c_options['theme_color'] !== '' ) {
    $theme_color = $c_options['theme_color'];
  }

  // Are we a subsite?
  if ( isset( $c_options ) &&
       array_key_exists( 'subsite', $c_options ) &&
       false !== $c_options['subsite'] ) {

    // Is the parent blog id set?
    if ( array_key_exists( 'parent_blog_id', $c_options ) &&
         '' !== $c_options['parent_blog_id'] ) {
      // ====================
      // Create Subnavigation
      // ====================
      // TODO sanatize $c_options['parent_blog_id'] is number

      $subsite_menu = intval( $c_options['parent_blog_id'] );

      // Do we have a custom blog name?
      if ( array_key_exists( 'parent_blog_name', $c_options ) &&
         '' !== $c_options['parent_blog_name'] ) {
        $parent_blog_name = $c_options['parent_blog_name'];
      }

      // ===============
      // Switching Blogs
      // ===============
      // @codingStandardsIgnoreStart
      global $blog_id;
      $current_blog_id = $blog_id;
      switch_to_blog( $subsite_menu );

      if ( false === $parent_blog_name ) {
        $parent_blog_name = get_bloginfo( 'name' );
      }

      ob_start();

      $wrapper  = <<<HTML
        <li class="dropdown" id="%s" class="%s">
          <a id="drop1" href="#" class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" role="button" aria-expanded="false">
            <i class="fa fa-bars"></i>
          </a>
          <ul class="dropdown-menu" role="menu" aria-labelledby="dLabel">
            <li class="dropdown-title">{$parent_blog_name}</li>
            %s
          </ul>
        </li>
HTML;

      wp_nav_menu(
          array(
            'menu'              => 'primary',
            'theme_location'    => 'primary',
            'depth'             => 1,
            'container'         => null,
            'walker'            => new WP_Bootstrap_Dropdown_Navwalker(),
            'items_wrap'        => $wrapper,
          )
      );
      $subsite_menu = ob_get_contents();
      ob_end_clean();
      switch_to_blog( $current_blog_id );
      // @codingStandardsIgnoreEnd
      // ==============
      // Switching Back
      // ==============
    }
  }
}

?><!DOCTYPE html>
<html <?php language_attributes(); ?> class="no-js">
<head>
  <meta charset="<?php bloginfo( 'charset' ); ?>">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <title><?php wp_title( '|', true, 'right' ); ?></title>
  <link rel="profile" href="http://gmpg.org/xfn/11">

  <?php wp_head(); ?>

  <?php if ( is_user_logged_in() ) { ?>
    <style  type="text/css" media="screen">
      .navbar-ws.affix {
        top: 32px !important;
      }

      #wpadminbar {
        z-index: 999999 !important;
      }
    </style>
  <?php } ?>

  <?php if ( false !== $theme_color ) : ?>
    <style type="text/css" media="screen">
    .theme-color-background {
      background: <?php echo esc_attr( $theme_color ); ?>;
    }

    figure[class^="effect-"] {
      background: <?php echo esc_attr( $theme_color ); ?>;
    }
    </style>
  <?php endif; ?>

  <style>
    @media (max-width: 1200px) {
      .navbar-ws .navbar-nav>li>a {
        <?php echo esc_attr( $menu_item_attr ); ?>
      }
    }
  </style>
</head>

<body <?php body_class(); ?>>
  <a href="#skippy" class="sr-only">Skip to Content</a>

  <!-- Google Tag Manager ASU Universal-->
  <noscript><iframe src="//www.googletagmanager.com/ns.html?id=GTM-KDWN8Z"
  height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
  <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
  new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
  j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
  '//www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
  })(window,document,'script','SI_dataLayer','GTM-KDWN8Z');</script>
  <!-- End Google Tag Manager ASU Universal -->
  <div id="page-wrapper">
    <div id="page">
      <div id="asu_header">
        <?php include 'header-asu.php'; ?>
        <div id="site-name-desktop" class="section site-name-desktop">
          <div class="container">
            <div class="site-title" id="asu_school_name"
              style="<?php echo esc_attr( $site_title_attr ); ?>"
            >
              <?php
                // Print the parent organization and its link
              $prefix   = '<span class="first-word">%1$s</span>&nbsp;|&nbsp;';
              $cOptions = get_option( 'wordpress_asu_theme_options' );

                // Do we have a parent org?
              if ( isset( $cOptions ) && is_array( $cOptions ) &&
                       array_key_exists( 'org', $cOptions ) &&
                       $cOptions['org'] !== '' ) {
                  // Does the parent org have a link?
                if ( array_key_exists( 'org_link', $cOptions ) &&
                       $cOptions['org_link'] !== '' ) {
                    $wrapper = '<a href="%1$s" id="org-link-site-title">%2$s</a>';

                    $wrapper = sprintf( $wrapper, esc_html( $cOptions['org_link'] ), '%1$s' );
                    $prefix  = sprintf( $prefix, $wrapper );
                }

                echo wp_kses( sprintf( $prefix, esc_html( $cOptions['org'] ) ), wp_kses_allowed_html( 'post' ) );
              }
              ?>
              <a href="<?php echo esc_url( home_url() ); ?>" id="blog-name-site-title"><?php bloginfo( 'name' ); ?></a>
            </div>
          </div>
        </div>
      </div>

      <!-- Global Navigation -->
      <nav class="navbar navbar-ws">
        <div class="container">
          <div class="navbar-header">
            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#ws-navbar-collapse-1">
              <span class="sr-only">Toggle navigation</span>
              <span class="icon-bar"></span>
              <span class="icon-bar"></span>
              <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="<?php echo esc_url( home_url() ); ?>"><?php bloginfo( 'name' ); ?></a>
          </div>
          <?php
          // ======================
          // Create Main Navigation
          // ======================

          $wrapper  = '<ul id="%1$s" class="%2$s">';

          if ( ! empty( $subsite_menu ) ) {
            $wrapper .= $subsite_menu;
          }

          $wrapper .= '<li>';
          $wrapper .= "<a href=\"$home_url\" title=\"Home\"  id=\"home-icon-main-nav\">";
          $wrapper .= '<span class="fa fa-home hidden-xs hidden-sm" aria-hidden="true"></span><span class="hidden-md hidden-lg">Home</span>';
          $wrapper .= '</a>';
          $wrapper .= '</li>';
          $wrapper .= '%3$s';
          $wrapper .= '</ul>';

          wp_nav_menu(
              array(
                'menu'              => 'primary',
                'theme_location'    => 'primary',
                'depth'             => 3,
                'container'         => 'div',
                'container_class'   => 'collapse navbar-collapse',
                'container_id'      => 'ws-navbar-collapse-1',
                'menu_class'        => 'nav navbar-nav',
                'fallback_cb'       => 'WP_Bootstrap_Navwalker::fallback',
                'walker'            => new WP_Bootstrap_Navwalker(),
                'items_wrap'        => $wrapper,
              )
          );
          ?>
          </div><!-- /.navbar-collapse -->
        </nav>
        <!-- End Navigation -->
        <span id="skippy" class="sr-only"></span>
