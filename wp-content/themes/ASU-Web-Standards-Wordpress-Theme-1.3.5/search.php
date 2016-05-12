<?php
/**
 * The template for displaying Search Results pages.
 *
 * @package asu-wordpress-web-standards-theme
 */

get_header(); ?>
<div id="main-wrapper" class="clearfix">
  <div class="clearfix">
    <?php echo do_shortcode( '[page_feature]' ); ?>

    <div id="content" class="site-content">
      <?php echo do_shortcode( '[asu_breadcrumbs]' ); ?>
      <main id="main" class="site-main">

        <div class="container">
          <h1 class="page-title"><?php printf( __( 'Search Results for: %s', 'asu-wordpress-web-standards-theme' ), '<span>' . get_search_query() . '</span>' ); ?></h1>
          <div class="row">
            <div class="col-sm-9">
            <?php
            if ( have_posts() ) :
            ?>
              <?php
              while ( have_posts() ) {
                  the_post();

                  /**
                   * Run the loop for the search to output the results.
                   * If you want to overload this in a child theme then include a file
                   * called content-search.php and that will be used instead.
                   */
                  get_template_part( 'content', 'search' );
              }
              the_posts_navigation();
              ?>
            <?php else : ?>
              <?php get_template_part( 'content', 'none' ); ?>
            <?php endif; ?>
            </div>
            <div class="col-sm-3">
              <div id="secondary" class="widget-area row" role="complementary">
                <?php get_sidebar(); ?>
              </div>
            </div>
          </div>
        </div>
      </main><!-- #main -->
    </div>
  </div><!-- #main -->
</div><!-- #main-wrapper -->
<?php
  get_footer();
