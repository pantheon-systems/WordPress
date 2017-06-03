<?php
/**
 * The Sidebar containing the main widget areas.
 *
 * @package asu-wordpress-web-standards-theme
 */
?>
<?php
if ( is_active_sidebar( 'footer' ) ) :
  dynamic_sidebar( 'footer' );
endif;

if ( is_active_sidebar( 'sidebar-1' ) ) : ?>
  <div id="sidebarNav" class="sidebar-nav affix-top">
    <?php dynamic_sidebar( 'sidebar-1' ); ?>
  </div>
<?php
endif;
?>
