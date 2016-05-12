<?php
/**
 * Related Links Shortcode used for simplifying Bootstrap code
 *
 * @author Global Insititue of Sustainability
 * @author Ivan Montiel
 *
 * @package asu-wordpress-web-standards
 */

if ( ! function_exists( 'related_links' ) ) :
  /**
   * This shortcode takes in a title, a limit, an include_siblings flag,
   * and content between the opening and closing tags of the shortcode.
   *
   * The title attribute is a string that will be used as the title of the
   * section. Set to false for no title. Defaults to "Additional Reading".
   *
   * The limit is the total number of links to show. Defaults to 10.
   *
   * The include_siblings flag, if true, will automatically generate
   * links for pages that are siblings (hierarchically) to the current
   * page that the shortcode is on.
   *
   * The content should be new line delimited a tags.
   *
   * Example:
   * ```
   * [related-links title="Related Links" limit=5 include_siblings=false]
   *   <a href="http://google.com">Google</a>
   *   <a href="http://bing.com">Bing</a>
   * [/related-links]
   * ```
   *
   * @param $atts array
   * @param $content string
   *
   * @return string
   */
  function related_links( $atts, $content = '' ) {
    global $post;

    $related_post          = [];
    $related_links_limit   = 10;
    $include_siblings      = true;
    $related_links_title   = 'Additional Reading';
    $related_post_template = <<<HTML
<li class="related-post">
  %s
</li>
HTML;

    $related_posts_template = <<<HTML
<div class="related-articles">
  <div class="container">
    <div class="row">
      <div class="col-xs-12">
        %s
        <ul class="nav nav-pills">
          %s
        </ul>
      </div>
    </div>
  </div>
</div>
HTML;

    // Default Overrides
    if ( is_array( $atts ) ) {
      if ( array_key_exists( 'limit', $atts ) ) {
        $related_links_limit = intval( $atts['limit'] );
      }

      if ( array_key_exists( 'title', $atts ) ) {
        $related_links_title = $atts['title'];
      }

      if ( array_key_exists( 'include_siblings', $atts ) ) {
        $include_siblings = filter_var( $atts['include_siblings'], FILTER_VALIDATE_BOOLEAN );
      }
    }

    // TODO get globally defined related links

    // use the related links given in the shortcode
    if ( ! empty( $content ) ) {
      $links = mb_split( "\n", $content );

      foreach ( $links as $link ) {
        $trimmed_link = trim( $link );
        if ( ! empty( $trimmed_link ) ) {
          $related_post[] = sprintf( $related_post_template, $trimmed_link );
        }
      }
    }

    // if there are still less related links than the limit, grab
    // links to pages that are in the same level of heirarchy
    if ( $include_siblings ) {
      if ( count( $related_post ) < $related_links_limit ) {
        $ancestors = get_post_ancestors( $post->ID );

        // make sure the page actually has a parent
        if ( is_array( $ancestors ) && count( $ancestors ) > 0 ) {
          // TODO why bother with get_page_children if we are making a query anyway?
          $all_wp_pages = ( new \WP_Query() )->query( array( 'post_type' => 'page' ) );
          $pages = get_page_children( $ancestors[0], $all_wp_pages );

          // make sure the parent really does have children (sanity check)
          if ( is_array( $pages ) && count( $pages ) > 0 ) {
            foreach ( $pages as $page ) {

              // do not include yourself
              if ( $post->ID != $page->ID ) {
                $link = '<a href="' . get_permalink( $page->ID ) . '">' . $page->post_title . '</a>';
                $related_post[] = sprintf( $related_post_template, $link );
              }
            }
          }
        }
      }
    }

    // trim to the limit
    if ( count( $related_post ) > $related_links_limit ) {
      $related_post = array_slice( $related_post, 0, $related_links_limit );
    }

    // Wrap the title in an h3 if it as not false
    if ( 'false' === $related_links_title ) {
      $related_links_title = '<div class="space-top-md"></div>';
    } else {
      $related_links_title = '<h3>' . $related_links_title . '</h3>';
    }

    $related_posts = sprintf( $related_posts_template, $related_links_title, join( $related_post, '' ) );

    return $related_posts;
  }
  add_shortcode( 'related-links', 'related_links' );
endif;