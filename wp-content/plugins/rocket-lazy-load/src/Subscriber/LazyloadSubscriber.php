<?php
/**
 * Lazyload subscriber
 *
 * @package RocketLazyloadPlugin
 */

namespace RocketLazyLoadPlugin\Subscriber;

defined('ABSPATH') || die('Cheatin\' uh?');

use RocketLazyLoadPlugin\EventManagement\SubscriberInterface;
use RocketLazyLoadPlugin\Options\OptionArray;
use RocketLazyload\Assets;
use RocketLazyload\Image;
use RocketLazyload\Iframe;

/**
 * Lazyload Subscriber
 *
 * @since 2.0
 * @author Remy Perona
 */
class LazyloadSubscriber implements SubscriberInterface
{
    /**
     * OptionArray instance
     *
     * @since 2.0
     * @author Remy Perona
     *
     * @var OptionArray
     */
    private $option_array;

    /**
     * Assets instance
     *
     * @since 2.0
     * @author Remy Perona
     *
     * @var Assets
     */
    private $assets;

    /**
     * Image instance
     *
     * @since 2.0
     * @author Remy Perona
     *
     * @var Image
     */
    private $image;

    /**
     * Iframe instance
     *
     * @since 2.0
     * @author Remy Perona
     *
     * @var Iframe
     */
    private $iframe;

    /**
     * Constructor
     *
     * @since 2.0
     * @author Remy Perona
     *
     * @param OptionArray $option_array OptionArray instance.
     * @param Assets      $assets Assets instance.
     * @param Image       $image Image instance.
     * @param Iframe      $iframe Iframe instance.
     */
    public function __construct(OptionArray $option_array, Assets $assets, Image $image, Iframe $iframe)
    {
        $this->option_array = $option_array;
        $this->assets       = $assets;
        $this->image        = $image;
        $this->iframe       = $iframe;
    }

    /**
     * @inheritDoc
     */
    public function getSubscribedEvents()
    {
        return [
            'wp_footer'            => [
                [ 'insertLazyloadScript', ROCKET_LL_INT_MAX ],
                ['insertYoutubeThumbnailScript', ROCKET_LL_INT_MAX ],
            ],
            'wp_head'              => ['insertNoJSStyle', ROCKET_LL_INT_MAX ],
            'wp_enqueue_scripts'   => ['insertYoutubeThumbnailStyle', ROCKET_LL_INT_MAX],
            'template_redirect'    => ['lazyload', ROCKET_LL_INT_MAX],
            'rocket_lazyload_html' => 'lazyloadResponsive',
            'init'                 => 'lazyloadSmilies',
        ];
    }

    /**
     * Inserts the lazyload script in the footer
     *
     * @since 2.0
     * @author Remy Perona
     *
     * @return void
     */
    public function insertLazyloadScript()
    {
        if (! $this->option_array->get('images') && ! $this->option_array->get('iframes')) {
            return;
        }

        if (! $this->shouldLazyload()) {
            return;
        }

        /**
         * Filters the threshold at which lazyload is triggered
         *
         * @since 1.2
         * @author Remy Perona
         *
         * @param int $threshold Threshold value.
         */
        $threshold = apply_filters('rocket_lazyload_threshold', 300);

        /**
         * Filters the use of the polyfill for intersectionObserver
         *
         * @since 3.3
         * @author Remy Perona
         *
         * @param bool $polyfill True to use the polyfill, false otherwise.
         */
        $polyfill = apply_filters('rocket_lazyload_polyfill', false);

        $args = [
            'base_url'  => ROCKET_LL_FRONT_JS_URL,
            'threshold' => $threshold,
            'version'   => '11.0.3',
            'polyfill'  => $polyfill,
        ];

        if ($this->option_array->get('images')) {
            $args['elements']['image']            = 'img[data-lazy-src]';
            $args['elements']['background_image'] = '.rocket-lazyload';
        }

        if ($this->option_array->get('iframes')) {
            $args['elements']['iframe'] = 'iframe[data-lazy-src]';
        }

        /**
         * Filters the arguments array for the lazyload script options
         *
         * @since 2.0
         * @author Remy Perona
         *
         * @param array $args Arguments used for the lazyload script options.
         */
        $args = apply_filters('rocket_lazyload_script_args', $args);

        $this->assets->insertLazyloadScript($args);
    }

    /**
     * Inserts the Youtube thumbnail script in the footer
     *
     * @since 2.0
     * @author Remy Perona
     *
     * @return void
     */
    public function insertYoutubeThumbnailScript()
    {
        if (! $this->option_array->get('youtube')) {
            return;
        }

        if (! $this->shouldLazyload()) {
            return;
        }

        /**
         * Filters the resolution of the YouTube thumbnail
         *
         * @since 1.4.8
         * @author Arun Basil Lal
         *
         * @param string $thumbnail_resolution The resolution of the thumbnail. Accepted values: default, mqdefault, sddefault, hqdefault, maxresdefault
         */
        $thumbnail_resolution = apply_filters('rocket_lazyload_youtube_thumbnail_resolution', 'hqdefault');

        $this->assets->insertYoutubeThumbnailScript(
            [
                'resolution' => $thumbnail_resolution,
                'lazy_image' => (bool) $this->option_array->get('images'),
            ]
        );
    }

