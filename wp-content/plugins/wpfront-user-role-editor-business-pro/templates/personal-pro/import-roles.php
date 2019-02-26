<?php
if (!defined('ABSPATH')) {
    exit();
}
?>

<div class="wrap import-roles">
    <h2>
        <?php echo $this->__('Import Roles'); ?>
    </h2>

    <?php
    if ($this->result != NULL) {
        if ($this->result->success) {
            ?>
            <div class="updated">
                <p><?php echo $this->result->message; ?></p>
            </div>
        <?php } else { ?>
            <div class="error">
                <p><?php echo $this->__('ERROR:') . ' ' . $this->result->message; ?></p>
            </div>
            <?php
        }
    }
    if ($this->import_data === NULL) {
        ?>

        <p>
            <?php echo $this->__('Choose a WPFront User Role Editor export file (.xml) to upload, then click Upload file and import.'); ?>
        </p>

        <?php
        wp_import_upload_form(admin_url('admin.php') . '?page=' . self::MENU_SLUG);
    } else {
        ?>
        <p>
            <?php
            printf($this->__('Roles exported from %s on %s by user %s.'), '<a href="' . $this->import_data->source_url . '">' . $this->import_data->source . '</a>', date('D, d M Y h:i:s A', strtotime($this->import_data->date)) . ' ' . $this->__('UTC'), $this->import_data->user_display_name);
            ?>
        </p>
        <?php
        if (empty($this->roles_data)) {
            ?>
            <p>
                <?php echo $this->__('Zero roles found in this export file to import.'); ?>
            </p>
            <?php
        } else {
            ?>
            <p>
                <?php echo $this->__('Select roles to import'); ?>
            </p>
            <form method="POST">
                <?php $this->main->create_nonce(); ?>
                <table>
                    <tbody>
                        <?php
                        $override = FALSE;
                        foreach ($this->roles_data as $key => $value) {
                            $override = $override || $value->override;
                            ?>
                            <tr>
                                <td>
                                    <label class="<?php echo $value->override ? 'override' : ''; ?>">
                                        <input type="checkbox" name="import-roles[<?php echo $key; ?>]" <?php echo $value->override ? '' : 'checked'; ?> />
                                        <?php echo $value->display_name; ?>
                                        <sup>*</sup>
                                    </label>
                                </td>
                            </tr>
                            <?php
                        }
                        ?>
                    </tbody>
                </table>
                <?php
                if ($override) {
                    ?>
                    <p class="override">
                        <?php echo $this->__('* These roles already exist in this site. Importing them will overwrite existing roles.'); ?>
                    </p>
                    <?php
                }
                ?>
                <input type="hidden" name="file-id" value="<?php echo $this->file_id; ?>" />
                <p class="submit">
                    <input type="submit" name="importroles" id="importroles" class="button button-primary" value="<?php echo $this->__('Import Roles'); ?>" />
                </p>
            </form>
            <?php
        }
    }
    ?>
</div>