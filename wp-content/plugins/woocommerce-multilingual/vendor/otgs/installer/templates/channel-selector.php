<div class="installer-channel-selector-wrap">
    <label>
        <?php esc_html_e('Updates channel:', 'installer'); ?>
        <select class="installer-channel-selector" data-repository-id="<?php echo $repository_id ?>"<?php if( !$can_switch ): ?> disabled<?php endif ?>>
            <?php foreach( $channels as $channel_id => $channel_name ): ?>
            <option value="<?php echo $channel_id ?>" <?php
                if( $channel_id == $current_channel ): ?>selected="selected"<?php
                endif; ?>><?php echo $channel_name ?></option>
            <?php endforeach ?>
        </select>
        <span class="installer-status-note" <?php if( $can_switch ): ?>style="display:none" <?php endif; ?>>
            <?php _e("To select different update channels (beta, development) you must update your existing products to their most recent stable versions.", 'installer') ?>
        </span>
        <span class="spinner spinner-with-text">
            <?php _e( "Updating the plugins on your site. Please don't close this page or navigate away.", 'installer' ); ?>
        </span>
        <span class="installer-channel-update-ok" data-text="<?php
            esc_attr_e( sprintf("Update completed. You are now using the %s channel of %s.",
	        '%CHANNEL%', WP_Installer()->get_generic_product_name( $repository_id ) ) ) ?>">
        </span>
        <span class="installer-channel-update-fail" data-text-unstable="<?php
        $support_url  = $repository_id ==='toolset' ? 'https://wp-types.com/forums/forum/support-2/' : 'https://wpml.org/forums/forum/english-support/';
        $download_url = $repository_id ==='toolset' ? 'https://wp-types.com/account/downloads/' : 'https://wpml.org/account/downloads/';
        echo esc_attr( sprintf(
            __( "Something went wrong and we could not install all updates from the %s channel. Click here to %stry again%s. If the errors persist, please switch back to the Production channel and contact the %s%s support%s.", 'installer' ),
	        '%CHANNEL%',
	        '<a href="#" class="installer-channel-retry"><strong>',
	        '</strong></a>',
            '<a href="' . $support_url . '">', WP_Installer()->get_generic_product_name( $repository_id ), '</a>'
        ) );
        ?>" data-text-stable="<?php
        echo esc_attr( sprintf(
            esc_attr__( "There was a problem switching to the %s channel. You can %sretry%s. If the problem continues, please %sdownload %s%s and install again manually", 'installer' ),
	        '%CHANNEL%',
	        '<a href="#" class="installer-channel-retry"><strong>',
	        '</strong></a>',
	        '<a href="' . $download_url . '">', WP_Installer()->get_generic_product_name( $repository_id ), '</a>'
        ) );
        ?>">
        </span>
        <input type="hidden" class="nonce" value="<?php echo $nonce ?>" />
    </label>

	<?php if( !$no_prompt ): ?>
    <div class="installer-warn-box installer-switch-confirmation" style="margin-top: 10px; display: none">
        <p class="alignright">
            <button class="button-secondary js-cancel"><?php _e("Cancel", 'installer') ?></button>
            <button class="button-primary js-proceed"><?php _e("Switch", 'installer') ?></button>
        </p>
        <p>
            <?php _e("Plugins will be updated to their most advanced version in the channel that you selected.", 'installer') ?>
        </p>
        <label>
            <input type="checkbox" value="1" class="js-remember"/>
                &nbsp;<?php _e("Remember my preference.", 'installer') ?><br />
        </label>
    </div>
    <?php endif; ?>

    <div class="installer-warn-text" <?php if( WP_Installer_Channels()->get_channel( $repository_id ) <= 1 ): ?>style="display:none" <?php endif; ?>>
        <?php printf(
                __("You are using a potentially less stable channel for %s. If you didn't enable this on purpose, you should switch to the 'Production' channel.", 'installer'),
	            WP_Installer()->get_generic_product_name( $repository_id )
            );
        ?>
    </div>

</div>