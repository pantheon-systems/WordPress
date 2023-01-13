<?php


class OceanWP_Freemius_Custom_Section extends WP_Customize_Section
{
    protected function get_skyrocket_resource_url()
    {
        if (strpos(wp_normalize_path(__DIR__), wp_normalize_path(WP_PLUGIN_DIR)) === 0) {
            // We're in a plugin directory and need to determine the url accordingly.
            return plugin_dir_url(__DIR__);
        }

        return trailingslashit(get_template_directory_uri());
    }
}


class OceanWP_Freemius_Upsell_Section extends OceanWP_Freemius_Custom_Section
{
    public $type = 'skyrocket-upsell';

    /**
     * The Upsell URL
     */
    public $url = '';

    /**
     * Render the section, and the controls that have been added to it.
     */
    protected function render()
    {
?>
        <li id="accordion-section-<?php echo ow_esc_attr($this->id); ?>" class="skyrocket_upsell_section accordion-section control-section control-section-<?php echo ow_esc_attr($this->id); ?> cannot-expand">
            <h3 class="upsell-section-title">
                <a href="<?php echo esc_url($this->url); ?>" target="_blank"><?php echo esc_html($this->title); ?></a>
            </h3>
        </li>
<?php
    }
}
