<?php
/**
 * Handle the lazyload required assets: inline CSS and JS
 *
 * @package RocketLazyload
 */

namespace RocketLazyload;

/**
 * Class containing the methods to return or print the assets needed for lazyloading
 */
class Assets
{
    /**
     * Inserts the lazyload script in the HTML
     *
     * @param array $args Array of arguments to populate the lazyload script options.
     * @return void
     */
    public function insertLazyloadScript($args = [])
    {
        echo $this->getLazyloadScript($args);
    }

    /**
     * Returns the lazyload inline script
     *
     * @param array $args Array of arguments to populate the lazyload script options.
     * @return string
     */
    public function getLazyloadScript($args = [])
    {
        $defaults = [
            'base_url'  => '',
            'elements'  => [
                'img',
                'iframe',
            ],
            'threshold' => 300,
            'version'   => '',
            'polyfill'  => false,
        ];

        $args   = wp_parse_args($args, $defaults);
        $min    = ( defined('SCRIPT_DEBUG') && SCRIPT_DEBUG ) ? '' : '.min';
        $script = '';

        if (isset($args['polyfill']) && $args['polyfill']) {
            $script .= '<script crossorigin="anonymous" src="https://polyfill.io/v3/polyfill.min.js?flags=gated&features=default%2CIntersectionObserver%2CIntersectionObserverEntry"></script>';
        }

        $script .= '<script>
            window.lazyLoadOptions = {
                elements_selector: "' . esc_attr(implode(',', $args['elements'])) . '",
                data_src: "lazy-src",
                data_srcset: "lazy-srcset",
                data_sizes: "lazy-sizes",
                skip_invisible: false,
                class_loading: "lazyloading",
                class_loaded: "lazyloaded",
                threshold: ' . esc_attr($args['threshold']) . ',
                callback_load: function(element) {
                    if ( element.tagName === "IFRAME" && element.dataset.rocketLazyload == "fitvidscompatible" ) {
                        if (element.classList.contains("lazyloaded") ) {
                            if (typeof window.jQuery != "undefined") {
                                if (jQuery.fn.fitVids) {
                                    jQuery(element).parent().fitVids();
                                }
                            }
                        }
                    }
                }
            };
        
        // Listen to the Initialized event
        window.addEventListener(\'LazyLoad::Initialized\', function (e) {
            // Get the instance and puts it in the lazyLoadInstance variable
            var lazyLoadInstance = e.detail.instance;
        
            if (window.MutationObserver) {
                var observer = new MutationObserver(function(mutations) {
                    mutations.forEach(function(mutation) {
                        mutation.addedNodes.forEach(function(node) {
                            if (typeof node.getElementsByTagName !== \'function\') {
                                return;
                            }

                            imgs = node.getElementsByTagName(\'img\');
                            iframes = node.getElementsByTagName(\'iframe\');
                            rocket_lazy = node.getElementsByClassName(\'rocket-lazyload\');

                            if ( 0 === imgs.length && 0 === iframes.length && 0 === rocket_lazy.length ) {
                                return;
                            }

                            lazyLoadInstance.update();
                        } );
                    } );
                } );
                
                var b      = document.getElementsByTagName("body")[0];
                var config = { childList: true, subtree: true };
                
                observer.observe(b, config);
            }
        }, false);
        </script>';

        /**
         * Filters the script tag for the lazyload script
         *
         * @since 2.2.6
         *
         * @param $script_tag HTML tag for the lazyload script.
         */
        $script .= apply_filters('rocket_lazyload_script_tag', '<script data-no-minify="1" async src="' . $args['base_url'] . $args['version'] . '/lazyload' . $min . '.js"></script>');

        return $script;
    }

    /**
     * Inserts in the HTML the script to replace the Youtube thumbnail by the iframe.
     *
     * @param array $args Array of arguments to populate the script options.
     * @return void
     */
    public function insertYoutubeThumbnailScript($args = [])
    {
        echo $this->getYoutubeThumbnailScript($args);
    }

    /**
     * Returns the Youtube Thumbnail inline script
     *
     * @param array $args Array of arguments to populate the script options.
     * @return string
     */
    public function getYoutubeThumbnailScript($args = [])
    {
        $defaults = [
            'resolution' => 'hqdefault',
            'lazy_image' => false,
        ];

        $allowed_resolutions = [
            'default'       => [
                'width'  => 120,
                'height' => 90,
            ],
            'mqdefault'     => [
                'width'  => 320,
                'height' => 180,
            ],
            'hqdefault'     => [
                'width'  => 480,
                'height' => 360,
            ],
            'sddefault'     => [
                'width'  => 640,
                'height' => 480,
            ],
            
            'maxresdefault' => [
                'width'  => 1280,
                'height' => 720,
            ],
        ];

        $args['resolution'] = ( isset($args['resolution']) && isset($allowed_resolutions[ $args['resolution'] ]) ) ? $args['resolution'] : 'hqdefault';

        $args = wp_parse_args($args, $defaults);

        $image = '<img src="https://i.ytimg.com/vi/ID/' . $args['resolution'] . '.jpg" width="' . $allowed_resolutions[ $args['resolution'] ]['width'] . '" height="' . $allowed_resolutions[ $args['resolution'] ]['height'] . '">';

        if (isset($args['lazy_image']) && $args['lazy_image']) {
            $image = '<img data-lazy-src="https://i.ytimg.com/vi/ID/' . $args['resolution'] . '.jpg" width="' . $allowed_resolutions[ $args['resolution'] ]['width'] . '" height="' . $allowed_resolutions[ $args['resolution'] ]['height'] . '"><noscript><img src="https://i.ytimg.com/vi/ID/' . $args['resolution'] . '.jpg" width="' . $allowed_resolutions[ $args['resolution'] ]['width'] . '" height="' . $allowed_resolutions[ $args['resolution'] ]['height'] . '"></noscript>';
        }

        return "<script>function lazyLoadThumb(e){var t='{$image}',a='<div class=\"play\"></div>';return t.replace(\"ID\",e)+a}function lazyLoadYoutubeIframe(){var e=document.createElement(\"iframe\"),t=\"https://www.youtube.com/embed/ID?autoplay=1\";t+=0===this.dataset.query.length?'':'&'+this.dataset.query;e.setAttribute(\"src\",t.replace(\"ID\",this.dataset.id)),e.setAttribute(\"frameborder\",\"0\"),e.setAttribute(\"allowfullscreen\",\"1\"),this.parentNode.replaceChild(e,this)}document.addEventListener(\"DOMContentLoaded\",function(){var e,t,a=document.getElementsByClassName(\"rll-youtube-player\");for(t=0;t<a.length;t++)e=document.createElement(\"div\"),e.setAttribute(\"data-id\",a[t].dataset.id),e.setAttribute(\"data-query\", a[t].dataset.query),e.innerHTML=lazyLoadThumb(a[t].dataset.id),e.onclick=lazyLoadYoutubeIframe,a[t].appendChild(e)});</script>";
    }

    /**
     * Inserts the CSS to style the Youtube thumbnail container
     *
     * @param array $args Array of arguments to populate the CSS.
     * @return void
     */
    public function insertYoutubeThumbnailCSS($args = [])
    {
        wp_register_style('rocket-lazyload', false);
        wp_enqueue_style('rocket-lazyload');
        wp_add_inline_style('rocket-lazyload', $this->getYoutubeThumbnailCSS($args));
    }

    /**
     * Returns the CSS for the Youtube Thumbnail
     *
     * @param array $args Array of arguments to populate the CSS.
     * @return string
     */
    public function getYoutubeThumbnailCSS($args = [])
    {
        $defaults = [
            'base_url' => '',
        ];

        $args = wp_parse_args($args, $defaults);

        return '.rll-youtube-player{position:relative;padding-bottom:56.23%;height:0;overflow:hidden;max-width:100%;}.rll-youtube-player iframe{position:absolute;top:0;left:0;width:100%;height:100%;z-index:100;background:0 0}.rll-youtube-player img{bottom:0;display:block;left:0;margin:auto;max-width:100%;width:100%;position:absolute;right:0;top:0;border:none;height:auto;cursor:pointer;-webkit-transition:.4s all;-moz-transition:.4s all;transition:.4s all}.rll-youtube-player img:hover{-webkit-filter:brightness(75%)}.rll-youtube-player .play{height:72px;width:72px;left:50%;top:50%;margin-left:-36px;margin-top:-36px;position:absolute;background:url(' . $args['base_url'] . 'img/youtube.png) no-repeat;cursor:pointer}.wp-has-aspect-ratio .rll-youtube-player{position:absolute;padding-bottom:0;width:100%;height:100%;top:0;bottom:0;left:0;right:0;';
    }

    /**
     * Inserts the CSS needed when Javascript is not enabled to keep the display correct
     */
    public function insertNoJSCSS()
    {
        echo $this->getNoJSCSS();
    }

    /**
     * Returns the CSS to correctly display images when JavaScript is disabled
     *
     * @return string
     */
    public function getNoJSCSS()
    {
        return '<noscript><style id="rocket-lazyload-nojs-css">.rll-youtube-player, [data-lazy-src]{display:none !important;}</style></noscript>';
    }
}
