<?php
if (!defined('ABSPATH')) {
    exit();
}
?>

<div class="wrap bulk-edit">
    <h2>
        <?php echo $this->__('Bulk Edit'); ?>
    </h2>

    <p>
        <?php echo $this->__('Select an option to continue'); ?>        
    </p>

    <form method="get" action="<?php echo $this->get_bulk_edit_url(); ?>">
        <input type="hidden" name="page" value="<?php echo self::MENU_SLUG; ?>" />

        <?php if ($this->can_add_remove_cap) { ?>
            <p>
                <label>
                    <input type="radio" name="bulk-edit-type" checked="true" value="add-remove-capability" /><?php echo $this->__('Add or remove capability'); ?>       
                </label>
            </p>
        <?php } ?>
        <?php if ($this->can_extended_perm) { ?>
            <p>
                <label>
                    <input type="radio" name="bulk-edit-type" value="extended-permissions" /><?php echo $this->__('Extended permissions'); ?>       
                </label>
            </p>
        <?php } ?>

        <p class="submit">
            <input type="submit" id="next-step" class="button button-primary" value="<?php echo $this->__('Next Step'); ?>" />
        </p>
    </form>

</div>