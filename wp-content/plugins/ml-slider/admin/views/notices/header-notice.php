<?php if (!defined('ABSPATH')) die('No direct access allowed'); ?>

<div class="updraft-ad-container updated">
	<div class="updraft_notice_container">
		<div class="updraft_advert_content_left">
			<img src="<?php echo METASLIDER_BASE_URL.'admin/images/'.$image;?>" width="60" height="60" alt="<?php _e('Logo','ml-slider');?>" />
		</div>
		<div class="updraft_advert_content_right">
			<h3 class="updraft_advert_heading"><?php echo $title; ?></h3>
            <div class="updraft-advert-dismiss">
                <a href="#" onclick="jQuery('.updraft-ad-container').slideUp(); jQuery.post(ajaxurl, {action: 'notice_handler', ad_identifier: '<?php echo $dismiss_time;?>', _wpnonce: metaslider_notices.handle_notices_nonce });"><?php echo sprintf(__('Hide this ad (%s)', 'ml-slider'), $hide_time); ?></a>
            </div>
			<p>
				<?php 
					echo $text;

					if (isset($discount_code)) echo ' <strong>' . $discount_code . '</strong>';

					if (!empty($button_link)) {
						echo $this->get_button_link($button_link, $button_meta);
					} 
				?>
            </p>
            <?php $count = count($this->active_ads('header'));
            if ($count > 1) { ?>
                <span class="updraft_ad_meta">ad <?php echo array_search($dismiss_time, array_keys($this->active_ads('header'))) + 1; ?>/<?php echo $count ?></span>
            <?php } ?>
		</div>
	</div>
	<div class="clear"></div>
</div>
