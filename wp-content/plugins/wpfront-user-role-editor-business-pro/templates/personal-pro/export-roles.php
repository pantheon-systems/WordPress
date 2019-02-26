<?php
if (!defined('ABSPATH')) {
    exit();
}
?>

<div class="wrap export-roles">
    <h2>
        <?php echo $this->__('Export Roles'); ?>
    </h2>

    <p>
        <?php echo $this->__('Select the roles to be uploaded'); ?>        
    </p>

    <form method="post" action="<?php echo admin_url('admin-ajax.php'); ?>">
        <?php $this->main->create_nonce(); ?>
        <table>
            <tbody>
                <?php
                foreach ($this->roles as $key => $value) {
                    ?>
                    <tr>
                        <td>
                            <label>
                                <input type="checkbox" name="export-roles[<?php echo $key; ?>]" checked="true" />
                                <?php echo $value; ?>
                            </label>
                        </td>
                    </tr>
                    <?php
                }
                ?>
            </tbody>
        </table>

        <input type="hidden" name="action" value="wpfront_user_role_editor_export_roles" />

        <p class="submit">
            <input type="submit" name="exportroles" id="exportroles" class="button button-primary" value="<?php echo $this->__('Download Export File'); ?>" />
        </p>

    </form>

</div>