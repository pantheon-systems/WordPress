<?php
/**
 * This file belongs to the YIT Plugin Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */

if ( !defined( 'ABSPATH' ) ) {
    exit;
} // Exit if accessed directly

if ( !function_exists( 'yit_plugin_locate_template' ) ) {
    /**
     * Locate the templates and return the path of the file found
     *
     * @param string $plugin_basename
     * @param string $path
     * @param array  $var
     *
     * @return string
     * @since 2.0.0
     */
    function yit_plugin_locate_template( $plugin_basename, $path, $var = null ) {

        $template_path = '/theme/templates/' . $path;

        $located = locate_template( array(
                                        $template_path,
                                    ) );

        if ( !$located ) {
            $located = $plugin_basename . '/templates/' . $path;
        }

        return $located;
    }

}

if ( !function_exists( 'yit_plugin_get_template' ) ) {
    /**
     * Retrieve a template file.
     *
     * @param string $plugin_basename
     * @param string $path
     * @param mixed  $var
     * @param bool   $return
     *
     * @return string
     * @since 2.0.0
     */
    function yit_plugin_get_template( $plugin_basename, $path, $var = null, $return = false ) {

        $located = yit_plugin_locate_template( $plugin_basename, $path, $var );

        if ( $var && is_array( $var ) ) {
            extract( $var );
        }

        if ( $return ) {
            ob_start();
        }

        // include file located
        if ( file_exists( $located ) ) {
            include( $located );
        }

        if ( $return ) {
            return ob_get_clean();
        }
    }
}

if ( !function_exists( 'yit_plugin_content' ) ) {
    /**
     * Return post content with read more link (if needed)
     *
     * @param string     $what
     * @param int|string $limit
     * @param string     $more_text
     * @param string     $split
     * @param string     $in_paragraph
     *
     * @return string
     * @since 2.0.0
     */
    function yit_plugin_content( $what = 'content', $limit = 25, $more_text = '', $split = '[...]', $in_paragraph = 'true' ) {
        if ( $what == 'content' ) {
            $content = get_the_content( $more_text );
        } else {
            if ( $what == 'excerpt' ) {
                $content = get_the_excerpt();
            } else {
                $content = $what;
            }
        }

        if ( $limit == 0 ) {
            if ( $what == 'excerpt' ) {
                $content = apply_filters( 'the_excerpt', $content );
            } else {
                $content = preg_replace( '/<img[^>]+./', '', $content ); //remove images
                $content = apply_filters( 'the_content', $content );
                $content = str_replace( ']]>', ']]&gt;', $content );
            }

            return $content;
        }

        // remove the tag more from the content
        if ( preg_match( "/<(a)[^>]*class\s*=\s*(['\"])more-link\\2[^>]*>(.*?)<\/\\1>/", $content, $matches ) ) {

            if ( strpos( $matches[ 0 ], '[button' ) ) {
                $more_link = str_replace( 'href="#"', 'href="' . get_permalink() . '"', do_shortcode( $matches[ 3 ] ) );
            } else {
                $more_link = $matches[ 0 ];
            }

            $content = str_replace( $more_link, '', $content );
            $split   = '';
        }

        if ( empty( $content ) ) {
            return;
        }
        $content = explode( ' ', $content );

        if ( !empty( $more_text ) && !isset( $more_link ) ) {
            //array_pop( $content );
            $more_link = strpos( $more_text, '<a class="btn"' ) ? $more_text : '<a class="read-more' . apply_filters( 'yit_simple_read_more_classes', ' ' ) . '" href="' . get_permalink() . '">' . $more_text . '</a>';
            $split     = '';
        } elseif ( !isset( $more_link ) ) {
            $more_link = '';
        }

        // split
        if ( count( $content ) >= $limit ) {
            $split_content = '';
            for ( $i = 0; $i < $limit; $i++ ) {
                $split_content .= $content[ $i ] . ' ';
            }

            $content = $split_content . $split;
        } else {
            $content = implode( " ", $content );
        }

        // TAGS UNCLOSED
        $tags = array();
        // get all tags opened
        preg_match_all( "/(<([\w]+)[^>]*>)/", $content, $tags_opened, PREG_SET_ORDER );
        foreach ( $tags_opened as $tag ) {
            $tags[] = $tag[ 2 ];
        }

        // get all tags closed and remove it from the tags opened.. the rest will be closed at the end of the content
        preg_match_all( "/(<\/([\w]+)[^>]*>)/", $content, $tags_closed, PREG_SET_ORDER );
        foreach ( $tags_closed as $tag ) {
            unset( $tags[ array_search( $tag[ 2 ], $tags ) ] );
        }

        // close the tags
        if ( !empty( $tags ) ) {
            foreach ( $tags as $tag ) {
                $content .= "</$tag>";
            }
        }

        //$content = preg_replace( '/\[.+\]/', '', $content );
        if ( $in_paragraph == true ): $content .= $more_link; endif;
        $content = preg_replace( '/<img[^>]+./', '', $content ); //remove images
        $content = apply_filters( 'the_content', $content );
        $content = str_replace( ']]>', ']]&gt;', $content ); // echo str_replace( array( '<', '>' ), array( '&lt;', '&gt;' ), $content );
        if ( $in_paragraph == false ): $content .= $more_link; endif;

        return $content;
    }
}

