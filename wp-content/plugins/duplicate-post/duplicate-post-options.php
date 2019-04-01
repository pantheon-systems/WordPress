<?php
/**
 * Add an option page
 */
if ( !defined( 'ABSPATH' ) ) {
	exit;
}

if ( is_admin() ){ // admin actions
	add_action( 'admin_menu', 'duplicate_post_menu' );
	add_action( 'admin_init', 'duplicate_post_register_settings' );
}

function duplicate_post_register_settings() { // whitelist options
	register_setting( 'duplicate_post_group', 'duplicate_post_copytitle');
	register_setting( 'duplicate_post_group', 'duplicate_post_copydate');
	register_setting( 'duplicate_post_group', 'duplicate_post_copystatus');
	register_setting( 'duplicate_post_group', 'duplicate_post_copyslug');
	register_setting( 'duplicate_post_group', 'duplicate_post_copyexcerpt');
	register_setting( 'duplicate_post_group', 'duplicate_post_copycontent');
	register_setting( 'duplicate_post_group', 'duplicate_post_copythumbnail');
	register_setting( 'duplicate_post_group', 'duplicate_post_copytemplate');
	register_setting( 'duplicate_post_group', 'duplicate_post_copyformat');
	register_setting( 'duplicate_post_group', 'duplicate_post_copyauthor');
	register_setting( 'duplicate_post_group', 'duplicate_post_copypassword');
	register_setting( 'duplicate_post_group', 'duplicate_post_copyattachments');
	register_setting( 'duplicate_post_group', 'duplicate_post_copychildren');
	register_setting( 'duplicate_post_group', 'duplicate_post_copycomments');
	register_setting( 'duplicate_post_group', 'duplicate_post_copymenuorder');
	register_setting( 'duplicate_post_group', 'duplicate_post_blacklist');
	register_setting( 'duplicate_post_group', 'duplicate_post_taxonomies_blacklist');
	register_setting( 'duplicate_post_group', 'duplicate_post_title_prefix');
	register_setting( 'duplicate_post_group', 'duplicate_post_title_suffix');
	register_setting( 'duplicate_post_group', 'duplicate_post_increase_menu_order_by');
	register_setting( 'duplicate_post_group', 'duplicate_post_roles');
	register_setting( 'duplicate_post_group', 'duplicate_post_types_enabled');
	register_setting( 'duplicate_post_group', 'duplicate_post_show_row');
	register_setting( 'duplicate_post_group', 'duplicate_post_show_adminbar');
	register_setting( 'duplicate_post_group', 'duplicate_post_show_submitbox');
	register_setting( 'duplicate_post_group', 'duplicate_post_show_bulkactions');
	register_setting( 'duplicate_post_group', 'duplicate_post_show_notice');	
}


function duplicate_post_menu() {
	add_options_page(__("Duplicate Post Options", 'duplicate-post'), __("Duplicate Post", 'duplicate-post'), 'manage_options', 'duplicatepost', 'duplicate_post_options');
}

