<?php
if (!defined('ABSPATH')) {
    exit();
}

require_once($this->main->pluginDIR() . 'classes/personal-pro/class-wpfront-user-role-editor-content-shortcodes-list-table.php');

$table = new WPFront_User_Role_Editor_Content_Shortcodes_List_Table($this);
$table->prepare_items();
$table->views();
?>

<form action="" method="get" class="search-form">
    <input type="hidden" name="page" value="<?php echo WPFront_User_Role_Editor_Content_Shortcodes::MENU_SLUG; ?>" />
    <?php $table->search_box($this->__('Search'), 'content-shortcodes'); ?>
</form>

<form id="form-content-shortcodes" method='post'>
    <?php
    $this->main->create_nonce('_shortcodes');
    $table->display();
    ?>
</form>

