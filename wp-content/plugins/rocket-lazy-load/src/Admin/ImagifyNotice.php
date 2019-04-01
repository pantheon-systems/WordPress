<?php
/**
 * Imagify Notice Class
 *
 * @package RocketLazyloadPlugin
 */

namespace RocketLazyLoadPlugin\Admin;

/**
 * Imagify Notice display
 *
 * @since 2.0
 * @author Remy Perona
 */
class ImagifyNotice
{
    /**
     * Template path
     *
     * @since 2.0
     * @author Remy Perona
     *
     * @var string
     */
    private $template_path;

    /**
     * Constructor
     *
     * @since 2.0
     * @author Remy Perona
     *
     * @param string $template_path Template path.
     */
    public function __construct($template_path)
    {
        $this->template_path = $template_path;
    }

    /**
     * Renders the Imagify notice
     *
     * @since 2.0
     * @author Remy Perona
     *
     * @return void
     */
    public function displayNotice()
    {
        $this->renderTemplate('imagify-notice');
    }

    /**
     * Renders the given template if it's readable.
     *
     * @since 2.0
     * @author Remy Perona
     *
     * @param string $template Template name.
     */
    protected function renderTemplate($template)
    {
        $template_path = $this->template_path . $template . '.php';

        if (! is_readable($template_path)) {
            return;
        }

        include $template_path;
    }
}
