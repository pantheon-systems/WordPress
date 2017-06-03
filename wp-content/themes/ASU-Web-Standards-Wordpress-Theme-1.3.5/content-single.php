<?php
/**
 * @package asu-wordpress-web-standards-theme
 */
?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

  <div class="entry-content">
    <?php the_content(); ?>
    <?php
      wp_link_pages(
          array(
            'before' => '<div class="page-links">' . __( 'Pages:', 'asu-wordpress-web-standards-theme' ),
            'after'  => '</div>',
          )
      );
    ?>
  </div><!-- .entry-content -->

  <footer class="entry-footer">
    <?php
      /* translators: used between list items, there is a space after the comma */
      $category_list = get_the_category_list( __( ', ', 'asu-wordpress-web-standards-theme' ) );

      /* translators: used between list items, there is a space after the comma */
      $tag_list = get_the_tag_list( '', __( ', ', 'asu-wordpress-web-standards-theme' ) );

    if ( ! asu_webstandards_categorized_blog() ) {
      // This blog only has 1 category so we just need to worry about tags in the meta text
      if ( '' != $tag_list ) {
        $meta_text = __( 'This entry was tagged %2$s. Bookmark the <a href="%3$s" rel="bookmark">permalink</a>.', 'asu-wordpress-web-standards-theme' );
      } else {
        $meta_text = __( 'Bookmark the <a href="%3$s" rel="bookmark">permalink</a>.', 'asu-wordpress-web-standards-theme' );
      }
    } else {
      // But this blog has loads of categories so we should probably display them here
      if ( '' != $tag_list ) {
        $meta_text = __( 'This entry was posted in %1$s and tagged %2$s. Bookmark the <a href="%3$s" rel="bookmark">permalink</a>.', 'asu-wordpress-web-standards-theme' );
      } else {
        $meta_text = __( 'This entry was posted in %1$s. Bookmark the <a href="%3$s" rel="bookmark">permalink</a>.', 'asu-wordpress-web-standards-theme' );
      }
    } // end check for categories on this blog

      printf(
          $meta_text,
          $category_list,
          $tag_list,
          get_permalink()
      );
    ?>

    <?php edit_post_link( __( 'Edit', 'asu-wordpress-web-standards-theme' ), '<span class="edit-link">', '</span>' ); ?>
  </footer><!-- .entry-footer -->
</article><!-- #post-## -->
