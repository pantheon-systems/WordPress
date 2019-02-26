<?php
if (!defined('ABSPATH')) {
    exit();
}
?>

<form method="post" class="validate">
    <?php $this->main->create_nonce('_shortcodes'); ?>
    <input type="hidden" name="id" value="<?php echo $this->ID; ?>" />
    <table class="form-table">
        <tbody>
            <tr class="form-required <?php echo $this->nameValid ? '' : 'form-invalid'; ?>">
                <th scope="row">
                    <label for="shortcode_name">
                        <?php echo $this->__('Name'); ?> <span class="description">(<?php echo $this->__('required'); ?>)</span>
                    </label>
                </th>
                <td>
                    <input class="regular-text" id="shortcode_name" name="name" type="text" aria-required="true" value="<?php echo $this->name; ?>" />
                </td>
            </tr>
            <tr class="form-required <?php echo $this->shortcodeValid ? '' : 'form-invalid'; ?>">
                <th scope="row">
                    <label for="shortcode_shortcode">
                        <?php echo $this->__('Shortcode'); ?> <span class="description">(<?php echo $this->__('required') . ' & ' . $this->__('unique'); ?>)</span>
                    </label>
                </th>
                <td>
                    <input class="regular-text" name="shortcode" type="text" id="shortcode_shortcode" aria-required="true" value="<?php echo $this->shortcode; ?>"  />
                    <br />
                    <span class="description">(<?php echo $this->__('Allowed characters: lowercase letters, numbers and underscore.'); ?>)</span>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="shortcode_user_type">
                        <?php echo $this->__('User Type'); ?>
                    </label>
                </th>
                <td>
                    <fieldset>
                        <label><input name="user_type" type="radio" value="<?php echo self::USER_TYPE_ALL; ?>" <?php echo $this->userType === self::USER_TYPE_ALL ? 'checked' : ''; ?> /><?php echo $this->__('All Users'); ?></label>
                        <br />
                        <label><input name="user_type" type="radio" value="<?php echo self::USER_TYPE_LOGGED_IN; ?>" <?php echo $this->userType === self::USER_TYPE_LOGGED_IN ? 'checked' : ''; ?> /><?php echo $this->__('Logged-in Users'); ?></label>
                        <br />
                        <label><input name="user_type" type="radio" value="<?php echo self::USER_TYPE_GUEST; ?>" <?php echo $this->userType === self::USER_TYPE_GUEST ? 'checked' : ''; ?> /><?php echo $this->__('Guest Users'); ?></label>
                        <br />
                        <label><input name="user_type" type="radio" value="<?php echo self::USER_TYPE_ROLES; ?>" <?php echo $this->userType === self::USER_TYPE_ROLES ? 'checked' : ''; ?> /><?php echo $this->__('Users in Roles'); ?></label>
                        <div class="<?php echo $this->userType === self::USER_TYPE_ROLES ? '' : 'hidden'; ?>">
                            <?php
                            $roles = $this->get_roles();

                            foreach ($roles as $key => $value) {
                                ?>
                                <label><input type="checkbox" name="selected-roles[<?php echo $key; ?>]" <?php echo in_array($key, $this->roles) ? 'checked' : ''; ?> /><?php echo $value; ?></label>
                                <br />
                                <?php
                            }
                            ?>
                        </div>
                    </fieldset>
                </td>
            </tr>
        </tbody>
    </table>
    <p class="submit">
        <input type="submit" id="add-edit-shortcode" name="add-edit-shortcode" class="button button-primary" value="<?php echo $this->__('Submit'); ?>" />
    </p>
</form>
<?php $this->footer(); ?>

<script type="text/javascript">

    (function ($) {

        var $container = $('div.wrap.content-shortcodes');

        $container.find('input[name="user_type"]').change(function () {
            if ($(this).val() == '<?php echo self::USER_TYPE_ROLES; ?>') {
                $(this).closest('fieldset').find('div').removeClass('hidden');
            } else {
                $(this).closest('fieldset').find('div').addClass('hidden');
            }
        });

    })(jQuery);

</script>