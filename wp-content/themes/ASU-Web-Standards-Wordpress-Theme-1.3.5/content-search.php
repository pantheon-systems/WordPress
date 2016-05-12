<?php
/**
 * The template used for displaying search page content in search.php
 *
 * @package asu-wordpress-web-standards-theme
 */
?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
  <header class="entry-header">
    <?php the_title( sprintf( '<h2 class="entry-title"><a href="%s" rel="bookmark">', esc_url( get_permalink() ) ), '</a></h2>' ); ?>
  </header><!-- .entry-header -->

<div class="entry-summary">
    <?php the_excerpt(); ?>
  </div><!-- .entry-summary -->
  <footer class="entry-footer">
    <?php # edit_post_link( __( 'Edit', 'asu-wordpress-web-standards-theme' ), '<span class="edit-link">', '</span>' ); ?>
  </footer><!-- .entry-footer -->
</article><!-- #post-## -->
