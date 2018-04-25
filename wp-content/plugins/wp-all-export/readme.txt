=== Export WordPress data to XML/CSV ===
Contributors: soflyy, wpallimport
Requires at least: 4.1
Tested up to: 4.9
Stable tag: 1.1.5
Tags: wordpress csv export, wordpress xml export, xml, csv, datafeed, export, migrate, export csv from wordpress, export xml from wordpress, advanced xml export, advanced csv export, export data, bulk csv export, export custom post type, export woocommerce products, export woocommerce orders, migrate woocommerce, csv export, export csv, xml export, export xml, csv exporter, datafeed

WP All Export is an extremely powerful exporter that makes it easy to export any XML or CSV file from WordPress.

== Description ==

WP All Export features a three step export process and an intuitive drag & drop interface that makes complicated export tasks simple and fast.

With WP All Export you can: export data for easy editing, migrate content from WordPress to another site, create a WooCommerce affiliate feed, generate filtered lists of WooCommerce orders, export the email addresses of new customers, create and publish customized WordPress RSS feeds - and much more.
[youtube https://www.youtube.com/watch?v=a-z0R-Ldkqo /]

* **Turn your WordPress data into a customized CSV or XML**

* **Choose which data to export:** WP All Export's drag and drop interface makes it easy to select exactly which data you'd like to export

* **Structure your export file however you like:** Rename CSV columns and XML elements, rearrange them, whatever you want to do.

* **Export any custom post type, any custom field:** Lots of plugins and themes store custom data in WordPress. You can export all of it with WP All Export.

* **Easy integration with WP All Import:** WP All Export will generate your WP All Import settings for you so importing your data back into WordPress is easy, simple, and fast.

**Wish you could edit your WordPress data in Excel? Now you can - export it with WP All Export, edit it, and then import it again with WP All Import.**

For technical support from the developers, please consider purchasing WP All Export Pro.

= WP All Export Professional Edition =

**WP All Export Pro** is a paid upgrade that includes premium support and adds the following features:

* **Send your data to 500+ apps:** Full integration with Zapier allows you to send your exported WordPress data to services like Dropbox and Google Drive, to create and update reports in Google Sheets, send email updates, or anything else you can think of. This is especially useful when you export WooCommerce orders to CSV.

	[Read more about WP All Export Pro and Zapier.](https://zapier.com/zapbook/wp-all-export-pro/)

* **Schedule exports to run automatically:** Exports can be configured via cron to run on any schedule you like. You can export new sales every week, recent user sign ups, new affiliate products added to your site, daily product stock reports, etc. Scheduled exports are incredibly powerful and flexible when combined with Zapier.

* **Add rules to filter data:** WP All Export Pro makes it easy to export the exact posts/products/orders you need. Want to export all WooCommerce orders over $100? Want to export all of the green shirts from your WooCommerce store? Want to export all new posts from 2014, except the ones added by Steve?

	You can with a simple to use interface on the 'New Export' page in WP All Export Pro.

* **Export WordPress users:** WP All Export Pro adds the ability to export WordPress users and all custom data associated with them. Available data is organized and cleaned up so you don’t need to know anything about how WordPress stores users in order to export them.

* **Export WooCommerce orders:** Export WooCommerce Order item data with WP All Export Pro. Just as with any other custom post type, you can export WooCommerce orders with the free version of WP All Export. However, the order item data is stored by WooCommerce in several custom database tables and this custom data is only accessible with WP All Export Pro.

* **Pass data through custom PHP functions:** With WP All Export Pro you can pass your data through a custom function before it is added to your export file. This will allow you to manipulate your data any way you see fit.

* **Guaranteed technical support via e-mail.**

[Upgrade to the Pro edition of WP All Export.](http://www.wpallimport.com/upgrade-to-wp-all-export-pro/?utm_source=wordpress.org&utm_medium=wordpress-dot-org-slash-wpae&utm_campaign=free+wp+all+export+plugin)

= WordPress CSV Exports =

A CSV is a very simple type of spreadsheet file where each column is separated by a comma. With WP All Export you can very easily set up a WordPress CSV export and control the order and title of the columns.

Very often you'll want to edit your data with Microsoft Excel, Google Sheets, Numbers, or maybe something else. This is why a CSV export is so powerful - all spreadsheet software can read, edit, and save CSV files. WP All Export allows you edit your WordPress data using whatever spreadsheet software you are most comfortable with.

= WordPress XML Exports =

Sometimes you'll want to export your data so that some other tool, software, or service can use it. Very often they will require your data to be formatted as an XML file. XML is very similar to HTML, but you don't need to know anything about that in order to set up an XML export with WP All Export.

If you want to set up a WordPress XML export all you need to do is select 'XML' when configuring your export template. And just like a CSV export, an XML export will allow you to customize the element names and put them in any order you wish.

== Premium Support ==
Upgrade to the Pro edition of WP All Export for premium support.

E-mail: support@wpallimport.com

== Installation ==

Either: -

* Upload the plugin from the Plugins page in WordPress
* Unzip wp-all-export.zip and upload the contents to /wp-content/plugins/, and then activate the plugin from the Plugins page in WordPress

== Changelog ==

= 1.1.5 =
* improvement: removed autoload=true from wp_options
* improvement: WPML options in separate section
* bug fix: allow underscores in main xml tags
* bug fix: prevent uploading import template into wpae
* bug fix: ID column in CSV
* bug fix: ACF repeater headers

= 1.1.4 =
* improvement: removed autoload=true from wp_options
* improvement: WPML options in separate section
* bug fix: allow underscores in main xml tags
* bug fix: prevent uploading import template into wpae
* bug fix: ID column in CSV
* bug fix: ACF repeater headers

= 1.1.3 =
* improvement: added post_modified field
* bug fix: export attributes for simple products
* bug fix: db schema updating
* bug fix: variations with trashed parents shouldn't be included in the export

= 1.1.2 =
* improvement: choose WPML language in export options
* bug fix: export ACF message field
* bug fix: export product categories with AFC repeater fields
* bug fix: export duplicate images from gallery
* bug fix: export search
* bug fix: export product variation attribute names
* bug fix: migrating hierarchical posts and pages

= 1.1.1 =
* improvement: compatibility with PHP 7.x

= 1.1.0 =
* improvement: added ACF fields to 'migrate' & 'add all fields' features
* improvement: added new filter 'wp_all_export_field_name'
* improvement: changed default date export to Y/m/d
* bug fix: export shipping class for product variations
* bug fix: import template for ACF relationship fields
* bug fix: export empty images metadata
* bug fix: import template for ACF gallery
* bug fix: import template for variation_description
* bug fix: import template for default product attributes
* bug fix: automatically adding parent_id column on products export

= 1.0.9 =
* bug fix: fixed compatibility with PHP 5.3

= 1.0.8 =
* improvement: pull the parent taxonomy data when exporting variations
* improvement: remove spaces from export filename
* improvement: new filter wp_all_export_after_csv_line
* improvement: date options for sale price dates from/to
* improvement: possibility to use tab as csv delimiter
* improvement: new filter 'wp_all_export_csv_headers'
* bug fix: db schema on multisite
* bug fix: import template for media items
* bug fix: export ACF repeater in XML format
* bug fix: export in CSV format when 'Main XML Tag' & 'Record XML Tag' option are blank
* bug fix: export ACF date_time_picker

= 1.0.7 =
* fixed db schema for multisite
* fixed export order items date
* fixed media items export ordering
* fixed import template for media items
* fixed export ACF repeater in XML format
* added new filter 'wp_all_export_csv_headers'
* added possibility to use tab as csv delimiter "\t"
* updated french translation

= 1.0.6 =
* added new filters 'wp_all_export_is_wrap_value_into_cdata', 'wp_all_export_add_before_element', 'wp_all_export_add_after_element'
* added 'WPML Translation ID' element to available data
* modified preview to show first 10 records
* fixed csv export with non comma delimiter
* fixed conflict with WP Google Maps Pro plugin

= 1.0.5 =
* fixed misaligned columns on exporting product attributes
* fixed export nested repeaters field in CSV format
* fixed live records counting for advanced export mode
* fixed Events Calendar conflict
* added new filters 'wp_all_export_add_before_node', 'wp_all_export_add_after_node'
* added possibility export repeater rows one per line
* exclude orphaned variations from exprt file
* changed UI for export media data ( images & attachments )

= 1.0.4 =
* fixed export attachment meta alt
* fixed export manually stored ACF
* fixed export repeater field for users in csv format
* fixed import export templates
* fixed ajaxurl conflict with WPML
* added French & Latvian translations
* added 'Variation Description' field

= 1.0.3 =
* fixed manage exports screen: "Info and options" disappears when WPAI plugin is disabled
* fixed css for WordPress 4.4
* fixed export ACF repeater field
* updated re-run export screen
* added hidden post types to the dropdown list on step 1
* added export templates
* added possibility to control main xml tag names & put additional info into xml file: apply_filters('wp_all_export_additional_data', array(), $exportOptions)
* added ‘pmxe_exported_post’ action: do_action('pmxe_exported_post', $record->ID );
* added option 'Create a new export file each time export is run'
* added option 'Only export posts once'
* added option 'Split large exports into multiple files'
* added possibility to change export field name ( related to export WooCommerce orders )
* added es_ES translation
* added possibility to add NS to field names

= 1.0.2 =
* fixed download bundle
* fixed export repeater fields for ACF 4.x
* fixed import template for custom product attributes
* added new option 'include BOM to export file
* added RU translation
* removed hidden post types from dropdown in step 1

= 1.0.1 =
* fixed export taxonomy: name instead of slug
* fixed pass data through php function
* added advanced (custom fields) section to export woo orders
* added draggable element deletion
* added auto-generate product export fields
* added 'attributes' field to product data
* added button to download bundle for WP All Import
* updated export file name
* changed export files destination to /exports

= 1.0.0 =
* WP All Export exits beta

= 0.9.1 =
* critical security fix - stopping non-logged in users from accessing adminInit http://www.wpallimport.com/2015/02/wp-import-4-1-1-mandatory-security-update/

= 0.9 =
* Initial release on WordPress.org.
