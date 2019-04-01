<?php
if (!defined('ABSPATH')) {
    exit();
}
?>

<div class="wrap content-shortcodes">
    <h2>
        <?php echo $this->__('Content Shortcodes'); ?>
        <a href="<?php echo $this->add_url(); ?>" class="add-new-h2"><?php echo $this->__('Add New'); ?></a>
        <?php if ($this->mode === 'LIST' && !empty($_GET['s'])) {
            ?>
            <span class="subtitle"><?php echo sprintf($this->__('Search results for "%s"'), $_GET['s']); ?></span>
        <?php }
        ?>
    </h2>

    <?php
    if (!empty($this->message)) {
        ?>
        <div class="<?php echo $this->error ? 'error below-h2' : 'updated'; ?>">
            <p>
                <?php echo $this->error ? sprintf('<strong>%s</strong>: ', $this->__('ERROR')) : ''; ?><?php echo $this->message; ?>
            </p>
        </div>
        <?php
    }
    ?>

    <?php
    switch ($this->mode) {
        case 'LIST':
            require_once($this->main->pluginDIR() . 'templates/personal-pro/content-shortcodes-list.php');
            break;
        case 'EDIT':
            include($this->main->pluginDIR() . 'templates/personal-pro/content-shortcodes-add-edit.php');
            break;
        case 'DELETE':
            include($this->main->pluginDIR() . 'templates/personal-pro/content-shortcodes-delete.php');
            break;
    }
    ?>

</div>