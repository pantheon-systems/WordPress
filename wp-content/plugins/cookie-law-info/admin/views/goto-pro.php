<style>
.cli-button-go-pro {
    box-shadow: none;
    border: 0;
    text-shadow: none;
    padding: 10px 15px;
    height: auto;
    font-size: 18px;
    border-radius: 4px;
    font-weight: 600;
    background: #00cb95;
    margin-top: 20px;
    text-decoration: none;
}

.cli-button {
    margin-bottom: 20px;
    color: #fff;
}
.cli-button:hover, .cli-button:visited {
    color: #fff;
}
.cli_gopro_block{ background: #fff; float: left; height:auto; padding: 15px; box-shadow: 0px 2px 2px #ccc; margin-top: 20px; width: 100%; }
.cli_gopro_block h3{ text-align: center; }
.cli_premium_features{ padding-left: 20px; }
.cli_premium_features li{ padding-left:15px; padding-right: 15px; }
.cli_premium_features li::before {
    font-family: dashicons;
    text-decoration: inherit;
    font-weight: 400;
    font-style: normal;
    vertical-align: top;
    text-align: center;
    content: "\f147";
    margin-right: 10px;
    margin-left: -25px;
    font-size: 16px;
    color: #3085bb;
}
.cli-button-documentation{
    border: 0;
    background: #d8d8dc;
    box-shadow: none;
    padding: 10px 15px;
    font-size: 15px;
    height: auto;
    margin-left: 10px;
    margin-right: 10px;
    margin-top: 10px;
    border-radius: 3px;
    text-decoration: none;
}
</style>

<div id="cli-plugin-migrate">
    <h3><?php echo __('Where did my settings go?','cookie-law-info'); ?></h3>
    <p><?php echo __('Cookie Law Info version 0.9 has been updated and has new settings.','cookie-law-info'); ?> <strong><?php echo __('Your previous settings are safe.','cookie-law-info'); ?></strong></p>
    <p><?php echo __('You can either copy over your old settings to this version, or use the new default values.','cookie-law-info'); ?> </p>
    <form method="post" action="<?php esc_url($_SERVER["REQUEST_URI"]) ?>">
        <p><label for="cli-migration"><?php echo __('Would you like to:','cookie-law-info'); ?></label></p>
        <ul>
            <li><input type="radio" id="cli-migration_field_yes" name="cli-migration_field" class="styled" value="2" /> <?php echo __('Use previous settings','cookie-law-info'); ?></li>
            <li><input type="radio" id="cli-migration_field_yes" name="cli-migration_field" class="styled" value="3" checked="checked" /> <?php echo __('Start afresh with the new version','cookie-law-info'); ?></li>
        </ul>
        <input type="submit" name="cli-migration-button" value="Update" class="button-secondary" onclick="return confirm('Are you sure you want to migrate settings?');" />
    </form>
    <p><?php echo __('If you want to go back to the previous version you can always download it again from','cookie-law-info'); ?> <a href="http://www.cookielawinfo.com">CookieLawInfo.com.</a></p>
</div>

<div class="cli_gopro_block" style="margin-top: 43px;">
    <p style="text-align: center;">
        <a href="https://www.webtoffee.com/product/gdpr-cookie-consent/" target="_blank" class="cli-button cli-button-go-pro"><?php echo __('Upgrade to GDPR Pro','cookie-law-info'); ?></a>
    </p>
    <p>
        <br/>
        <ul class="cli_premium_features">
            <li><?php echo __('Manage list of cookies ( Name, CookieID, Description, Duration, Type, Category, Header Script, Footer Script).','cookie-law-info'); ?></li>
            <li><?php echo __('Manage Cookie Categories.','cookie-law-info'); ?></li>
            <li><?php echo __('Scan and auto block scripts - Google Analytics, FB Pixel, Google tag manager.','cookie-law-info'); ?></li>
            <li><?php echo __("Location based exclusion of cookie notice for EU countries.","cookie-law-info"); ?></li>
            <li><?php echo __("Provide the users with the granular control over the cookies/scipts used by the website.","cookie-law-info"); ?></li>
            <li><?php echo __("Optionally log users' consent and export into a CSV file.","cookie-law-info"); ?></li>
            <li><?php echo __("Customize the privacy overview in the cookie settings popup.","cookie-law-info"); ?></li>
            <li><?php echo __("Export/import cookies and related information via CSV file.","cookie-law-info"); ?></li>
            <li><?php echo __("Scan and import/export the cookies in the homepage.","cookie-law-info"); ?></li>
            <li><?php echo __('Allow to display cookie settings popup where site visitors can opt-in or give consent to cookie categories.','cookie-law-info'); ?></li>
            <li><?php echo __('Customisable to look just like your own website\'s style: customise the colours, styles and fonts.','cookie-law-info'); ?></li>
            <li><?php echo __('Put the cookie bar in either the header or the footer.','cookie-law-info'); ?></li>
        </ul>
        <br/>
    </p>
    <p style="text-align: center;">
        <a href="http://cookielawinfo.com/user-guide/" target="_blank" class="cli-button cli-button-documentation" style=" color: #555 !important;"><?php echo __('Documentation','cookie-law-info'); ?></a>
    </p>
</div>

<div class="cli_gopro_block">
    <h3 style="text-align: center;"><?php echo __('Like this plugin?','cookie-law-info'); ?></h3>
    <p><?php echo __('If you find this plugin useful please show your support and rate it','cookie-law-info'); ?> <a href="http://wordpress.org/support/view/plugin-reviews/cookie-law-info" target="_blank" style="color: #ffc600; text-decoration: none;">★★★★★</a><?php echo __(' on','cookie-law-info'); ?> <a href="http://wordpress.org/support/view/plugin-reviews/cookie-law-info" target="_blank">WordPress.org</a> -<?php echo __('  much appreciated!','cookie-law-info'); ?> :)</p>
</div>

<!-- <div>
    <form action="http://cookielawinfo.us5.list-manage.com/subscribe/post?u=b32779d828ef2e37e68e1580d&amp;id=71af66b86e" method="post" id="mc-embedded-subscribe-form" name="mc-embedded-subscribe-form" class="validate" target="_blank">
            <h3><label for="mce-EMAIL">Subscribe to our mailing list</label></h3>
            <p>Occasional updates on plugin updates, compliance requirements, who's doing what and industry best practice.</p>
            <input type="email" value="" name="EMAIL" class="vvv_textfield" id="mce-EMAIL" placeholder="email address" required>
            <div class="">
                    <input type="submit" value="Subscribe" name="subscribe" id="mc-embedded-subscribe" class="button-secondary">
            </div>
            <p>We will not send you spam or pass your details to 3rd Parties.</p>
    </form>
</div>-->
<!--End mc_embed_signup-->