function duplicate_post_options() {

	if ( current_user_can( 'promote_users' ) && (isset($_GET['settings-updated'])  && $_GET['settings-updated'] == true)){
		global $wp_roles;
		$roles = $wp_roles->get_names();

		$dp_roles = get_option('duplicate_post_roles');
		if ( $dp_roles == "" ) $dp_roles = array();

		foreach ($roles as $name => $display_name){
			$role = get_role($name);

			// role should have at least edit_posts capability
			if ( !$role->has_cap('edit_posts') ) continue;

			/* If the role doesn't have the capability and it was selected, add it. */
			if ( !$role->has_cap( 'copy_posts' )  && in_array($name, $dp_roles) )
				$role->add_cap( 'copy_posts' );

			/* If the role has the capability and it wasn't selected, remove it. */
			elseif ( $role->has_cap( 'copy_posts' ) && !in_array($name, $dp_roles) )
			$role->remove_cap( 'copy_posts' );
		}
	}
	?>
<div class="wrap">
	<div id="icon-options-general" class="icon32">
		<br>
	</div>
	<h1>
		<?php esc_html_e("Duplicate Post Options", 'duplicate-post'); ?>
	</h1>
	
	<div
		style="margin: 9px 15px 4px 0; padding: 5px 30px; text-align: center; float: left; clear:left; border: solid 3px #cccccc; width: 600px;">
		<p>
		<?php esc_html_e('Help me develop the plugin, add new features and improve support!', 'duplicate-post'); ?>
		<br/>
		<?php esc_html_e('Donate whatever sum you choose, even just 10¢.', 'duplicate-post'); ?>
		<br/>
		<a href="https://duplicate-post.lopo.it/donate"><img id="donate-button" style="margin: 0px auto;" src="<?php echo plugins_url( 'donate.png', __FILE__ ); ?>" alt="Donate"/></a>
		<br/>
		<a href="https://duplicate-post.lopo.it/"><?php esc_html_e('Documentation', 'duplicate-post'); ?></a>
		 - <a href="https://translate.wordpress.org/projects/wp-plugins/duplicate-post"><?php esc_html_e('Translate', 'duplicate-post'); ?></a>		 
		 - <a href="https://wordpress.org/support/plugin/duplicate-post"><?php esc_html_e('Support Forum', 'duplicate-post'); ?></a>
		</p>
	</div>
		

	<script>
	jQuery(document).on( 'click', '.nav-tab-wrapper a', function() {
		jQuery('.nav-tab').removeClass('nav-tab-active');
		jQuery(this).addClass('nav-tab-active');
		jQuery('section').hide();
		jQuery('section').eq(jQuery(this).index()).show();	
		return false;
	});

	function toggle_private_taxonomies(){
		jQuery('.taxonomy_private').toggle(300);
	}

	
	jQuery(function(){
		jQuery('.taxonomy_private').hide(300);
	});
	
	</script>

	<style>
h2.nav-tab-wrapper {
	margin: 22px 0 0 0;
}

h2 .nav-tab:focus {
	color: #555;
	box-shadow: none;
}

#sections {
	padding: 22px;
	background: #fff;
	border: 1px solid #ccc;
	border-top: 0px;
}

section {
	display: none;
}

section:first-of-type {
	display: block;
}

.no-js h2.nav-tab-wrapper {
	display: none;
}

.no-js #sections {
	border-top: 1px solid #ccc;
	margin-top: 22px;
}

.no-js section {
	border-top: 1px dashed #aaa;
	margin-top: 22px;
	padding-top: 22px;
}

.no-js section:first-child {
	margin: 0px;
	padding: 0px;
	border: 0px;
}

label {
	display: block;
}

label.taxonomy_private {
	font-style: italic;
}

