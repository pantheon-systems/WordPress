<?php
/**
 * @package asu-wordpress-web-standards-theme
 */
?>
<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
  <header class="entry-header"> 
    <?php the_title( sprintf( '<h2 class="entry-title"><a href="%s" rel="bookmark">', esc_url( get_permalink() ) ), '</a></h2>' ); ?>

    <?php if ( 'post' == get_post_type() ) : ?>
    <div class="entry-meta">
      <?php asu_webstandards_posted_on(); ?>
    </div><!-- .entry-meta -->
    <?php endif; ?>
  </header><!-- .entry-header -->

  <?php
  if ( is_search() ) :
    // Only display Excerpts for Search
  ?>
  <div class="entry-summary">
  <?php the_excerpt(); ?>
  </div><!-- .entry-summary -->
  <?php else : ?>
  <div class="entry-content">
    <?php the_content( __( 'Continue reading <span class="meta-nav">&rarr;</span>', 'asu-wordpress-web-standards-theme' ) ); ?>
    <?php
      wp_link_pages(
          array(
            'before' => '<div class="page-links">' . __( 'Pages:', 'asu-wordpress-web-standards-theme' ),
            'after'  => '</div>',
          )
      );
    ?>
  </div><!-- .entry-content -->
  <?php endif; ?>

  <footer class="entry-footer">
    <?php
    if ( 'post' == get_post_type() ) :
      // Hide category and tag text for pages on Search
    ?>
    <?php
      /* translators: used between list items, there is a space after the comma */
      $categories_list = get_the_category_list( __( ', ', 'asu-wordpress-web-standards-theme' ) );
    if ( $categories_list && asu_webstandards_categorized_blog() ) :
      ?>
      <span class="cat-links">
      <?php printf( __( 'Posted in %1$s', 'asu-wordpress-web-standards-theme' ), $categories_list ); ?>
      </span>
      <?php endif; // End if categories ?>

      <?php
      /* translators: used between list items, there is a space after the comma */
      $tags_list = get_the_tag_list( '', __( ', ', 'asu-wordpress-web-standards-theme' ) );
      if ( $tags_list ) :
      ?>
      <span class="tags-links">
      <?php printf( __( 'Tagged %1$s', 'asu-wordpress-web-standards-theme' ), $tags_list ); ?>
      </span>
      <?php endif; // End if $tags_list ?>
    <?php endif; // End if 'post' == get_post_type() ?>

    <?php if ( ! post_password_required() && ( comments_open() || '0' != get_comments_number() ) ) : ?>
    <span class="comments-link"><?php comments_popup_link( __( 'Leave a comment', 'asu-wordpress-web-standards-theme' ), __( '1 Comment', 'asu-wordpress-web-standards-theme' ), __( '% Comments', 'asu-wordpress-web-standards-theme' ) ); ?></span>
    <?php endif; ?>

    <?php edit_post_link( __( 'Edit', 'asu-wordpress-web-standards-theme' ), '<span class="edit-link">', '</span>' ); ?>
  </footer><!-- .entry-footer -->
  


  
</article><!-- #post-## -->