if ( !function_exists( 'yit_plugin_string' ) ) {
    /**
     * Simple echo a string, with a before and after string, only if the main string is not empty.
     *
     * @param string $before What there is before the main string
     * @param string $string The main string. If it is empty or null, the functions return null.
     * @param string $after  What there is after the main string
     * @param bool   $echo   If echo or only return it
     *
     * @return string The complete string, if the main string is not empty or null
     * @since 2.0.0
     */
    function yit_plugin_string( $before = '', $string = '', $after = '', $echo = true ) {
        $html = '';

        if ( $string != '' AND !is_null( $string ) ) {
            $html = $before . $string . $after;
        }

        if ( $echo ) {
            echo $html;
        }

        return $html;
    }
}

if ( !function_exists( 'yit_plugin_decode_title' ) ) {
    /**
     * Change some special characters to put easily html into a string
     *
     * E.G.
     * string: This is [my title] with | a new line
     * return: This is <span class="title-highlight">my title</span> with <br /> a new line
     *
     * @param  string $title The string to convert
     *
     * @return string  The html
     *
     * @since 1.0
     */
    function yit_plugin_decode_title( $title ) {
        $replaces = apply_filters( 'yit_title_special_characters', array() );

        return preg_replace( array_keys( $replaces ), array_values( $replaces ), $title );
    }
}

if ( !function_exists( 'yit_plugin_get_attachment_id' ) ) {

    /**
     * Return the ID of an attachment.
     *
     * @param string $url
     *
     * @return int
     *
     * @since 2.0.0
     */

    function yit_plugin_get_attachment_id( $url ) {

        $upload_dir = wp_upload_dir();
        $dir        = trailingslashit( $upload_dir[ 'baseurl' ] );

        if ( false === strpos( $url, $dir ) ) {
            return false;
        }

        $file = basename( $url );

        $query = array(
            'post_type'  => 'attachment',
            'fields'     => 'ids',
            'meta_query' => array(
                array(
                    'value'   => $file,
                    'compare' => 'LIKE',
                ),
            ),
        );

        $query[ 'meta_query' ][ 0 ][ 'key' ] = '_wp_attached_file';
        $ids                                 = get_posts( $query );

        foreach ( $ids as $id ) {
            $attachment_image = wp_get_attachment_image_src( $id, 'full' );
            if ( $url == array_shift( $attachment_image ) || $url == str_replace( 'https://', 'http://', array_shift( $attachment_image ) ) ) {
                return $id;
            }
        }
        $query[ 'meta_query' ][ 0 ][ 'key' ] = '_wp_attachment_metadata';
        $ids                                 = get_posts( $query );

        foreach ( $ids as $id ) {

            $meta = wp_get_attachment_metadata( $id );
            if ( !isset( $meta[ 'sizes' ] ) ) {
                continue;
            }

            foreach ( (array) $meta[ 'sizes' ] as $size => $values ) {
                if ( $values[ 'file' ] == $file && $url == str_replace( 'https://', 'http://', array_shift( wp_get_attachment_image_src( $id, $size ) ) ) ) {

                    return $id;
                }
            }
        }

        return false;
    }
}

if ( !function_exists( 'yit_enqueue_script' ) ) {
    /**
     * Enqueues script.
     *
     * Registers the script if src provided (does NOT overwrite) and enqueues.
     *
     * @since  2.0.0
     * @author Simone D'Amico <simone.damico@yithemes.com>
     * @see    yit_register_script() For parameter information.
     */
    function yit_enqueue_script( $handle, $src, $deps = array(), $ver = false, $in_footer = true ) {

        if ( function_exists( 'YIT_Asset' ) && !is_admin() ) {
            $enqueue = true;
            YIT_Asset()->set( 'script', $handle, compact( 'src', 'deps', 'ver', 'in_footer', 'enqueue' ) );
        } else {
            wp_enqueue_script( $handle, $src, $deps, $ver, $in_footer );
        }
    }
}

