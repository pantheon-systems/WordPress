<?php
if (!defined('ABSPATH')) {
    exit();
}
?>

<form method="post" action="<?php echo $this->delete_url(); ?>">
    <?php $this->main->create_nonce('_shortcodes'); ?>
    <p><?php echo $this->__('You have specified these shortcodes for deletion'); ?>:</p>
    <ul>
        <?php
        $data = array();

        if (!empty($this->ID)) {
            if (!is_array($this->ID)) {
                $this->ID = array($this->ID);
                $this->name = array($this->name);
                $this->shortcode = array($this->shortcode);
            }

            for ($i = 0; $i < count($this->ID); $i++) {
                $data[] = (OBJECT) array(
                            'id' => $this->ID[$i],
                            'name' => $this->name[$i],
                            'shortcode' => $this->shortcode[$i]
                );
            }
        }

        foreach ($data as $value) {
            ?>
            <li>
                <?php
                printf('%s: <strong>%s</strong> [<strong>%s</strong>]', $this->__('Shortcode'), $value->name, $value->shortcode);
                ?>
                <input type="hidden" name="delete-shortcode[<?php echo $value->id; ?>]" value="1" />
            </li>
            <?php
        }
        ?>
    </ul>
    <p class="submit">
        <input type="submit" name="confirm-delete" id="submit" class="button" value="<?php echo $this->__('Confirm Deletion'); ?>" <?php echo empty($data) ? 'disabled' : ''; ?>>
    </p>
</form>