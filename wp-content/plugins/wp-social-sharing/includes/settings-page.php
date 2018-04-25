<?php 
if( ! defined("SS_VERSION") ) {
	header( 'Status: 403 Forbidden' );
	header( 'HTTP/1.1 403 Forbidden' );
	exit;
}
?>
<div id="ss" class="wrap">
	<div class="ss-container">
		<div class="ss-column ss-primary">
			<h2>WP Social Sharing</h2>
			<form id="ss_settings" method="post" action="options.php">
			<?php settings_fields( 'wp_social_sharing' ); ?>
			<table class="form-table">
				<tr valign="top">
					<th scope="row">
						<label><?php _e('Social share button','wp-social-sharing');?></label>
					</th>
					<td>
						<input type="checkbox" id="facebook_share" name="wp_social_sharing[social_options][]" value="facebook" <?php checked( in_array( 'facebook', $opts['social_options'] ), true ); ?> /><label for="facebook_share"><?php echo _e('Facebook','wp-social-sharing')?></label>
						<input type="checkbox" id="twitter_share" name="wp_social_sharing[social_options][]" value="twitter" <?php checked( in_array( 'twitter', $opts['social_options'] ), true ); ?> /><label for="twitter_share"><?php echo _e('Twitter','wp-social-sharing')?></label>
						<input type="checkbox" id="googleplus_share" name="wp_social_sharing[social_options][]" value="googleplus" <?php checked( in_array( 'googleplus', $opts['social_options'] ), true ); ?> /><label for="googleplus_share"><?php echo _e('Google Plus','wp-social-sharing')?></label>
						<br /><input type="checkbox" id="linkedin_share" name="wp_social_sharing[social_options][]" value="linkedin" <?php checked( in_array( 'linkedin', $opts['social_options'] ), true ); ?> /><label for="linkedin_share"><?php echo _e('Linkedin','wp-social-sharing')?></label>
						<input type="checkbox" id="pinterest_share" name="wp_social_sharing[social_options][]" value="pinterest" <?php checked( in_array( 'pinterest', $opts['social_options'] ), true ); ?> /><label for="pinterest_share"><?php echo _e('Pinterest','wp-social-sharing')?></label>
                        <input type="checkbox" id="xing_share" name="wp_social_sharing[social_options][]" value="xing" <?php checked( in_array( 'xing', $opts['social_options'] ), true ); ?> /><label for="xing_share"><?php echo _e('Xing','wp-social-sharing')?></label>
                        <input type="checkbox" id="reddit_share" name="wp_social_sharing[social_options][]" value="reddit" <?php checked( in_array( 'reddit', $opts['social_options'] ), true ); ?> /><label for="reddit_share"><?php echo _e('Reddit','wp-social-sharing')?></label>
					</td>
				</tr>
				<tr valign="top">
					<th>Social Icon order</th>
					<td>
						<div class="dndicon">
							<?php $s_order=get_option('wss_wp_social_sharing');
								  if(empty($s_order)) $s_order='f,t,g,l,p,x,r';
								  $io=explode(',',rtrim($s_order,','));
							foreach ($io as $i){
								switch($i){
									case 'f':
										echo '<div class="s-icon facebook-icon" id="f"></div>';				
										break;
									case 'g':
										echo '<div class="s-icon googleplus-icon" id="g"></div>';
										break;
									case 't':
										echo '<div class="s-icon twitter-icon" id="t"></div>';
										break;
									case 'l':
										echo '<div class="s-icon linkedin-icon" id="l"></div>';	
										break;
									case 'p':
										echo '<div class="s-icon pinterest-icon" id="p"></div>';
										break;
                                    case 'x':
                                        echo '<div class="s-icon xing-icon" id="x"></div>';
										break;
                                    case 'r':
                                    	echo '<div class="s-icon reddit-icon" id="r"></div>';
                                    	break;
								}
							}?>
						</div>
						<br /><small><?php _e('Drag the social icon to change the order. No need to save.', 'wp-social-sharing'); ?></small>
					</td>
				</tr>
				<tr>
					<th><label for="social_icon_position"><?php _e('Social Icon Position','wp-social-sharing');?></label></th>
					<td>
						<select name="wp_social_sharing[social_icon_position]">
							<option value="before" <?php if($opts['social_icon_position'] == 'before') echo "selected='selected'"?>>Before Content</option>
							<option value="after" <?php if($opts['social_icon_position'] == 'after') echo "selected='selected'"?>>After Content</option>
						</select>
					</td>
				</tr>
				<tr valign="top">
					<th><label for="alws_show_icons"><?php _e('Always show social icons','wp-social-sharing');?></label></th>
					<td>
						<input type="checkbox" id="alws_show_icons" name="wp_social_sharing[show_icons]" value="1" <?php checked(  '1', $opts['show_icons'], true ); ?> />
					</td>
				</tr>
				<tr valign="top">
					<th><label for="before_button_text"><?php _e('Text before Sharing buttons','wp-social-sharing');?></label></th>
					<td>
						<input type="text" class="widefat" name="wp_social_sharing[before_button_text]" id="before_button_text" value="<?php echo esc_attr($opts['before_button_text']); ?>" /> 
					</td>
				</tr>
				<tr valign="top">
					<th><label for="before_button_text"><?php _e('Text Position','wp-social-sharing');?></label></th>
					<td>
						<select name="wp_social_sharing[text_position]">
							<option value="left" <?php if($opts['text_position'] == 'left') echo "selected='selected'"?>>Left</option>
							<option value="right" <?php if($opts['text_position'] == 'right') echo "selected='selected'"?>>Right</option>
							<option value="top" <?php if($opts['text_position'] == 'top') echo "selected='selected'"?>>Top</option>
							<option value="bottom" <?php if($opts['text_position'] == 'bottom') echo "selected='selected'"?>>Bottom</option>
						</select>
					</td>
				</tr>
				<tr valign="top">
					<th><label for="facebook_text"><?php _e('Facebook Share button text','wp-social-sharing');?></label></th>
					<td>
						<input type="text" class="widefat" name="wp_social_sharing[facebook_text]" id="facebook_text" value="<?php echo esc_attr($opts['facebook_text']); ?>" /> 
					</td>
				</tr>
				<tr valign="top">
					<th><label for="twitter_text"><?php _e('Twitter Share button text','wp-social-sharing');?></label></th>
					<td>
						<input type="text" class="widefat" name="wp_social_sharing[twitter_text]" id="twitter_text" value="<?php echo esc_attr($opts['twitter_text']); ?>" /> 
					</td>
				</tr>
				<tr valign="top">
					<th><label for="googleplus_text"><?php _e('Google plus share button text','wp-social-sharing');?></label></th>
					<td>
						<input type="text" name="wp_social_sharing[googleplus_text]" id="googleplus_text" class="widefat" value="<?php echo esc_attr($opts['googleplus_text']); ?>" /> 
					</td>
				</tr>
				<tr valign="top">
					<th><label for="linkedin_text"><?php _e('Linkedin share button text','wp-social-sharing');?></label></th>
					<td>
						<input type="text" name="wp_social_sharing[linkedin_text]" id="linkedin_text" class="widefat" value="<?php echo esc_attr($opts['linkedin_text']); ?>" /> 
					</td>
				</tr>
				<tr valign="top">
					<th><label for="pinterest_text"><?php _e('Pinterest share button text','wp-social-sharing');?></label></th>
					<td>
						<input type="text" name="wp_social_sharing[pinterest_text]" id="pinterest_text" class="widefat" value="<?php echo esc_attr($opts['pinterest_text']); ?>" /> 
					</td>
				</tr>
				<tr valign="top">
					<th><label for="pinterest_image"><?php _e('Default share image','wp-social-sharing')?></label></th>
					<td>
						<input type="text" name="wp_social_sharing[pinterest_image]" id="pinterest_image"  value="<?php echo esc_attr($opts['pinterest_image']); ?>"/><input type="button" class="set_custom_images button" id="set_custom_images" value="<?php _e('Upload','wp-social-sharing')?>" />
						<input type="button" class="button" id="remove_custom_images" value="<?php _e('Remove','wp-social-sharing')?>" />
						<br /><small><?php _e('Required for Pinterest', 'wp-social-sharing'); ?></small>
						<div id="set_custom_image_src"><?php if($opts['pinterest_image'] != ''): ?><img src="<?php echo $opts['pinterest_image'];?>" width="100px" /> <?php endif;?></div>
					</td>
				</tr>
                <tr valign="top">
                	<th><label for="xing_text"><?php _e('Xing share button text','wp-social-sharing');?></label></th>
                    <td>
                    	<input type="text" name="wp_social_sharing[xing_text]" id="xing_text" class="widefat" value="<?php echo esc_attr($opts['xing_text']); ?>" />
                    </td>
                </tr>
                <tr valign="top">
                	<th><label for="reddit_text"><?php _e('Reddit share button text','wp-social-sharing');?></label></th>
                    <td>
                    	<input type="text" name="wp_social_sharing[reddit_text]" id="reddit_text" class="widefat" value="<?php echo esc_attr($opts['reddit_text']); ?>" />
                    </td>
                </tr>
				<tr>
					<th><label><?php _e('Load plugin scripts','wp-social-sharing');?></label></th>
					<td>
						<input type="checkbox" name="wp_social_sharing[load_static][]" id="load_icon_css" value="load_css" <?php checked( in_array( 'load_css', $opts['load_static'] ), true ); ?>><label for="load_icon_css"><?php _e('Load Share button CSS','wp-social-sharing');?></label>
						<input type="checkbox" name="wp_social_sharing[load_static][]" id="load_popup_js" value="load_js"  <?php checked( in_array( 'load_js', $opts['load_static'] ), true ); ?>><label for="load_popup_js"><?php _e('Load Share button JS','wp-social-sharing') ?></label>
					</td>
				</tr>
				
				<tr valign="top">
					<th scope="row">
						<label><?php _e('Automatically add sharing links?', 'wp-social-sharing'); ?></label>
					</th>
					<td>
						<ul>
						<?php foreach( $post_types as $post_type_id => $post_type ) { ?>
							<li>
								<label>
									<input type="checkbox" name="wp_social_sharing[auto_add_post_types][]" value="<?php echo esc_attr( $post_type_id ); ?>" <?php checked( in_array( $post_type_id, $opts['auto_add_post_types'] ), true ); ?>> <?php printf( __(' Auto display to %s', 'wp-social-sharing' ), $post_type->labels->name ); ?>
								</label>
							</li>
						<?php } ?>
						</ul>
						<small><?php _e('Automatically adds the sharing links to the end of the selected post types.', 'wp-social-sharing'); ?></small>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row">
						<label for="ss_twitter_username"><?php _e('Twitter Username', 'wp-social-sharing'); ?></label>
					</th>
					<td>
						<input type="text" name="wp_social_sharing[twitter_username]" id="ss_twitter_username" class="widefat" placeholder="arjun077" value="<?php echo esc_attr($opts['twitter_username']); ?>">
						<small><?php _e('Set this if you want to append "via @yourTwitterUsername" to tweets.', 'wp-social-sharing'); ?></small>
					</td>
				</tr>
			</table>
			<?php
				submit_button();
			?>
		</form>
	</div>

	<div class="ss-column ss-secondary">

		<div class="ss-box">
			<h3 class="ss-title"><?php _e( 'Donate $10, $20 or $50', 'wp-social-sharing' ); ?></h3>
			<p><?php _e( 'If you like this plugin, consider supporting it by donating a token of your appreciation.', 'wp-social-sharing' ); ?></p>
			<div class="ss-donate">
				<form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_top">
					<input type="hidden" name="cmd" value="_s-xclick">
					<input type="hidden" name="encrypted" value="-----BEGIN PKCS7-----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-----END PKCS7-----">
					<button name="submit" class="button-primary"><?php _e( 'Donate with PayPal', 'wp-social-sharing' ); ?></button>
				</form>
			</div>
			<p><?php _e( 'Some other ways to support this plugin', 'wp-social-sharing' ); ?></p>
			<ul class="ul-square">
				<li><a href="http://wordpress.org/support/view/plugin-reviews/wp-social-sharing?rate=5#postform" target="_blank"><?php printf( __( 'Leave a %s review on WordPress.org', 'wp-social-sharing' ), '&#9733;&#9733;&#9733;&#9733;&#9733;' ); ?></a></li>
				<li><a href="http://twitter.com/intent/tweet/?text=<?php echo urlencode('I am using Wordpress "WP Social Sharing" plugin to show social sharing buttons on my WordPress site.'); ?>&via=arjun077&url=<?php echo urlencode('http://wordpress.org/plugins/wp-social-sharing/'); ?>" target="_blank"><?php _e('Tweet about this plugin','wp-social-sharing');?></a></li>
				<li><a href="http://wordpress.org/plugins/wp-social-sharing/#compatibility"  target="_blank"><?php _e( 'Vote "works" on the WordPress.org plugin page', 'wp-social-sharing' ); ?></a></li>
			</ul>
		</div>
		<div class="ss-box">
			<h3 class="ss-title"><?php _e( 'Looking for support?', 'wp-social-sharing' ); ?></h3>
			<p><?php printf( __( 'Please use the %splugin support forums%s on WordPress.org.', 'wp-social-sharing' ), '<a href="#">', '</a>' ); ?></p>
		</div>
		<br style="clear:both; " />
	</div>
</div>
</div>
