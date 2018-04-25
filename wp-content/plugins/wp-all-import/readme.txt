=== Import any XML or CSV File to WordPress ===
Contributors: soflyy, wpallimport 
Requires at least: 4.1
Tested up to: 4.9
Stable tag: 3.4.6
Tags: wordpress csv import, wordpress xml import, xml, csv, datafeed, import, migrate, import csv to wordpress, import xml to wordpress, advanced xml import, advanced csv import, bulk csv import, bulk xml import, bulk data import, xml to custom post type, csv to custom post type, woocommerce csv import, woocommerce xml import, csv import, import csv, xml import, import xml, csv importer

WP All Import is an extremely powerful importer that makes it easy to import any XML or CSV file to WordPress.

== Description ==

= WP All Import - Simple & Powerful XML / CSV Importer Plugin =

*“It's a wonderful plugin that does so much, so well that it's hard to list all of the features. But I'll tell you this, I was able to import the content of a pair of websites running the ModX CMS into a WordPress install in less than 30 minutes. No joke!”*  
**Alex Vasquez** - DigiSavvy Co-Founder & WordCamp Los Angeles Organizer

WP All Import has a four step import process and an intuitive drag & drop interface that makes complicated import tasks simple and fast.

There are no special requirements that the elements in your file must be laid out in a certain way. WP All Import really can import any XML or CSV file.

WP All Import can be used for everything from migrating content from a legacy CMS to WordPress to building a store with an affiliate datafeed to displaying live stock quotes or sports scores to building a real estate portal.

