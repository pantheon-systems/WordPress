<?php
/**
 * This file belongs to the YIT Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */


if ( ! defined( 'ABSPATH' ) ) {
    exit;
} // Exit if accessed directly

if ( ! class_exists( 'YIT_Video' ) ) {
    /**
     * YIT Video
     *
     * Class to manage the video from youtube and vimeo or other services
     *
     * @class YIT_Video
     * @package    YITH
     * @since      1.0.0
     * @author     Antonino Scarfi' <antonino.scarfi@yithemes.com>
     *
     */

    class YIT_Video {

        /**
         * Generate the HTML for a youtube video
         *
         * @static
         *
         * @param array $args Array of arguments to configure the video to generate
         *
         * @return string
         * @since  1.0
         * @author Antonino Scarfi' <antonino.scarfi@yithemes.com>
         */
        public static function youtube( $args = array() ) {
            $defaults = array(
                'id' => '',
                'url' => '',
                'width' => 425,
                'height' => 356,
                'echo' => false
            );
            $args = wp_parse_args( $args, $defaults );
            extract( $args );

            // get video ID if you have only URL
            if ( empty( $id ) && ! empty( $url ) ) {
                $id = self::video_id_by_url( $url );
            } elseif ( empty( $id ) && empty( $url ) ) {
                return;
            }

            ob_start();

            $id = preg_replace( '/[&|&amp;]feature=([\w\-]*)/', '', $id );
            $id = preg_replace( '/(youtube|vimeo):/', '', $id ); ?>

            <div class="post_video youtube">
                <iframe wmode="transparent" width="<?php echo $width; ?>" height="<?php echo $height; ?>" src="https://www.youtube.com/embed/<?php echo $id; ?>?wmode=transparent" frameborder="0" allowfullscreen></iframe>
            </div>

            <?php
            $html = apply_filters( 'yit_video_youtube', ob_get_clean() );

            if( $echo ) echo $html;

            return $html;
        }

        /**
         * Generate the HTML for a vimeo video
         *
         * @static
         *
         * @param array $args Array of arguments to configure the video to generate
         *
         * @return string
         * @since  1.0
         * @author Antonino Scarfi' <antonino.scarfi@yithemes.com>
         */
        public static function vimeo( $args = array() ) {
            $defaults = array(
                'id' => '',
                'url' => '',
                'width' => 425,
                'height' => 356,
                'echo' => false
            );
            $args = wp_parse_args( $args, $defaults );
            extract( $args );

            // get video ID if you have only URL
            if ( empty( $id ) && ! empty( $url ) ) {
                $id = self::video_id_by_url( $url );
            }

            ob_start();

            $id = preg_replace( '/[&|&amp;]feature=([\w\-]*)/', '', $id );
            $id = preg_replace( '/(youtube|vimeo):/', '', $id );
            $http  = is_ssl()? 'https' : 'http';
            ?>


            <div class="post_video vimeo">
                <iframe wmode="transparent" src="<?php echo $http;?>://player.vimeo.com/video/<?php echo $id; ?>?title=0&amp;byline=0&amp;portrait=0" width="<?php echo $width; ?>" height="<?php echo $height; ?>" frameborder="0" webkitAllowFullScreen mozallowfullscreen allowFullScreen></iframe>
            </div>

            <?php
            $html = apply_filters( 'yit_video_vimeo', ob_get_clean() );

            if( $echo ) echo $html;

            return $html;
        }

        /**
         * Retrieve video ID from URL
         *
         * @static
         *
         * @param array $url The URL of video
         *
         * @return bool|string
         * @since  1.0
         * @author Antonino Scarfi' <antonino.scarfi@yithemes.com>
         */
        public static function video_id_by_url( $url ) {
            $parsed = parse_url( esc_url( $url ) );


            if ( ! isset( $parsed['host'] ) ) {
                return false;
            }


            switch ( $parsed['host'] ) {
                case 'youtube.com' :
                case 'www.youtube.com' :
                case 'youtu.be' :
                case 'www.youtu.be' :
                    $id = self::youtube_id_by_url( $url );
                    return "youtube:$id";

                case 'www.vimeo.com' :
                case 'vimeo.com' :
                    preg_match( '/http(s)?:\/\/(\w+.)?vimeo\.com\/(.*\/)?([0-9]+)/', $url, $matches );

                    $id = trim( $matches[4], '/' );
                    return "vimeo:$id";

                default :
                    return false;

            }
        }

        /**
         * Retrieve video ID from URL
         *
         * @static
         *
         * @param array $url The URL of video
         *
         * @return bool|string
         * @since  1.0
         * @author Antonino Scarfi' <antonino.scarfi@yithemes.com>
         */
        protected static function youtube_id_by_url( $url ) {
            if ( preg_match( '/http(s)?:\/\/youtu.be/', $url, $matches) ) {
                $url = parse_url($url, PHP_URL_PATH);
                $url = str_replace( '/', '', $url);
                return $url;

            } elseif ( preg_match( '/watch/', $url, $matches) ) {
                $arr = parse_url($url);
                $url = str_replace( 'v=', '', $arr['query'] );
                return $url;

            } elseif ( preg_match( '/http(s)?:\/\/(\w+.)?youtube.com\/v/', $url, $matches) ) {
                $arr = parse_url($url);
                $url = str_replace( '/v/', '', $arr['path'] );
                return $url;

            } elseif ( preg_match( '/http(s)?:\/\/(\w+.)?youtube.com\/embed/', $url, $matches) ) {
                $arr = parse_url($url);
                $url = str_replace( '/embed/', '', $arr['path'] );
                return $url;

            } elseif ( preg_match("#(?<=v=)[a-zA-Z0-9-]+(?=&)|(?<=[0-9]/)[^&\n]+|(?<=v=)[^&\n]+#", $url, $matches) ) {
                return $matches[0];

            } else {
                return false;
            }
        }

    }
}