if ( !function_exists( 'yit_enqueue_style' ) ) {
    /**
     * Enqueues style.
     *
     * Registers the style if src provided (does NOT overwrite) and enqueues.
     *
     * @since  2.0.0
     * @author Simone D'Amico <simone.damico@yithemes.com>
     * @see    yit_register_style() For parameter information.
     */
    function yit_enqueue_style( $handle, $src, $deps = array(), $ver = false, $media = 'all' ) {

        if ( function_exists( 'YIT_Asset' ) ) {
            $enqueue = true;
            $who     = YIT_Asset()->get_stylesheet_handle( get_stylesheet_uri(), 'style' );
            $where   = 'before';

            if ( false == $who ) {
                $who = '';
            }

            YIT_Asset()->set( 'style', $handle, compact( 'src', 'deps', 'ver', 'media', 'enqueue' ), $where, $who );
        } else {
            wp_enqueue_style( $handle, $src, $deps, $ver, $media );
        }
    }
}

if ( !function_exists( 'yit_get_post_meta' ) ) {
    /**
     * Retrieve the value of a metabox.
     *
     * This function retrieve the value of a metabox attached to a post. It return either a single value or an array.
     *
     * @param int    $id   Post ID.
     * @param string $meta The meta key to retrieve.
     *
     * @return mixed Single value or array
     * @since    2.0.0
     */
    function yit_get_post_meta( $id, $meta ) {
        if ( !strpos( $meta, '[' ) ) {
            return get_post_meta( $id, $meta, true );
        }

        $sub_meta = explode( '[', $meta );

        $meta = get_post_meta( $id, current( $sub_meta ), true );
        for ( $i = 1; $i < count( $sub_meta ); $i++ ) {
            $current_submeta = rtrim( $sub_meta[ $i ], ']' );
            if ( !isset( $meta[ $current_submeta ] ) )
                return false;
            $meta = $meta[ $current_submeta ];
        }

        return $meta;
    }
}

if ( !function_exists( 'yit_string' ) ) {
    /**
     * Simple echo a string, with a before and after string, only if the main string is not empty.
     *
     * @param string $before What there is before the main string
     * @param string $string The main string. If it is empty or null, the functions return null.
     * @param string $after  What there is after the main string
     * @param bool   $echo   If echo or only return it
     *
     * @return string The complete string, if the main string is not empty or null
     * @since 2.0.0
     */
    function yit_string( $before = '', $string = '', $after = '', $echo = true ) {
        $html = '';

        if ( $string != '' AND !is_null( $string ) ) {
            $html = $before . $string . $after;
        }

        if ( $echo ) {
            echo $html;
        }

        return $html;
    }
}


if ( !function_exists( 'yit_pagination' ) ) {
    /**
     * Print pagination
     *
     * @param string $pages
     * @param int    $range
     *
     * @return string
     * @since 2.0.0
     */
    function yit_pagination( $pages = '', $range = 10 ) {
        $showitems = ( $range * 2 ) + 1;

        $paged = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : false;
        if ( $paged === false ) {
            $paged = ( get_query_var( 'page' ) ) ? get_query_var( 'page' ) : false;
        }
        if ( $paged === false ) {
            $paged = 1;
        }


        $html = '';

        if ( $pages == '' ) {
            global $wp_query;

            if ( isset( $wp_query->max_num_pages ) ) {
                $pages = $wp_query->max_num_pages;
            }

            if ( !$pages ) {
                $pages = 1;
            }
        }

        if ( 1 != $pages ) {
            $html .= "<div class='general-pagination clearfix'>";
            if ( $paged > 2 ) {
                $html .= sprintf( '<a class="%s" href="%s">&laquo;</a>', 'yit_pagination_first', get_pagenum_link( 1 ) );
            }
            if ( $paged > 1 ) {
                $html .= sprintf( '<a class="%s" href="%s">&lsaquo;</a>', 'yit_pagination_previous', get_pagenum_link( $paged - 1 ) );
            }

            for ( $i = 1; $i <= $pages; $i++ ) {
                if ( 1 != $pages && ( !( $i >= $paged + $range + 1 || $i <= $paged - $range - 1 ) || $pages <= $showitems ) ) {
                    $class = ( $paged == $i ) ? " class='selected'" : '';
                    $html  .= "<a href='" . get_pagenum_link( $i ) . "'$class >$i</a>";
                }
            }

            if ( $paged < $pages ) {
                $html .= sprintf( '<a class="%s" href="%s">&rsaquo;</a>', 'yit_pagination_next', get_pagenum_link( $paged + 1 ) );
            }
            if ( $paged < $pages - 1 ) {
                $html .= sprintf( '<a class="%s" href="%s">&raquo;</a>', 'yit_pagination_last', get_pagenum_link( $pages ) );
            }

            $html .= "</div>\n";
        }

        echo apply_filters( 'yit_pagination_html', $html );
    }
}

