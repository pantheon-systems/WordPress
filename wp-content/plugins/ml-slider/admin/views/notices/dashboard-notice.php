<?php if (!defined('ABSPATH')) die('No direct access allowed'); ?>

<?php if (isset($mega) && $mega) {?>

<div class="updraft-ad-container updated">
    <h1><?php echo $title; ?></h1>
    <p><?php echo $text; ?>
    <div class="updraft-advert-dismiss">
        <a href="#" onclick="jQuery('.updraft-ad-container').slideUp(); jQuery.post(ajaxurl, {action: 'notice_handler', ad_identifier: '<?php echo $dismiss_time;?>', _wpnonce: metaslider_notices.handle_notices_nonce });"><?php echo sprintf('%s (%s)', __('Dismiss', 'ml-slider'), $hide_time); ?></a>
    </div>
    <?php foreach ($this->mega_notice_parts() as $ad_identifier => $values) { 
        extract($values); ?>
    <div class="mega_list">
        <p><strong><?php echo $title; ?></strong> <?php echo $text;
            if (!empty($button_link)) {
                echo $this->get_button_link($button_link, $button_meta);
            } ?>
        </p>
    </div>
    <div class="clear"></div>
    <?php } ?>
</div>
<?php } else {
    
    // Render a typical header ad
    include METASLIDER_PATH.'admin/views/notices/header-notice.php';

}