    /**
     * Inserts the no JS CSS compatibility in the header
     *
     * @since 2.0.3
     * @author Remy Perona
     *
     * @return void
     */
    public function insertNoJSStyle()
    {
        if (! $this->shouldLazyload()) {
            return;
        }

        $this->assets->insertNoJSCSS();
    }

    /**
     * Inserts the Youtube thumbnail CSS in the header
     *
     * @since 2.0
     * @author Remy Perona
     *
     * @return void
     */
    public function insertYoutubeThumbnailStyle()
    {
        if (! $this->option_array->get('youtube')) {
            return;
        }

        if (! $this->shouldLazyload()) {
            return;
        }

        $this->assets->insertYoutubeThumbnailCSS(
            [
                'base_url' => ROCKET_LL_ASSETS_URL,
            ]
        );
    }

    /**
     * Checks if lazyload should be applied
     *
     * @since 2.0
     * @author Remy Perona
     *
     * @return bool
     */
    private function shouldLazyload()
    {
        if (is_admin() || is_feed() || is_preview() || ( defined('REST_REQUEST') && REST_REQUEST ) || ( defined('DONOTLAZYLOAD') && DONOTLAZYLOAD )) {
            return false;
        }

        // Exclude Page Builders editors.
        $excluded_parameters = [
            'fl_builder',
            'et_fb',
            'ct_builder',
        ];

        foreach ($excluded_parameters as $excluded) {
            if (isset($_GET[ $excluded ])) {
                return false;
            }
        }

        /**
         * Filters the lazyload application
         *
         * @since 2.0
         * @author Remy Perona
         *
         * @param bool $do_rocket_lazyload True to apply lazyload, false otherwise.
         */
        if (! apply_filters('do_rocket_lazyload', true)) { // WPCS: prefix ok.
            return false;
        }

        return true;
    }

    /**
     * Gets the content to lazyload
     *
     * @since 2.0
     * @author Remy Perona
     *
     * @return void
     */
    public function lazyload()
    {
        if (! $this->shouldLazyload()) {
            return;
        }

        ob_start([$this, 'lazyloadBuffer']);
    }

    /**
     * Applies lazyload on the provided content
     *
     * @since 2.0
     * @author Remy Perona
     *
     * @param string $html HTML content.
     * @return string
     */
    public function lazyloadBuffer($html)
    {
        $buffer = $this->ignoreScripts($html);
        $buffer = $this->ignoreNoscripts($buffer);

        if ($this->option_array->get('images')) {
            $html = $this->image->lazyloadImages($html, $buffer);
            $html = $this->image->lazyloadPictures($html, $buffer);
            $html = $this->image->lazyloadBackgroundImages($html, $buffer);
        }

        if ($this->option_array->get('iframes')) {
            $args = [
                'youtube' => $this->option_array->get('youtube'),
            ];

            $html = $this->iframe->lazyloadIframes($html, $buffer, $args);
        }

        return $html;
    }

    /**
     * Applies lazyload on responsive images attributes srcset and sizes
     *
     * @since 2.0
     * @author Remy Perona
     *
     * @param string $html Image HTML.
     * @return string
     */
    public function lazyloadResponsive($html)
    {
        return $this->image->lazyloadResponsiveAttributes($html);
    }

    /**
     * Applies lazyload on WordPress smilies
     *
     * @since 2.0
     * @author Remy Perona
     *
     * @return void
     */
    public function lazyloadSmilies()
    {
        if (! $this->shouldLazyload()) {
            return;
        }

        if (! $this->option_array->get('images')) {
            return;
        }

        $filters = [
            'the_content'  => 10,
            'the_excerpt'  => 10,
            'comment_text' => 20,
        ];
        
        foreach ($filters as $filter => $prio) {
            if (! has_filter($filter)) {
                continue;
            }

            remove_filter($filter, 'convert_smilies', $prio);
            add_filter($filter, [$this->image, 'convertSmilies'], $prio);
        }
    }

    /**
     * Remove inline scripts from the HTML to parse
     *
     * @param string $html HTML content.
     * @return string
     */
    private function ignoreScripts($html)
    {
        return preg_replace('/<script\b(?:[^>]*)>(?:.+)?<\/script>/Umsi', '', $html);
    }

    /**
     * Remove noscript tags from the HTML to parse
     *
     * @param string $html HTML content.
     * @return string
     */
    private function ignoreNoscripts($html)
    {
        return preg_replace('#<noscript>(?:.+)</noscript>#Umsi', '', $html);
    }
}