if ( !function_exists( 'yit_registered_sidebars' ) ) {
    /**
     * Retrieve all registered sidebars
     *
     * @return array
     * @since 2.0.0
     */
    function yit_registered_sidebars() {
        global $wp_registered_sidebars;

        $return = array();

        if ( empty( $wp_registered_sidebars ) ) {
            $return = array( '' => '' );
        }

        foreach ( ( array ) $wp_registered_sidebars as $the_ ) {
            $return[ $the_[ 'name' ] ] = $the_[ 'name' ];
        }

        ksort( $return );

        return $return;
    }
}

if ( !function_exists( 'yit_layout_option' ) ) {
    /**
     * Retrieve a layout option
     *
     * @param        $key
     * @param bool   $id
     * @param string $type
     * @param string $model
     *
     * @return array
     * @since 2.0.0
     */
    function yit_layout_option( $key, $id = false, $type = "post", $model = "post_type" ) {

        $option = '';

        if ( defined( 'YIT' ) ) {
            $option = YIT_Layout_Panel()->get_option( $key, $id, $type, $model );
        } else {
            if ( !$id && ( is_single() || is_page() ) ) {
                global $post;
                $id = $post->ID;
            } elseif ( $id != 'all' ) {
                $option = get_post_meta( $id, $key );
            }
        }

        return $option;
    }
}

if ( !function_exists( 'yit_curPageURL' ) ) {
    /**
     * Retrieve the current complete url
     *
     * @since 1.0
     */
    function yit_curPageURL() {
        $pageURL = 'http';
        if ( isset( $_SERVER[ "HTTPS" ] ) AND $_SERVER[ "HTTPS" ] == "on" ) {
            $pageURL .= "s";
        }

        $pageURL .= "://";

        if ( isset( $_SERVER[ "SERVER_PORT" ] ) AND $_SERVER[ "SERVER_PORT" ] != "80" ) {
            $pageURL .= $_SERVER[ "SERVER_NAME" ] . ":" . $_SERVER[ "SERVER_PORT" ] . $_SERVER[ "REQUEST_URI" ];
        } else {
            $pageURL .= $_SERVER[ "SERVER_NAME" ] . $_SERVER[ "REQUEST_URI" ];
        }

        return $pageURL;
    }
}

if ( !function_exists( 'yit_get_excluded_categories' ) ) {
    /**
     *
     * Retrieve the escluded categories, set on Theme Options
     *
     * @param int $k
     *
     * @return string String with all id categories excluded, separated by a comma
     *
     * @since 2.0.0
     */

    function yit_get_excluded_categories( $k = 1 ) {

        global $post;

        if ( !isset( $post->ID ) ) {
            return;
        }

        $cf_cats = get_post_meta( $post->ID, 'blog-cats', true );

        if ( !empty( $cf_cats ) ) {
            return $cf_cats;
        }

        $cats = function_exists( 'yit_get_option' ) ? yit_get_option( 'blog-excluded-cats' ) : '';


        if ( !is_array( $cats ) || empty( $cats ) || !isset( $cats[ $k ] ) ) {
            return;
        }

        $cats = array_map( 'trim', $cats[ $k ] );

        $i     = 0;
        $query = '';
        foreach ( $cats as $cat ) {
            $query .= ",-$cat";

            $i++;
        }

        ltrim( ',', $query );

        return $query;
    }
}


if ( !function_exists( 'yit_add_extra_theme_headers' ) ) {
    add_filter( 'extra_theme_headers', 'yit_add_extra_theme_headers' );

    /**
     * Check the framework core version
     *
     * @param $headers Array
     *
     * @return bool
     * @since  2.0.0
     * @author Andrea Grillo <andrea.grillo@yithemes.com>
     */
    function yit_add_extra_theme_headers( $headers ) {
        $headers[] = 'Core Framework Version';

        return $headers;
    }
}

if ( !function_exists( 'yit_check_plugin_support' ) ) {
    /**
     * Check the framework core version
     *
     * @return bool
     * @since  2.0.0
     * @author Andrea Grillo <andrea.grillo@yithemes.com>
     */
    function yit_check_plugin_support() {

        $headers[ 'core' ]   = wp_get_theme()->get( 'Core Framework Version' );
        $headers[ 'author' ] = wp_get_theme()->get( 'Author' );

        if ( !$headers[ 'core' ] && defined( 'YIT_CORE_VERSION' ) ) {
            $headers[ 'core' ] = YIT_CORE_VERSION;
        }

        if ( ( !empty( $headers[ 'core' ] ) && version_compare( $headers[ 'core' ], '2.0.0', '<=' ) ) || $headers[ 'author' ] != 'Your Inspiration Themes' ) {
            return true;
        } else {
            return false;
        }
    }
}