a.toggle_link {
	font-size: small;
}
img#donate-button{
	vertical-align: middle;
}
</style>


	<form method="post" action="options.php" style="clear: both">
		<?php settings_fields('duplicate_post_group'); ?>

		<h2 class="nav-tab-wrapper">
			<a class="nav-tab nav-tab-active"
				href="<?php echo admin_url() ?>/index.php?page=duplicate-post-what"><?php esc_html_e('What to copy', 'duplicate-post'); ?>
			</a> <a class="nav-tab"
				href="<?php echo admin_url() ?>/index.php?page=duplicate-post-who"><?php esc_html_e('Permissions', 'duplicate-post'); ?>
			</a> <a class="nav-tab"
				href="<?php echo admin_url() ?>/index.php?page=duplicate-post-where"><?php esc_html_e('Display', 'duplicate-post'); ?>
			</a>
		</h2>

		<section>

			<table class="form-table">
				<tr valign="top">
					<th scope="row"><?php esc_html_e('Post/page elements to copy', 'duplicate-post'); ?>
					</th>
					<td colspan="2"><label> <input type="checkbox"
							name="duplicate_post_copytitle" value="1" <?php  if(get_option('duplicate_post_copytitle') == 1) echo 'checked="checked"'; ?>"/>
							<?php esc_html_e("Title", 'default'); ?>
					</label> <label> <input type="checkbox"
							name="duplicate_post_copydate" value="1" <?php  if(get_option('duplicate_post_copydate') == 1) echo 'checked="checked"'; ?>"/>
							<?php esc_html_e("Date", 'default'); ?>
					</label> <label> <input type="checkbox"
							name="duplicate_post_copystatus" value="1" <?php  if(get_option('duplicate_post_copystatus') == 1) echo 'checked="checked"'; ?>"/>
							<?php esc_html_e("Status", 'default'); ?>
					</label> <label> <input type="checkbox"
							name="duplicate_post_copyslug" value="1" <?php  if(get_option('duplicate_post_copyslug') == 1) echo 'checked="checked"'; ?>"/>
							<?php esc_html_e("Slug", 'default'); ?>
					</label> <label> <input type="checkbox"
							name="duplicate_post_copyexcerpt" value="1" <?php  if(get_option('duplicate_post_copyexcerpt') == 1) echo 'checked="checked"'; ?>"/>
							<?php esc_html_e("Excerpt", 'default'); ?>
					</label> <label> <input type="checkbox"
							name="duplicate_post_copycontent" value="1" <?php  if(get_option('duplicate_post_copycontent') == 1) echo 'checked="checked"'; ?>"/>
							<?php esc_html_e("Content", 'default'); ?>
					</label> <label> <input type="checkbox"
							name="duplicate_post_copythumbnail" value="1" <?php  if(get_option('duplicate_post_copythumbnail') == 1) echo 'checked="checked"'; ?>"/>
							<?php esc_html_e("Featured Image", 'default'); ?>
					</label> <label> <input type="checkbox"
							name="duplicate_post_copytemplate" value="1" <?php  if(get_option('duplicate_post_copytemplate') == 1) echo 'checked="checked"'; ?>"/>
							<?php esc_html_e("Template", 'default'); ?>
					</label> <label> <input type="checkbox"
							name="duplicate_post_copyformat" value="1" <?php  if(get_option('duplicate_post_copyformat') == 1) echo 'checked="checked"'; ?>"/>
							<?php echo esc_html_x("Format", 'post format', 'default'); ?>																					
					</label> <label> <input type="checkbox"
							name="duplicate_post_copyauthor" value="1" <?php  if(get_option('duplicate_post_copyauthor') == 1) echo 'checked="checked"'; ?>"/>
							<?php esc_html_e("Author", 'default'); ?>
					</label> <label> <input type="checkbox"
							name="duplicate_post_copypassword" value="1" <?php  if(get_option('duplicate_post_copypassword') == 1) echo 'checked="checked"'; ?>"/>
							<?php esc_html_e("Password", 'default'); ?>
					</label> <label> <input type="checkbox"
							name="duplicate_post_copyattachments" value="1" <?php  if(get_option('duplicate_post_copyattachments') == 1) echo 'checked="checked"'; ?>"/>
							<?php esc_html_e("Attachments", 'duplicate-post');  ?> <small>(<?php esc_html_e("you probably want this unchecked, unless you have very special requirements", 'duplicate-post');  ?>)</small>
					</label> <label> <input type="checkbox"
							name="duplicate_post_copychildren" value="1" <?php  if(get_option('duplicate_post_copychildren') == 1) echo 'checked="checked"'; ?>"/>
							<?php esc_html_e("Children", 'duplicate-post');  ?>
					</label> <label> <input type="checkbox"
							name="duplicate_post_copycomments" value="1" <?php  if(get_option('duplicate_post_copycomments') == 1) echo 'checked="checked"'; ?>"/>
							<?php esc_html_e("Comments", 'default');  ?> <small>(<?php esc_html_e("except pingbacks and trackbacks", 'duplicate-post');  ?>)</small>
					</label> <label> <input type="checkbox"
							name="duplicate_post_copymenuorder" value="1" <?php  if(get_option('duplicate_post_copymenuorder') == 1) echo 'checked="checked"'; ?>"/>
							<?php esc_html_e("Menu order", 'default');  ?>
					</label>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row"><?php esc_html_e("Title prefix", 'duplicate-post'); ?>
					</th>
					<td><input type="text" name="duplicate_post_title_prefix"
						value="<?php echo get_option('duplicate_post_title_prefix'); ?>" />
					</td>
					<td><span class="description"><?php esc_html_e("Prefix to be added before the title, e.g. \"Copy of\" (blank for no prefix)", 'duplicate-post'); ?>
					</span>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row"><?php esc_html_e("Title suffix", 'duplicate-post'); ?>
					</th>
					<td><input type="text" name="duplicate_post_title_suffix"
						value="<?php echo get_option('duplicate_post_title_suffix'); ?>" />
					</td>
					<td><span class="description"><?php esc_html_e("Suffix to be added after the title, e.g. \"(dup)\" (blank for no suffix)", 'duplicate-post'); ?>
					</span>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row"><?php esc_html_e("Increase menu order by", 'duplicate-post'); ?>
					</th>
					<td><input type="text" name="duplicate_post_increase_menu_order_by"
						value="<?php echo get_option('duplicate_post_increase_menu_order_by'); ?>" />
					</td>
					<td><span class="description"><?php esc_html_e("Add this number to the original menu order (blank or zero to retain the value)", 'duplicate-post'); ?>
					</span>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row"><?php esc_html_e("Do not copy these fields", 'duplicate-post'); ?>
					</th>
					<td id="textfield"><input type="text"
						name="duplicate_post_blacklist"
						value="<?php echo get_option('duplicate_post_blacklist'); ?>" /></td>
					<td><span class="description"><?php esc_html_e("Comma-separated list of meta fields that must not be copied", 'duplicate-post'); ?><br />
							<small><?php esc_html_e("You can use * to match zero or more alphanumeric characters or underscores: e.g. field*", 'duplicate-post'); ?>
						</small> </span>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row"><?php esc_html_e("Do not copy these taxonomies", 'duplicate-post'); ?><br />
						<a class="toggle_link" href="#"
						onclick="toggle_private_taxonomies();return false;"><?php esc_html_e('Show/hide private taxonomies', 'duplicate-post');?>
					</a>
					</th>
					<td colspan="2"><?php $taxonomies=get_taxonomies(array(),'objects'); usort($taxonomies, 'duplicate_post_tax_obj_cmp');
					$taxonomies_blacklist = get_option('duplicate_post_taxonomies_blacklist');
					if ($taxonomies_blacklist == "") $taxonomies_blacklist = array();
					foreach ($taxonomies as $taxonomy ) : 
						if($taxonomy->name == 'post_format'){
							continue;
						}
						?> <label
						class="taxonomy_<?php echo ($taxonomy->public)?'public':'private';?>">
							<input type="checkbox"
							name="duplicate_post_taxonomies_blacklist[]"
							value="<?php echo $taxonomy->name?>"
							<?php if(in_array($taxonomy->name, $taxonomies_blacklist)) echo 'checked="checked"'?> />
							<?php echo $taxonomy->labels->name.' ['.$taxonomy->name.']'; ?>
					</label> <?php endforeach; ?> <span class="description"><?php esc_html_e("Select the taxonomies you don't want to be copied", 'duplicate-post'); ?>
					</span>
					</td>
				</tr>
			</table>
		</section>
		<section>
			<table class="form-table">
				<?php if ( current_user_can( 'promote_users' ) ){ ?>
				<tr valign="top">
					<th scope="row"><?php esc_html_e("Roles allowed to copy", 'duplicate-post'); ?>
					</th>
					<td><?php	global $wp_roles;
					$roles = $wp_roles->get_names();
					foreach ($roles as $name => $display_name): $role = get_role($name);
					if ( !$role->has_cap('edit_posts') ) continue; ?> <label> <input
							type="checkbox" name="duplicate_post_roles[]"
							value="<?php echo $name ?>"
							<?php if($role->has_cap('copy_posts')) echo 'checked="checked"'?> />
							<?php echo translate_user_role($display_name); ?>
					</label> <?php endforeach; ?> <span class="description"><?php esc_html_e("Warning: users will be able to copy all posts, even those of other users", 'duplicate-post'); ?><br />
							<?php esc_html_e("Passwords and contents of password-protected posts may become visible to undesired users and visitors", 'duplicate-post'); ?>
					</span>
					</td>
				</tr>
				<?php } ?>
				<tr valign="top">
					<th scope="row"><?php esc_html_e("Enable for these post types", 'duplicate-post'); ?>
					</th>
					<td><?php $post_types = get_post_types(array('show_ui' => true),'objects');
					foreach ($post_types as $post_type_object ) :
					if ($post_type_object->name == 'attachment') continue; ?> <label> <input
							type="checkbox" name="duplicate_post_types_enabled[]"
							value="<?php echo $post_type_object->name?>"
							<?php if(duplicate_post_is_post_type_enabled($post_type_object->name)) echo 'checked="checked"'?> />
							<?php echo $post_type_object->labels->name?>
					</label> <?php endforeach; ?> <span class="description"><?php esc_html_e("Select the post types you want the plugin to be enabled", 'duplicate-post'); ?>
							<br /> <?php esc_html_e("Whether the links are displayed for custom post types registered by themes or plugins depends on their use of standard WordPress UI elements", 'duplicate-post'); ?>
					</span>
					</td>
				</tr>
			</table>
		</section>
		<section>
			<table class="form-table">
				<tr valign="top">
					<th scope="row"><?php esc_html_e("Show links in", 'duplicate-post'); ?>
					</th>
					<td><label><input type="checkbox" name="duplicate_post_show_row"
							value="1" <?php  if(get_option('duplicate_post_show_row') == 1) echo 'checked="checked"'; ?>"/>
							<?php esc_html_e("Post list", 'duplicate-post'); ?> </label>
							<label><input type="checkbox" name="duplicate_post_show_submitbox" value="1" <?php  if(get_option('duplicate_post_show_submitbox') == 1) echo 'checked="checked"'; ?>"/>
							<?php esc_html_e("Edit screen", 'duplicate-post'); ?> </label>
							<label><input type="checkbox" name="duplicate_post_show_adminbar" value="1" <?php  if(get_option('duplicate_post_show_adminbar') == 1) echo 'checked="checked"'; ?>"/>
							<?php esc_html_e("Admin bar", 'duplicate-post'); ?> <small>(<?php esc_html_e("now works on Edit screen too — check this option to use with Gutenberg enabled", 'duplicate-post');  ?>)</small></label> 
							<?php global $wp_version;
							if( version_compare($wp_version, '4.7') >= 0 ){ ?>
							<label><input type="checkbox" name="duplicate_post_show_bulkactions" value="1" <?php  if(get_option('duplicate_post_show_bulkactions') == 1) echo 'checked="checked"'; ?>"/>
							<?php esc_html_e("Bulk Actions", 'default'); ?> </label>
							<?php } ?>												
					</td>
				</tr>
				<tr valign="top">
					<td colspan="2"><span class="description"><?php esc_html_e("Whether the links are displayed for custom post types registered by themes or plugins depends on their use of standard WordPress UI elements", 'duplicate-post'); ?>
							<br /> <?php printf(__('You can also use the template tag duplicate_post_clone_post_link( $link, $before, $after, $id ). More info <a href="%s">here</a>', 'duplicate-post'), 'https://duplicate-post.lopo.it/docs/developers-guide/functions-template-tags/duplicate_post_clone_post_link/'); ?>
					</span>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row"><?php esc_html_e("Show update notice", 'duplicate-post'); ?>
					</th>
					<td><input type="checkbox" name="duplicate_post_show_notice"
							value="1" <?php  if(get_option('duplicate_post_show_notice') == 1) echo 'checked="checked"'; ?>"/>
					</td>
				</tr>				
			</table>
		</section>
		<p class="submit">
			<input type="submit" class="button-primary"
				value="<?php esc_html_e('Save Changes', 'duplicate-post') ?>" />
		</p>

	</form>
</div>
<?php
}
?>