Check out our [documentation and video tutorials](http://www.wpallimport.com/documentation/?utm_source=free-plugin&utm_medium=dot-org&utm_campaign=docs) to make the most of WP All Import.

WP All Import integrates with our companion plugin, [WP All Export](https://wordpress.org/plugins/wp-all-export/). You can export posts, WooCommerce products, orders, users, or anything else with WP All Export. Then you can edit in Excel and re-import to the same site or migrate the data to another site with WP All Import.

For technical support from the developers, please consider purchasing WP All Import Pro.

= WP All Import Professional Edition =
[youtube http://www.youtube.com/watch?v=pD6WQANJcJY /]

*WP All Import Pro* is a paid upgrade that includes premium support and adds the following features:

* Import data to Custom Fields - used by many themes, especially those using Custom Post Types - to store data associated with the posts.

* Import images to the post media gallery - WP All Import can download images from URLs in an XML or CSV file and put them in the media gallery.

* Cron Job/Recurring Imports - WP All Import pro can check periodically check a file for updates, and add, edit, and delete to the imported posts accordingly.

* Import files from a URL - Download and import files from external websites, even if they are password protected with HTTP authentication. URL imports are integrated with the recurring/cron imports feature, so WP All Import can periodically re-download the files and add, edit, and delete posts accordingly.

* Execution of Custom PHP Functions on data, i.e. use something like [my_function({xpath/to/a/field[1]})] in your template, to pass the value of {xpath/to/a/field[1]} to my_function and display whatever it returns.

* Guaranteed technical support via e-mail.

[Upgrade to the Pro edition of WP All Import.](http://www.wpallimport.com/upgrade-to-pro/?utm_source=free-plugin&utm_medium=dot-org&utm_campaign=upgrade)

Need to [import XML and CSV to WooCommerce?](http://wordpress.org/plugins/woocommerce-xml-csv-product-import/) Check out our WooCommerce add-on.

= WordPress CSV Imports =

Read on to learn more about the CSV importer functionality of WP All Import. Importing CSVs with WP All Import is exactly the same as importing XML files, because internally, WP All Import actually converts your CSV file to an XML file on the fly. You can use the same XPath filtering options and all the same features you have when importing XML files.

CSV imports don't require your CSV file to have a specific structure. Your CSV file can use any column names/headings. You can map the columns in your CSV file to the appropriate places in WordPress during the import process.

When importing CSV files, your CSV should have UTF-8 encoding if you are having trouble importing special characters.

In step 2 of a CSV import, you can specify an alternative delimiter if you aren't using a comma. WP All Import can import CSVs that are pipe-delimited, # delimited, or delimited/separated by any other character.

For CSV import tutorials and example files, visit our [documentation](http://www.wpallimport.com/documentation/?utm_source=free-plugin&utm_medium=dot-org&utm_campaign=documentation). Please keep in mind CSV imports with WP All Import are just like XML imports - you have all the same functionality, and the process is exactly the same. Any of our tutorial videos that apply to XML files also apply to importing CSV files, so if you see a tutorial with us importing an XML file, know that you can follow the exact same steps for a CSV import.

= Add-Ons =

A number of premium add-ons are available to add functionality to the importer and make XML & CSV import tasks to complex plugins simple.

 - Advanced Custom Fields Add-On - [ACF](http://www.advancedcustomfields.com/) XML & CSV importer
 - WooCommerce Add-On - XML & CSV importer for all [WooCommerce](http://wordpress.org/plugins/woocommerce) product types
 - User Import Add-On - XML & CSV importer for users, including user_meta
 - Link Cloak Add-On - Auto-create redirects for links present during an XML or CSV import

Learn more about our add-ons at [http://www.wpallimport.com/add-ons](http://www.wpallimport.com/add-ons?utm_source=free-plugin&utm_medium=dot-org&utm_campaign=add-ons)

A [developer API](http://www.wpallimport.com/documentation/developers/action-reference/?utm_source=free-plugin&utm_medium=dot-org&utm_campaign=documentation) (action hooks) is also available.

== Premium Support ==
Upgrade to the Pro edition of WP All Import for premium support.

E-mail: support@wpallimport.com

== Import To WooCommerce ==

Need to [import XML and CSV to WooCommerce?](http://wordpress.org/plugins/woocommerce-xml-csv-product-import/) Check out our WooCommerce add-on.

[WooCommerce XML & CSV Import Pro Version](http://www.wpallimport.com/woocommerce-product-import?utm_source=free-plugin&utm_medium=dot-org&utm_campaign=woocommerce)

== Frequently Asked Questions ==

**What Size Files Can WP All Import Handle?**
It depends on your hosting provider’s settings. We’ve imported files of 200Mb and up, even on shared hosts. WP All Import splits your file into manageable chunks. 

[Various settings are available](http://www.wpallimport.com/documentation/advanced/import-processing/?utm_source=free-plugin&utm_medium=dot-org&utm_campaign=large-files) to make it possible to import larger files or speed up your import.

*The answer to all of the following questions is yes:*

Does this really work with ANY XML or CSV file?
Can WP All Import get ALL of the data out of the file? Even attributes?
Does it work with special character encoding like Hebrew, Arabic, Chinese, etc?

== Screenshots ==

1. Choose file.
2. Filtering options.
3. Choose where to import your data.
4. Manage imports.

== Changelog ==

= 3.4.6 =
* improvement: added timestamp to import log lines
* improvement: added support for bmp images
* improvement: added new action pmxi_before_post_import_{$addon}
* security fix: patch XSS exploit
* bug fix: import pages hierarchy
* bug fix: error in pclzip.lib.php with php 7.1
* bug fix: import taxonomies hierarchy
* bug fix: json to xml convertation
* bug fix: removed SWFUpload

= 3.4.5 =
* improvement: custom fields delection
* improvement: new action wp_all_import_post_skipped
* improvement: updated history page title
* improvement: optimize large imports deletion
* improvement: added import friendly name to confirm screen
* improvement: sql query optimization on manage imports screen
* bug fix: generation image filename
* bug fix: wp_all_import_specified_records filter

= 3.4.4 =
* bug fix: import template not worked when downloaded via Import Settings
* bug fix: updating user login
* bug fix: import images with encoded quotes 
* improvement: added hungarian translation

= 3.4.3 =
* improvement: new filter 'wp_all_import_phpexcel_delimiter'
* improvement: new filter 'wp_all_import_is_trim_parsed_data'
* improvement: added new filter 'wp_all_import_skip_x_csv_rows'
* improvement: added csv delimiter setting to import options screen
* bug fix: import duplicate tags

= 3.4.2 =
* bug fix: conflict with the event calendar plugin
* bug fix: import images for newly created products

= 3.4.1 =
* improvement: Stop parsing data which is not going to be updated
* improvement: added new filter wp_all_import_phpexcel_object to modify excel data before import
* bug fix: search for images ending with underscores in media
* bug fix: import hierarchical posts/pages
* bug fix: import cpt page templates

= 3.4.0 =
* improvement: compatibility with PHP 7.x

= 3.3.9 =
* improvement: new re-import option 'is update post type'
* bug fix: hierarchy taxonomies preview
* bug fix: empty logs folder generation
* bug fix: 'Keep images currently in Media Library' option for add-ons
* bug fix: import bundles with gz files
* bug fix: custom functions for attachments

= 3.3.8 =
* improvement: 'Force Stream Reader' setting
* improvement: new filter 'wp_all_import_auto_create_csv_headers'
* improvement: new filter 'wp_all_import_is_base64_images_allowed'
* improvement: new filter 'wp_all_import_set_post_terms' to leave a specific category alone when a post is being updated
* bug fix: nodes navigation for xpath like /news/item
* bug fix: frozen import template screen for cyrillic XML feeds
* bug fix: conflict between taxonomies & user import
* bug fix: creating users with the same email
* bug fix: enable keep line breaks option by default
* bug fix: composer namespace conflict
* bug fix: images preview when wp is in subdirectory
* bug fix: 'Instead of deletion, set Custom Field' option for users import

= 3.3.7 =
* added new option 'Use StreamReader instead of XMLReader to parse import file' to fix issue with libxml 2.9.3
* execute 'pmxi_article_data' filter for all posts ( new & existing )

= 3.3.6 =
* added de_CH translation
* added support for .svg images
* added possibility for import excerpts for pages
* added new filter 'wp_all_import_specified_records'
* added new filter 'wp_all_import_is_post_to_delete'
* disable XMLReader stream filter for HHVM
* improve search for existing images in media gallery

= 3.3.5 =
* fixed 'Use images currently in Media Library' option

= 3.3.4 =
* fixed error messages on step 1 in case when server throws fatal error e.q. time limit exception
* fixed option "Delete posts that are no longer present in your file", now it works with empty CSV files which has only one header row
* fixed custom php functions in images preview
* fixed detecting root nodes with colons in names
* added es_ES translation
* added de_DE translation
* added iterative ajax delete process ( deleting associated posts )
* added feature to download template/bundle from import settings
* added new option for importing images "Use images currently in Media Library"
* added new action 'pmxi_missing_post'

= 3.3.3 =
* fixed duplicate matching by custom field
* fixed converting image filenames to lowercase
* fixed import html to image description
* fixed import _wp_old_slug
* added Post ID to manual record matching
* added 'Comment status' to 'Choose data to update' section

= 3.3.2 =
* fixed fatal error on saving settings

= 3.3.1 =
* fixed parsing CSV with empty lines
* fixed parsing multiple IF statements
* fixed preview in case when ‘Disable the visual editor when writing’ is enabled
* fixed conflict with WooCommerce - Store Exporter Deluxe
* added notifications for required addons
* added support for wp all export bundle
* added support for manual import bundle
* added feature 'click to download import file'
* added validation for excerpt and images sections
* added auto-detect a broken Unique ID notification
* added import template notifications
* removed support for importing WooCommerce Orders
* changed absolute paths to relative in db

= 3.3.0 =
* added new options to taxonomies import 'Try to match terms to existing child Product Categories' & 'Only assign Products to the imported Product Category, not the entire hierarchy'
* added support for Excel files ( .xls, .xlsx ) 

= 3.2.9 =
* load ini_set only on plugin pages
* fixed saving import template

= 3.2.8 =
* fixed Apply mapping rules before splitting via separator symbol for manual hierarchy
* fixed path equal or less than
* fixed changing unique key when moving back from confirm screen
* fixed override page template
* updated wp_all_import_is_post_to_update filter with second argument XML node as array
* added a second argument to pmxi_saved_post action ( SimpleXML object ) of current record

= 3.2.7 =
* fixed enum fields mapping rules feature

= 3.2.6 =
* Compatibility with 3rd party development: http://www.wpallimport.com/documentation/addon-dev/overview/

= 3.2.5 =
* Important security fixes - additional hardening, prevention of blind SQL injection and reflected XSS attacks

= 3.2.4 =
* critical security fix - stopping non-logged in users from accessing adminInit

= 3.2.3 =
* fixed re-count record when a file has been changed at an import setting screen
* fixed unlink attachment source when posts updated/deleted
* added a limit 10 to the existing meta values

= 3.2.2 =
* fixed database schema
* uploading large files

= 3.2.1 =
* fixed updating import settings

= 3.2.0 =
* IMPORTANT: WP All Import v4 (3.2.0) is a MAJOR update. Read this post before upgrading: http://www.wpallimport.com/2014/11/free-version-wordpress-org-update-information
* speed up the import of taxonomies/categories
* added taxonomies/categories mapping feature
* added custom fields auto-detection feature
* added custom fields mapping feature
* added images/taxonomies preview feature
* added unofficial support for more file formats - json & sql
* added new setting (secure mode) to protect your files
* better import logs
* updated design

= 3.1.5 =
* fixed pmxi_delete_post action
* fixed import menu order & post parent for pages
* fixed import log for continue import feature
* added is update author option
* fixed post formats
* fixed UTC dates on manage imports page

= 3.1.4 =
* changed support email

= 3.1.3 =
* fixed import pages

= 3.1.2 =
* added compatibility with WP 3.9
* added autodetect session mode
* updated convertation CSV to XML with XMLWriter
* fixed import *.zip files
* fixed xpath helper on step 2
* fixed showing zeros in XML tree
* fixed deleting history files
* fixed autodetect image extensions
* fixed increasing SQL query length
* allow post content to be empty on step 3
* delete deprecated settings "my csv contain html code" and "case sensitivity"

= 3.1.1 =
* Fixed compatibility with addons
* Fixed "download image" option for import products
* Fixed CSS for WP 3.8
* Fixed dismiss links

= 3.1.0 =
* Compatibility with WP 3.8
* Compatibility with WPAI WooCommerce add-on (paid) 1.2.4
* Performance Improvements
* Improved UI
* Lots of bug fixes
* New Record Matching section
* Added option to set Post Status with XPath (the value of presented XPath should be one of the following: publish, draft, trash)
* Preview navigation

= 3.0.4 = 
* Fixed import categories;
* Updated UI/UX;
* Added import/export templates feature;
* Added enhanced session functionality;
* Added option to set post status with XPath;
* Added feeds encoding feature;

= 3.0.2 = 
* Added support for the WooCommerce add-on

= 3.0 = 
* Free edition of 3.0 pro release

= 2.14 =
* Category list delimiter bug fix

= 2.13 =
* Tons of bug fixes, updates, and additional features. 


= 2.12 =
* Initial release on WordPress.org.