if ( !function_exists( 'yit_ie_version' ) ) {
    /**
     * Retrieve IE version.
     *
     * @return int|float
     * @since  1.0.0
     * @author Andrea Grillo <andrea.grillo@yithemes.com>, Andrea Frascaspata<andrea.frascaspata@yithemes.com>
     */
    function yit_ie_version() {

        if ( !isset( $_SERVER[ 'HTTP_USER_AGENT' ] ) ) {
            return -1;
        }
        preg_match( '/MSIE ([0-9]+\.[0-9])/', $_SERVER[ 'HTTP_USER_AGENT' ], $reg );

        if ( !isset( $reg[ 1 ] ) ) // IE 11 FIX
        {
            preg_match( '/rv:([0-9]+\.[0-9])/', $_SERVER[ 'HTTP_USER_AGENT' ], $reg );
            if ( !isset( $reg[ 1 ] ) ) {
                return -1;
            } else {
                return floatval( $reg[ 1 ] );
            }
        } else {
            return floatval( $reg[ 1 ] );
        }
    }
}

if ( !function_exists( 'yit_avoid_duplicate' ) ) {
    /**
     * Check if something exists. If yes, add a -N to the value where N is a number.
     *
     * @param mixed  $value
     * @param array  $array
     * @param string $check
     *
     * @return mixed
     * @since  2.0.0
     * @author Antonino Scarf� <antonino.scarfi@yithemes.com>
     */
    function yit_avoid_duplicate( $value, $array, $check = 'value' ) {
        $match = array();

        if ( !is_array( $array ) ) {
            return $value;
        }

        if ( ( $check == 'value' && !in_array( $value, $array ) ) || ( $check == 'key' && !isset( $array[ $value ] ) ) ) {
            return $value;
        } else {
            if ( !preg_match( '/([a-z]+)-([0-9]+)/', $value, $match ) ) {
                $i = 2;
            } else {
                $i     = intval( $match[ 2 ] ) + 1;
                $value = $match[ 1 ];
            }

            return yit_avoid_duplicate( $value . '-' . $i, $array, $check );
        }
    }
}

if ( !function_exists( 'yit_title_special_characters' ) ) {
    /**
     * The chars used in yit_decode_title() and yit_encode_title()
     *
     * E.G.
     * string: This is [my title] with | a new line
     * return: This is <span class="highlight">my title</span> with <br /> a new line
     *
     * @param  string $title The string to convert
     *
     * @return string  The html
     *
     * @since 1.0
     */
    function yit_title_special_characters( $chars ) {
        return array_merge( $chars, array(
            '/[=\[](.*?)[=\]]/' => '<span class="title-highlight">$1</span>',
            '/\|/'              => '<br />',
        ) );
    }

    add_filter( 'yit_title_special_characters', 'yit_title_special_characters' );
}

if ( !function_exists( 'yit_decode_title' ) ) {
    /**
     * Change some special characters to put easily html into a string
     *
     * E.G.
     * string: This is [my title] with | a new line
     * return: This is <span class="title-highlight">my title</span> with <br /> a new line
     *
     * @param  string $title The string to convert
     *
     * @return string  The html
     *
     * @since 1.0
     */
    function yit_decode_title( $title ) {
        $replaces = apply_filters( 'yit_title_special_characters', array() );

        return preg_replace( array_keys( $replaces ), array_values( $replaces ), $title );
    }
}

if ( !function_exists( 'yit_encode_title' ) ) {
    /**
     * Change some special characters to put easily html into a string
     *
     * E.G.
     * string: This is [my title] with | a new line
     * return: This is <span class="title-highlight">my title</span> with <br /> a new line
     *
     * @param  string $title The string to convert
     *
     * @return string  The html
     *
     * @since 1.0
     */
    function yit_encode_title( $title ) {
        $replaces = apply_filters( 'yit_title_special_characters', array() );

        return preg_replace( array_values( $replaces ), array_keys( $replaces ), $title );
    }
}

if ( !function_exists( 'yit_remove_chars_title' ) ) {
    /**
     * Change some special characters to put easily html into a string
     *
     * E.G.
     * string: This is [my title] with | a new line
     * return: This is <span class="title-highlight">my title</span> with <br /> a new line
     *
     * @param  string $title The string to convert
     *
     * @return string  The html
     *
     * @since 1.0
     */
    function yit_remove_chars_title( $title ) {
        $replaces = apply_filters( 'yit_title_special_characters', array() );

        return preg_replace( array_keys( $replaces ), '$1', $title );
    }
}

