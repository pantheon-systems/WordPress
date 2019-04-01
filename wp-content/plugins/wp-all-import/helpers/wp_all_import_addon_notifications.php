<?php
if ( ! function_exists('wp_all_import_addon_notifications') ){
	function wp_all_import_addon_notifications(){
		if ( ! empty($_GET['page']) and preg_match('%(pmxi-admin)%i', $_GET['page']))
		{
			if ( ! function_exists( 'is_plugin_active' ) ) require_once ABSPATH . 'wp-admin/includes/plugin.php';

			$current_theme = wp_get_theme();
			$parent_theme  = $current_theme->parent();				
			$theme_name    = $current_theme->get('Name');
			$parent_theme_name = ($parent_theme) ? $parent_theme->get('Name') : '';							

			$current_themes = array($theme_name, $parent_theme_name);
			
			$recommended_addons = array();
			
			// Reales WP Add-On
			if ( in_array('Reales WP', $current_themes) 
					and is_wp_error(validate_plugin('reales-wp-xml-csv-property-listings-import/reales-add-on.php')))					
			{
				$recommended_addons[] = array(
					'title' => 'Reales WP',
					'url'   => 'https://wordpress.org/plugins/reales-wp-xml-csv-property-listings-import/',					
				);
			}
			// WooCommerce Add-On
			if ( is_plugin_active('woocommerce/woocommerce.php') 
				and is_wp_error(validate_plugin('woocommerce-xml-csv-product-import/plugin.php'))
					and is_wp_error(validate_plugin('wpai-woocommerce-add-on/wpai-woocommerce-add-on.php')) )
			{
				$recommended_addons[] = array(
					'title' => 'WooCommerce',
					'url'   => 'https://wordpress.org/plugins/woocommerce-xml-csv-product-import/'
				);
			}
			// WP Job Manager Add-On
			if ( is_plugin_active('wp-job-manager/wp-job-manager.php') 
				and is_wp_error(validate_plugin('wp-job-manager-xml-csv-listings-import/wp-job-manager-add-on.php'))
					and ! in_array('Listify', $current_themes)
						and ! in_array('Jobify', $current_themes)
							and ! in_array('Listable', $current_themes))
			{
				$recommended_addons[] = array(
					'title' => 'WP Job Manager',
					'url'   => 'https://wordpress.org/plugins/wp-job-manager-xml-csv-listings-import/'
				);
			}
			// WP Residence Add-On
			if ( is_wp_error(validate_plugin('wp-residence-add-on-for-wp-all-import/wp-residence-add-on.php'))
				and (preg_match('%(WP Residence)%i', $theme_name) or preg_match('%(WP Residence)%i', $parent_theme_name)) )
			{
				$recommended_addons[] = array(
					'title' => 'WP Residence',
					'url'   => 'https://wordpress.org/plugins/wp-residence-add-on-for-wp-all-import/'
				);
			}
			// Realia Add-On
			if ( is_wp_error(validate_plugin('realia-xml-csv-property-listings-import/realia-add-on.php'))
				and (in_array('Realia', $current_themes) or is_plugin_active('realia/realia.php')) )
			{
				$recommended_addons[] = array(
					'title' => 'Realia',
					'url'   => 'https://wordpress.org/plugins/realia-xml-csv-property-listings-import/'
				);
			}
			// Listify Add-On
			if ( in_array('Listify', $current_themes) 
				and is_wp_error(validate_plugin('listify-xml-csv-listings-import/listify-add-on.php')))
			{
				$recommended_addons[] = array(
					'title' => 'Listify',
					'url'   => 'https://wordpress.org/plugins/listify-xml-csv-listings-import/'
				);
			}			
			// RealHomes Add-On
			if ( in_array('RealHomes Theme', $current_themes) 
				and is_wp_error(validate_plugin('realhomes-xml-csv-property-listings-import/realhomes-add-on.php')))
			{
				$recommended_addons[] = array(
					'title' => 'RealHomes',
					'url'   => 'https://wordpress.org/plugins/realhomes-xml-csv-property-listings-import/'
				);
			}
			// Jobify Add-On
			if ( in_array('Jobify', $current_themes) 
				and is_wp_error(validate_plugin('jobify-xml-csv-listings-import/jobify-add-on.php')))
			{
				$recommended_addons[] = array(
					'title' => 'Jobify',
					'url'   => 'https://wordpress.org/plugins/jobify-xml-csv-listings-import/'
				);
			}
			// Listable Add-On
			if ( in_array('Listable', $current_themes)
				and is_wp_error(validate_plugin('import-xml-csv-listings-to-listable-theme/listable-add-on.php')))
			{
				$recommended_addons[] = array(
					'title' => 'Listable',
					'url'   => 'https://wordpress.org/plugins/import-xml-csv-listings-to-listable-theme/'
				);
			}
			// Yoast SEO Add-On
			if ( is_plugin_active('wordpress-seo/wp-seo.php')
				and is_wp_error(validate_plugin('yoast-seo-settings-xml-csv-import/yoast-addon.php')))
			{				
				$recommended_addons[] = array(
					'title' => 'Yoast SEO',
					'url'   => 'https://wordpress.org/plugins/yoast-seo-settings-xml-csv-import/'
				);
			}			
			// ACF Add-On
			if ( is_plugin_active('advanced-custom-fields-pro/acf.php')
				and is_wp_error(validate_plugin('wpai-acf-add-on/wpai-acf-add-on.php')))
			{
				if ( ! get_option('acf_addon_notice_ignore')) {
					?>
					<div class="updated notice is-dismissible wpallimport-dismissible" rel="acf_addon"><p>
						<?php 
							printf(
								__('Make imports easier with the <strong>Advanced Custom Fields Add-On</strong> for WP All Import: <a href="%s" target="_blank">Read More</a>', 'wp_all_import_plugin'),
								'http://www.wpallimport.com/advanced-custom-fields/'
							);
						?>
					</p></div>
					<?php
				}				
			}
			// WP All Export
			if ( is_wp_error(validate_plugin('wp-all-export-pro/wp-all-export-pro.php')) and is_wp_error(validate_plugin('wp-all-export/wp-all-export.php')) )
			{
				if ( ! get_option('wp_all_export_notice_ignore')) {
					?>
					<!--div class="wpallimport-wrapper updated notice is-dismissible wpallimport-dismissible" rel="wp_all_export" style="margin: 10px 0; padding: 12px 0;">
						<div class="wpallimport-notify-wrapper">
							<div class="found_records speedup" style="margin-top: 20px;">
								<h3 style="font-size:26px;"><?php _e('WP All Export', 'wp_all_import_plugin');?></h3>
								<h4><?php _e("Export anything in WordPress to CSV, XML, or Excel.", "wp_all_import_plugin"); ?></h4>
							</div>		
						</div>		
						<a class="button button-primary button-hero wpallimport-large-button wpallimport-wpae-notify-read-more" href="http://www.wpallimport.com/export" target="_blank"><?php _e('Read More', 'wp_all_import_plugin');?></a>		
					</div-->
					<?php
				}				
			}

			if ( ! empty($recommended_addons))
			{
				foreach ($recommended_addons as $addon) {
					if ( ! get_option(sanitize_key($addon['title']) . '_notice_ignore')) {
						?>
						<div class="updated notice is-dismissible wpallimport-dismissible" rel="<?php echo sanitize_key($addon['title']); ?>"><p>
							<?php printf(
									__('Make imports easier with the <strong>free %s Add-On</strong> for WP All Import: <a href="%s" target="_blank">Get Add-On</a>', 'wp_all_import_plugin'),
									$addon['title'],
									$addon['url']
								  );
							?>
						</p></div>
						<?php
					}
				}
			}				
		}
	}
}	