<?php

namespace Wpae\Pro\Filtering;

/**
 * Class FilteringFactory
 * @package Wpae\Pro\Filtering
 */
class FilteringFactory
{
    public static function getFilterEngine()
    {
        return new FilteringFree();
    }

    public static function render_filtering_block( $engine, $isWizard, $post, $is_on_template_screen = false )
    {

        if ( $isWizard or $post['export_type'] != 'specific' ) return;

        ?>
        <div class="wpallexport-collapsed wpallexport-section closed">
            <div class="wpallexport-content-section wpallexport-filtering-section" <?php if ($is_on_template_screen):?>style="margin-bottom: 10px;"<?php endif; ?>>
                <div class="wpallexport-collapsed-header" style="padding-left: 25px;">
                    <h3><?php _e('Filtering Options','wp_all_export_plugin');?></h3>
                </div>
                <div class="wpallexport-collapsed-content" style="padding: 0;">
                    <div class="wpallexport-collapsed-content-inner">
                        <?php include_once PMXE_ROOT_DIR . '/views/admin/export/blocks/filters.php'; ?>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }
}