if ( !function_exists( 'is_shop_installed' ) ) {
    /**
     * Detect if there is a shop plugin installed
     *
     * @return bool
     * @since  2.0.0
     * @author Francesco Grasso <francesco.grasso@yithemes.com
     */
    function is_shop_installed() {
        global $woocommerce;
        if ( isset( $woocommerce ) || defined( 'JIGOSHOP_VERSION' ) ) {
            return true;
        } else {
            return false;
        }
    }
}

if ( !function_exists( 'yit_load_js_file' ) ) {
    /**
     * Load .min.js file if WP_Debug is not defined
     *
     * @param string $filename The file name
     *
     * @return string The file path
     * @since  2.0.0
     * @author Andrea Grillo <andrea.grillo@yithemes.com>
     */
    function yit_load_js_file( $filename ) {

        if ( !( ( defined( 'WP_DEBUG' ) && WP_DEBUG ) || ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) || isset( $_GET[ 'yith_script_debug' ] ) ) ) {
            $filename = str_replace( '.js', '.min.js', $filename );
        }

        return $filename;
    }
}

if ( !function_exists( 'yit_wpml_register_string' ) ) {
    /**
     * Register a string in wpml trnslation
     *
     * @param $contenxt context name
     * @param $name     string name
     * @param $value    value to translate
     *
     * @since  2.0.0
     * @author Andrea Frascaspata <andrea.frascaspata@yithemes.com>
     */
    function yit_wpml_register_string( $contenxt, $name, $value ) {
        // wpml string translation
        do_action( 'wpml_register_single_string', $contenxt, $name, $value );
    }
}

if ( !function_exists( 'yit_wpml_string_translate' ) ) {
    /**
     * Get a string translation
     *
     * @param $contenxt         context name
     * @param $name             string name
     * @param $default_value    value to translate
     *
     * @return string the string translated
     * @since  2.0.0
     * @author Andrea Frascaspata <andrea.frascaspata@yithemes.com>
     */
    function yit_wpml_string_translate( $contenxt, $name, $default_value ) {
        return apply_filters( 'wpml_translate_single_string', $default_value, $contenxt, $name );
    }

}

if ( !function_exists( 'yit_wpml_object_id' ) ) {
    /**
     * Get id of post translation in current language
     *
     * @param int         $element_id
     * @param string      $element_type
     * @param bool        $return_original_if_missing
     * @param null|string $ulanguage_code
     *
     * @return int the translation id
     * @since  2.0.0
     * @author Antonio La Rocca <antonio.larocca@yithemes.com>
     */
    function yit_wpml_object_id( $element_id, $element_type = 'post', $return_original_if_missing = false, $ulanguage_code = null ) {
        if ( function_exists( 'wpml_object_id_filter' ) ) {
            return wpml_object_id_filter( $element_id, $element_type, $return_original_if_missing, $ulanguage_code );
        } elseif ( function_exists( 'icl_object_id' ) ) {
            return icl_object_id( $element_id, $element_type, $return_original_if_missing, $ulanguage_code );
        } else {
            return $element_id;
        }
    }

}


if ( !function_exists( 'yith_get_formatted_price' ) ) {
    /**
     * Format the price with a currency symbol.
     *
     * @param float $price
     * @param array $args (default: array())
     *
     * @return string
     */
    function yith_get_formatted_price( $price, $args = array() ) {
        extract( apply_filters( 'wc_price_args', wp_parse_args( $args, array(
            'ex_tax_label'       => false,
            'currency'           => '',
            'decimal_separator'  => wc_get_price_decimal_separator(),
            'thousand_separator' => wc_get_price_thousand_separator(),
            'decimals'           => wc_get_price_decimals(),
            'price_format'       => get_woocommerce_price_format(),
        ) ) ) );

        $negative = $price < 0;
        $price    = apply_filters( 'raw_woocommerce_price', floatval( $negative ? $price * -1 : $price ) );
        $price    = apply_filters( 'formatted_woocommerce_price', number_format( $price, $decimals, $decimal_separator, $thousand_separator ), $price, $decimals, $decimal_separator, $thousand_separator );

        if ( apply_filters( 'woocommerce_price_trim_zeros', false ) && $decimals > 0 ) {
            $price = wc_trim_zeros( $price );
        }

        $formatted_price = ( $negative ? '-' : '' ) . sprintf( $price_format, get_woocommerce_currency_symbol( $currency ), $price );
        $return          = $formatted_price;

        return apply_filters( 'wc_price', $return, $price, $args );
    }
}

