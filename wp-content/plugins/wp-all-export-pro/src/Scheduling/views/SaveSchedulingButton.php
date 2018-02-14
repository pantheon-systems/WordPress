<div class="wpae-save-button button button-primary button-hero wpallexport-large-button"
     style="position: relative; width: 285px; margin-left: 5px;">
    <svg width="30" height="30" viewBox="0 0 1792 1792"
         xmlns="http://www.w3.org/2000/svg"
         style="fill: white; display: none;">
        <path
                d="M1671 566q0 40-28 68l-724 724-136 136q-28 28-68 28t-68-28l-136-136-362-362q-28-28-28-68t28-68l136-136q28-28 68-28t68 28l294 295 656-657q28-28 68-28t68 28l136 136q28 28 28 68z"
                fill="white"/>
    </svg>
    <div class="easing-spinner" style="display: none;">
        <div class="double-bounce1"></div>
        <div class="double-bounce2"></div>
    </div>
    <div class="save-text"
         style="display: block; position:absolute; <?php if($this->isWizard) {?> left: 70px; <?php } else { ?> left: 60px; <?php } ?> top:0; user-select: none;">
        <?php if($this->isWizard) {?>
            <?php _e('Confirm & Run Export', 'wp_all_export_plugin'); ?>
        <?php } else { ?>
            <?php _e('Save Export Configuration', 'wp_all_export_plugin'); ?>
        <?php } ?>
    </div>
</div>