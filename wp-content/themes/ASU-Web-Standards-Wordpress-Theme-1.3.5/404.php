<?php
/**
 * The template for displaying 404 pages (Not Found).
 *
 * @author The Julie Ann Wrigley Global Institute of Sustainability
 * @author Ivan Montiel
 *
 * @copyright 2014-2015 Arizona State University
 *
 * @license MIT
 * @license http://opensource.org/licenses/MIT
 *
 * @package asu-wordpress-web-standards-theme
 */

$image_404 = '';

if ( is_array( get_option( 'wordpress_asu_theme_options' ) ) ) {
  $c_options = get_option( 'wordpress_asu_theme_options' );

  // Do we have a 404 image?
  if ( isset( $c_options ) &&
       array_key_exists( 'image_404', $c_options ) &&
       $c_options['image_404'] !== '' ) {
    $image_404 = $c_options['image_404'];
  }
}

get_header();
?>

<div id="main-wrapper" class="clearfix four-oh-four-error-page">
  <div class="clearfix">
    <div class="column">
      <div class="region region-content">
        <div class="block block-system">
          <div class="content">
            <div class="panel-display clearfix">
              <section class="hero-tall hero-bg-img hero-action-call" style="background-image:url(<?php echo esc_url( $image_404 ); ?>)">
                <div class="container">
                  <div class="row">
                    <div class="fdt-home-container fdt-home-column-content clearfix panel-panel row-fluid container">
                      <div class="fdt-home-column-content-region fdt-home-row panel-panel span12">
                        <div class="panel-pane pane-fieldable-panels-pane pane-fpid-12 pane-bundle-text">
                          <h1 class="pane-title">
                            404
                          </h1>
                          <p>Not Found</p>
                          <br/>
                          <br/>
                        </div>
                        <div class="panel-pane pane-fieldable-panels-pane pane-fpid-12 pane-bundle-text">
                          <p><?php _e( 'It looks like nothing was found!', 'asu-wordpress-web-standards-theme' ); ?></p>
                          <p><?php _e( 'Maybe try searching?', 'asu-wordpress-web-standards-theme' ); ?></p>
                          
                          <?php get_search_form(); ?>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </section>
            </div>
          </div>
        </div>
      </div>
    </div><!--/page feature-->
  </div>
</div>
<?php get_footer(); ?>