if ( !function_exists( 'yith_get_terms' ) ) {
    /**
     * Get terms
     *
     * @param $args
     *
     * @return array|int|WP_Error
     */
    function yith_get_terms( $args ) {
        global $wp_version;
        if ( version_compare( $wp_version, '4.5', '>=' ) ) {
            $terms = get_terms( $args );
        } else {
            $terms = get_terms( $args[ 'taxonomy' ], $args );
        }

        return $terms;
    }
}

if ( !function_exists( 'yith_field_deps_data' ) ) {
    function yith_field_deps_data( $args ) {
        $deps_data = '';
        if ( isset( $args[ 'deps' ] ) && ( isset( $args[ 'deps' ][ 'ids' ] ) || isset( $args[ 'deps' ][ 'id' ] ) ) && ( isset( $args[ 'deps' ][ 'values' ] ) || isset( $args[ 'deps' ][ 'value' ] ) ) ) {
            $deps       = $args[ 'deps' ];
            $id         = isset( $deps[ 'target-id' ] ) ? $deps[ 'target-id' ] : $args[ 'id' ];
            $dep_id     = isset( $deps[ 'id' ] ) ? $deps[ 'id' ] : $deps[ 'ids' ];
            $dep_values = isset( $deps[ 'value' ] ) ? $deps[ 'value' ] : $deps[ 'values' ];
            $dep_type   = isset( $deps[ 'type' ] ) ? $deps[ 'type' ] : 'hide'; // possible values: hide|disable

            $deps_data = "data-dep-target='$id' data-dep-id='$dep_id' data-dep-value='$dep_values' data-dep-type='$dep_type'";
        }

        return $deps_data;
    }
}

if ( !function_exists( 'yith_panel_field_deps_data' ) ) {
    /**
     * @param                                               $option
     * @param YIT_Plugin_Panel|YIT_Plugin_Panel_WooCommerce $panel
     *
     * @return string
     */
    function yith_panel_field_deps_data( $option, $panel ) {
        $deps_data = '';
        if ( isset( $option[ 'deps' ] ) && ( isset( $option[ 'deps' ][ 'ids' ] ) || isset( $option[ 'deps' ][ 'id' ] ) ) && isset( $option[ 'deps' ][ 'values' ] ) ) {
            $dep_id                    = isset( $option[ 'deps' ][ 'id' ] ) ? $option[ 'deps' ][ 'id' ] : $option[ 'deps' ][ 'ids' ];
            $option[ 'deps' ][ 'ids' ] = $option[ 'deps' ][ 'id' ] = $panel->get_id_field( $dep_id );
            $option[ 'id' ]            = $panel->get_id_field( $option[ 'id' ] );

            $deps_data = yith_field_deps_data( $option );
        }

        return $deps_data;
    }
}

if ( !function_exists( 'yith_plugin_fw_get_field' ) ) {
    /**
     * @param array $field
     * @param bool  $echo
     * @param bool  $show_container
     *
     * @return string|void
     */
    function yith_plugin_fw_get_field( $field, $echo = false, $show_container = true ) {
        if ( empty( $field[ 'type' ] ) )
            return '';

        if ( !isset( $field[ 'value' ] ) )
            $field[ 'value' ] = '';

        if ( !isset( $field[ 'name' ] ) )
            $field[ 'name' ] = '';

        if ( !isset( $field[ 'custom_attributes' ] ) )
            $field[ 'custom_attributes' ] = '';

        if ( !isset( $field[ 'default' ] ) && isset( $field[ 'std' ] ) )
            $field[ 'default' ] = $field[ 'std' ];


        $field_template = yith_plugin_fw_get_field_template_path( $field );
        if ( $field_template ) {
            if ( !$echo )
                ob_start();

            if ( $show_container ) echo '<div class="yith-plugin-fw-field-wrapper yith-plugin-fw-' . $field[ 'type' ] . '-field-wrapper">';

            do_action( 'yith_plugin_fw_get_field_before', $field );
            do_action( 'yith_plugin_fw_get_field_' . $field[ 'type' ] . '_before', $field );

            include( $field_template );

            do_action( 'yith_plugin_fw_get_field_after', $field );
            do_action( 'yith_plugin_fw_get_field_' . $field[ 'type' ] . '_after', $field );

            if ( $show_container ) echo '</div>';

            if ( !$echo )
                return ob_get_clean();
        }
    }
}

if ( !function_exists( 'yith_plugin_fw_get_field_template_path' ) ) {
    function yith_plugin_fw_get_field_template_path( $field ) {
        if ( empty( $field[ 'type' ] ) )
            return false;

        $field_template = YIT_CORE_PLUGIN_TEMPLATE_PATH . '/fields/' . sanitize_title( $field[ 'type' ] ) . '.php';
        $field_template = apply_filters( 'yith_plugin_fw_get_field_template_path', $field_template, $field );

        return file_exists( $field_template ) ? $field_template : false;
    }
}

if ( !function_exists( 'yith_plugin_fw_html_data_to_string' ) ) {
    function yith_plugin_fw_html_data_to_string( $data = array(), $echo = false ) {
        $html_data = '';

        if ( is_array( $data ) ) {
            foreach ( $data as $key => $value ) {
                $current_value = !is_array( $value ) ? $value : implode( ',', $value );
                $html_data     .= " data-$key='$current_value'";
            }
            $html_data .= ' ';
        }

        if ( $echo )
            echo $html_data;
        else
            return $html_data;
    }
}
if ( !function_exists( 'yith_plugin_fw_get_icon' ) ) {
    function yith_plugin_fw_get_icon( $icon = '', $args = array() ) {
        return YIT_Icons()->get_icon( $icon, $args );
    }
}

if ( !function_exists( 'yith_plugin_fw_is_true' ) ) {
    function yith_plugin_fw_is_true( $value ) {
        return true === $value || 1 === $value || '1' === $value || 'yes' === $value;
    }
}


if ( !function_exists( 'yith_plugin_fw_enqueue_enhanced_select' ) ) {
    function yith_plugin_fw_enqueue_enhanced_select() {
        wp_enqueue_script( 'yith-enhanced-select' );
        $select2_style_to_enqueue = function_exists( 'WC' ) ? 'woocommerce_admin_styles' : 'yith-select2-no-wc';
        wp_enqueue_style( $select2_style_to_enqueue );
    }
}

if ( !function_exists( 'yit_add_select2_fields' ) ) {
    /**
     * Add select 2
     *
     * @param array $args
     */
    function yit_add_select2_fields( $args = array() ) {
        $default = array(
            'type'              => 'hidden',
            'class'             => '',
            'id'                => '',
            'name'              => '',
            'data-placeholder'  => '',
            'data-allow_clear'  => false,
            'data-selected'     => '',
            'data-multiple'     => false,
            'data-action'       => '',
            'value'             => '',
            'style'             => '',
            'custom-attributes' => array()
        );

        $args = wp_parse_args( $args, $default );

        $custom_attributes = array();
        foreach ( $args[ 'custom-attributes' ] as $attribute => $attribute_value ) {
            $custom_attributes[] = esc_attr( $attribute ) . '="' . esc_attr( $attribute_value ) . '"';
        }
        $custom_attributes = implode( ' ', $custom_attributes );

        if ( !function_exists( 'WC' ) || version_compare( WC()->version, '2.7.0', '>=' ) ) {
            if ( $args[ 'data-multiple' ] === true && substr( $args[ 'name' ], -2 ) != '[]' ) {
                $args[ 'name' ] = $args[ 'name' ] . '[]';
            }
            $select2_template_name = 'select2.php';

        } else {
            if ( $args[ 'data-multiple' ] === false && is_array( $args[ 'data-selected' ] ) ) {
                $args[ 'data-selected' ] = current( $args[ 'data-selected' ] );
            }
            $select2_template_name = 'select2-wc-2.6.php';
        }

        $template = YIT_CORE_PLUGIN_TEMPLATE_PATH . '/fields/resources/' . $select2_template_name;
        if ( file_exists( $template ) ) {
            include $template;
        }
    }
}

if ( !function_exists( 'yith_plugin_fw_get_version' ) ) {
    function yith_plugin_fw_get_version() {
        $plugin_fw_data = get_file_data( trailingslashit( YIT_CORE_PLUGIN_PATH ) . 'init.php', array( 'Version' => 'Version' ) );
        return $plugin_fw_data[ 'Version' ];
    }
}

if ( !function_exists( 'yith_get_premium_support_url' ) ) {
    //@TODO: To Remove
    /**
     * Return the url for My Account > Support dashboard
     *
     * @return string The complete string, if the main string is not empty or null
     * @since 2.0.0
     */
    function yith_get_premium_support_url() {
        return 'https://yithemes.com/my-account/support/dashboard/';
    }
}

if ( !function_exists( 'yith_plugin_fw_is_panel' ) ) {
    function yith_plugin_fw_is_panel() {
        $panel_screen_id = 'yith-plugins_page';
        $screen          = function_exists( 'get_current_screen' ) ? get_current_screen() : null;

        return $screen instanceof WP_Screen && strpos( $screen->id, $panel_screen_id ) !== false;
    }
}