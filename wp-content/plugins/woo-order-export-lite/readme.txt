=== Advanced Order Export For WooCommerce ===
Contributors: algolplus
Donate link: https://algolplus.com/plugins/
Tags: woocommerce,export,order,xls,csv,xml,woo export lite,export orders,orders export,csv export,xml export,xls export,tsv
Requires PHP: 5.4.0
Requires at least: 4.7
Tested up to: 5.1
Stable tag: 2.1.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Export orders from WooCommerce with ease ( Excel/CSV/TSV/XML/JSON supported )

== Description ==
This plugin helps you to **easily** export WooCommerce order data. 

Export any custom field assigned to orders/products/coupons is easy and you can select from various formats to export the data in such as CSV, XLS, XML and JSON.

= Features =

* **select** the fields to export
* **rename** labels
* **reorder** columns 
* export WooCommerce **custom fields** or terms for products/orders
* mark your WooCommerce orders and run "Export as..." a **bulk operation**.
* apply **powerful filters** and much more

= Export Includes =

* order data
* summary order details (# of items, discounts, taxes etc…)
* customer details (both shipping and billing)
* product attributes
* coupon details
* XLS, CSV, TSV, XML and JSON formats

= Use this plugin to export orders for =

* sending order data to 3rd part drop shippers
* updating your accounting system
* analysing your order data


Have an idea or feature request?
Please create a topic in the "Support" section with any ideas or suggestions for new features.

> Pro Version

> Are you looking to have your WooCommerce products drop shipped from a third party? Our plugin can help you export your orders to CSV/XML/etc and send them to your drop shipper. You can even automate this process with [Pro version](https://algolplus.com/plugins/downloads/advanced-order-export-for-woocommerce-pro/) .



== Installation ==

= Automatic Installation =
Go to WordPress dashboard, click  Plugins / Add New  , type 'order export lite' and hit Enter.
Install and activate plugin, visit WooCommerce > Export Orders.

= Manual Installation =
[Please, visit the link and follow the instructions](http://codex.wordpress.org/Managing_Plugins#Manual_Plugin_Installation)

== Frequently Asked Questions ==

Need help? Create ticket in [helpdesk system](https://algolplus.freshdesk.com). Don't forget to attach your settings or some screenshots. It will significantly reduce reply time :)

Check [some snippets](https://algolplus.com/plugins/snippets-plugins/) for popular plugins or review  [this page](https://algolplus.com/plugins/code-samples/) to study how to extend the plugin.

= I want to add a product attribute to the export  =
Check screenshot #5! You should open section "Set up fields", open section "Products"(right column), click button "Add field", select field in 2nd dropdown, type column title and press button "Confirm".

= I can't filter/export custom attribute for Simple Product =
I'm sorry, but it's impossible. You should add this attribute to Products>Attributes at first and use "Filter by Product Taxonomies".

= Plugin produces unreadable XLS file =
The theme or another plugin outputs some lines. Usually, there are extra empty lines at the end of functions.php(in active theme).

= I can't export Excel file (blank message or error 500) =
Please, increase "memory_limit" upto 256M or ask hosting support to do it.

= How can I add a Gravity Forms field to export? =
Open order, look at items and remember meta name.
Visit WooCommerce>Export Orders,
open section "Set up fields", scroll down to "Products", click button "Set up fields" ( you will see popup)
select SAME name in second dropdown

= When exporting .csv containing european special characters , I want to open this csv in Excel without extra actions =
You  should open tab "CSV" and set up ISO-8859-1 as codepage.

= Red text flashes at bottom during page loading = 
It's a normal situation. The plugin hides this warning on successful load. 

= Can I request any new feature ? =
Yes, you can email a request to aprokaev@gmail.com. We intensively develop this plugin.

== Screenshots ==

1. Default view after installation.  Just click 'Express Export' to get results.
2. Filter orders by many parameters, not only by order date or status.
3. Select the fields to export, rename labels, reorder columns.
4. Button Preview works for all formats.
5. Add custom field or taxonomy as new column to export.
6. Select orders to export and use "bulk action".

== Changelog ==

= 2.1.1 - 2019-02-14 =
* Fixed critical bug - new version damages CSV and TSV parameters, so "Bulk action" doesn't work

= 2.1.0 - 2019-02-06 =
* New format - **PDF**
* Fixed some vulnerabilities
* Added button "Reset settings"
* Section "Setup fields" works on phone/tablet
* New XLS option to avoid formatting - "Force general format for all cells"
* Fixed bug - fields "Summary Report Total xxxx" stayed at bottom
* Fixed bug -  "Summary report" was not sorted by item name
* Fixed bug - fields reset when  user switches between flat formats
* Fixed bug - field "full categories" was empty for variations
* Tested for jQuery 3.0+

= 2.0.1 - 2018-11-14 =
* Fixed bug - "total weight" and "count of unique products" were empty
* Fixed bug - message "wrong Select2 loaded"
* Fixed bug - UI issues after switching formats (CSV-XML-CSV)
* Shows some instructions if user gets popup with empty error message
* Shows warning if XML can not be built (PHP extension is not installed)

= 2.0.0 - 2018-10-24 =
* It's a **major update**. Backup settings (tab "Tools") before upgrading
* New section "Set up fields to export"  - simplify UI, format fields, allow duplicates
* Compatible with Woocommerce 3.5

= 1.5.6 - 2018-08-30 =
* Added filter by user custom fields
* Added order fields "Count of exported items", "User Website"
* Added product fields "Product Id", "Variation Id", "Order Line Subtotal Tax"
* Multiple custom fields with same title are exported as list (for order)
* Format Shipping/Billing fields as string (Excel only)
* Fixed compatibility issue with WP Redis cache
* Fixed bug - "Progressbar" shows error message correctly
* Fixed bug - "Progressbar" doesn't miss  orders  if both "Mark exported" and "Export unmarked only" are ON
* Reduced memory footprint (options are not autoloaded)

= 1.5.5 - 2018-06-08 =
* Added filter by item name
* Added filter by item metadata
* Added operators <,>,>=,<= for order and product custom fields
* Updated filter by shipping method (adapted for WooCommerce 3.4)
* Fixed bug in filter by product taxonomies 
* Allow to enter time in date range filter (after date)
* Show sections "Filter by order" and "Filter by coupon" as opened if some checkboxes are ON in these sections
* Added order field "Total Orders For Customer"
* Splited product field "Name" to "Item Name" and "Product Name" (to export current product name)
* Automatically scroll section "Setup Fields" to bottom after adding new field
* Export multiple values from same item meta
* Added new hooks for summary reports
* Prevent csv injection (we add space if cell value starts with =,+,-,@). Thank Bhushan Patil for finding this vulnerability!

= 1.5.4 - 2018-04-25 =
* Prompting to save changes if user modifies settings
* Product fields and order item fields were separated in popup "Setup fields"
* Allow to filter by raw shipping methods (not assigned to shipping zones)
* Record time of last export for the order (option "mark exported orders" must be ON)
* Added order fields "Line number", "Order Subtotal - Cart Discount"
* Added product field "Full names for categories"
* Added operators "Is set", "Not is set" for custom fields
* Added option "Enable debug output" to section "Misc Settings"
* Added option "Cleanup phone" to section "Misc Settings"
* Tags {from_date} and {to_date} can be used in filename 
* Fixed bug in UI  if order item meta has many values

= 1.5.3 - 2018-02-12 =
* The plugin is compatible with WooCommerce 3.3.1 
* Supports complex structures (arrays,objects) in the fields, export it as JSON string
* Shows "download link" for iPad/iPhone
* Added product field "Product URL"
* Fixed bug for Excel dates

= 1.5.2 - 2018-01-22 =
* Fixed dangerous bug for field "Order Line (w/o tax)" (tax was subtracted TWICE)
* Setup fields added for "Summary Report By Products"
* Corrected formats for Excel dates
* Added CSV option  "Convert line breaks to literals"
* Added order fields "City, State, Zip", "Date of first refund"
* Added product fields "Item ID", "Item Tax Rate", "Item Discount Amount", "Order Line Total (include tax)"
* Added more filters for UI
* Fixed bug for forms having huge number of fields
* Fixed bug for Excel builder 
* Fixed bug during import
* Fixed bug during bulk actions

= 1.5.1 - 2017-11-24 =
* The plugin is translated via translate.wordpress.org, so it requires WordPress 4.6+
* Added "Summary Report By Products"
* Added option "Format numbers ( WC decimal separator )" in "Misc Settings"
* Bulk actions work stable now ( WordPress 4.7+ required )
* Fixed bug at tab "Tools ( export/import procedure )
* Many messages were untranslatable in UI
* Optimized for shops having huge number of customers

= 1.5.0 - 2017-10-27 =
* Allow sort orders by "Created Date", "Modified Date"
* Added combined fields "Billing Address 1&2", "Shipping Address 1&2"
* Added checkboxes "Mark exported orders" and "Export unmarked orders only" to section "Filter By Order"
* Added section "custom php" in "Misc Settings" ( requires capability "edit_themes"! )
* Added option "Export refund notes as Customer Note" in "Misc Settings"
* Added text field for custom date format
* Added settings for JSON format
* The plugin is PHP7 compatible 
* Added headers "WC requires at least", "WC tested up to" for WooCommerce version check
* Optimized for shops having huge number of products

= 1.4.5 - 2017-09-06 =
* Fixed activation error for PHP less than 7.0

= 1.4.4 - 2017-09-04 =
* Fixed critical bug , headers were missed

= 1.4.3 - 2017-09-01 =
* User can select which date use ( created/modified/paid/completed )  in filter "Date Range"
* User can add new value to filters ( type text and press Enter )
* Added filter "Any Coupon Used"
* Added field  "Date Paid"
* Added checkbox to export all comments ( including system messages )
* Added checkbox to strip tags in product description/variation
* Added checkbox to export item rows at new line ( for CSV format )
* Tweak UI ( tooltips, reduce sections )
* Sorted values in all dropdowns 
* Fixed bug - Don't export draft order
* Fixed bug - Don't create file during estimation
* Plugin code partially refactored

= 1.4.2 - 2017-07-13 =
* Fixed critical bug in deactivation procedure

= 1.4.1 - 2017-07-12 =
* German translation was added. Thanks to contributor!
* Added filter "Billing locations"
* Added new format TSV (tab separated values)
* Added self closing tags for XML
* Added option to skip refunded items
* Import/export works with single profile
* Force string format for  some Excel columns ( customer note, phone number,..)
* Fixed some bugs for refunds
* Fixed bug for export via bulk actions 

= 1.4.0 - 2017-06-02 =
* Fixed bug for field "Customer order note"
* Fixed bug for filter by product category
* Tested for WordPress 4.8
* Added new product fields "Description" and "Short Description"
* Added logger for backgound tasks (for WooCommerce 3.0+)
* Added a lot of hooks 
* New tab "Order Change" to export single order immediately (Pro)

= 1.3.1 - 2017-05-12 =
* Optimized for big shops (tested with 10,000+ orders)
* Export refunds
* Export deleted products
* Added new filter "Product custom fields"
* Added new product field "Product Variation"
* Added new coupon fields "Type","Amount", "Discount Amount + Tax"
* Tweaked default settings
* Menu uses capability "view_woocommerce_reports"

= 1.3.0 - 2017-04-11 =
* The plugin is compatible with WooCommerce 3.0
* Display warning message if user interface fails to load
* Update Select2.js to fix some user interface problems
* Fixed fields "Order Tax" and "Subtotal" (uses WooCommerce functions to format it)

= 1.2.7 - 2017-03-17 =
* Portuguese and French translations were added. Thanks to contributors!
* Added new field "Order amount without tax"
* Added new product field "Quantity (- Refund)"
* Added tab "Help"
* Added some UI hooks
* Fixed bug in filter by Taxonomies
* Fixed bug in filter by Shipping Methods (disabled for WooCommerce  earlier than  2.6)
* Fixed field "State Full Name" (html entities removed)
* Skip **deleted products** during export
* Removed word "hack" from PHPExcel source

= 1.2.6 - 2017-02-02 =
* Added new filter "Filter by coupons"
* Added new filter "Shipping methods" to section "Filter by shipping"
* Added "refund" fields for items/taxes/shipping
* Simple products can be filtered by attributes using "Product Taxonomies"
* Fixed bug in filtering by products ( it checked first X products only)
* Fixed bug for filename in bulk actions
* Kill extra lines in generated files if the theme or another plugin outputs something at top
* XLS format doesn't require module "php-zip" now

= 1.2.5 - 2016-12-21 =
* Button "Preview" displays estimation (# of orders in exported file)
* User can change number of orders in "Preview"
* Orders can be sorted by "Order Id" in descending/ascending direction
* Added column "Image Url" for products (featured image)
* Fixed bug, **the plugin exported deleted orders!**
* Fixed bug, autocomplete displayed deleted products in filter "Product"
* Fixed bug, filter "category" and filter "Attribute" work together for variations
* Fixed bug, import settings didn't work correcty
* Suppress useless warning if the plugin can't create file in system "/tmp" folder
* New filters/hooks for products/coupons/vendors
* New filters/hooks for XLS format
* Russian/Chinise translations were updated

= 1.2.4 - 2016-11-15 =
* Added new filter "Item Metadata" to section "Filter by product"
* Added Chinese language. I’d like to thank user [7o599](https://wordpress.org/support/users/7o599/) 
* Added new tab "Tools" with section "export/import settings"
* Added button to hide non-selected fields
* XML format supports custom structures (some hooks were added too)
* Fixed bug for taxonomies (we export attribute Name instead of slug now)
* Fixed bug for XLS  without header line
* Fixed bug with pagination after export (bulk action)
* Fixed bug in action "Hide unused" for products
* Fixed bug for shops having huge number of users
* Fixed bug for "&" inside XML 

= 1.2.3 - 2016-10-21 =
* Added usermeta fields to section "Add field"
* "Press ESC to cancel export" added to progressbar 
* Added column "State Name"
* Added columns "Shipping Method", "Payment Method" (abbreviations)
* Format CSV can be exported without quotes around values
* Added checkbox to skip suborders
* Bulk export recoded to be compatible with servers behind a Load Balancer
* Skip root xml if it's empty
* New filters/hooks for CSV/XML formats
* [Code samples](https://algolplus.com/plugins/code-samples/)  added to documentation

= 1.2.2 - 2016-09-28 =
* Added column "Product Shipping Class"
* Added column "Download Url"
* Added column "Item Seller"
* Fixed bug in field "Line w/o tax" (if price doesn't include tax)
* Fixed bug in XML format  (for PHP7)
* A lot of new filters/hooks added

= 1.2.1 - 2016-08-12 =
* New filter by Payment Method
* New filter by Vendor( product creator)
* New field "Order Notes"
* Button "Export w/o progressbar" (added for servers behind a Load Balancer)
* Fixed bug if order was filtered by variable product

= 1.2.0 - 2016-07-11 =
* Support both XLS and XLSX
* Solved problem with filters ("Outdated Select2.js" warning)
* Added date/time format
* Comparison operators for custom fields & product attributes( + LIKE operator)
* Codepage for CSV file
* Preview displays 3 records
* Fixed bug for "Item cost"
* Refreshed language files 
 
= 1.1.13 - 2016-06-18 =
* Possibility to "Delete" fields (except default!)
* Added 'Hide unused' for order/product/coupon fields (dropdowns filtered by matching orders)
* Auto width for Excel format
* Export attributes which are not used in variations
* Support single/double quotes in column name
* Added  MAX # of columns ( if we export products as columns)

= 1.1.12 - 2016-05-25 =
* Added filter by users/roles
* Added filename for downloaded file
* Export refund amount
* Xls supports RTL

= 1.1.11 - 2016-04-27 =
* Added filter by custom fields (for order)
* Coded fallback if the plugin can't create files in folder "/tmp"
* Added new hooks/filters

= 1.1.10 - 2016-03-30 =
* "Filter by product" allows to export only filtered products
* Fixed bug for meta fields with spaces in title
* Fixed bug for XML/JSON fields ( unable to rename )
* Added new hooks/filters
* Added extra UI alerts
* Added tab "Profiles" (Pro version)

= 1.1.9 - 2016-03-14 =
* Disable Object Cache during export
* Added fields : Line Subtotal, Order Subtotal, Order Total Tax

= 1.1.8 - 2016-03-07 =
* Added link to PRO version
* Fixed few minor bugs

= 1.1.7 - 2016-02-18 =
* Added options "prepend/append raw XML"
* Added column "Item#" for Products
* Fixed custom fields for Products

= 1.1.6 - 2016-02-04 =
* Added column "Total weight" (to support Royal Mails DMO)
* Display progressbar errors during export

= 1.1.5 - 2016-01-21 =
* Fixed another bug for product custom fields

= 1.1.4 - 2016-01-13 =
* Added custom css to our pages only

= 1.1.3 - 2015-12-18 =
* Ability to export selected orders only
* Fixed bug for product custom fields
* Fixed progressbar freeze

= 1.1.2 - 2015-11-11 =
* Fixed path for temporary files
* Export coupon description

= 1.1.1 - 2015-10-27 =
* Export products taxonomies

= 1.1.0 - 2015-10-06 =
* Order exported records by ID
* Corrected extension for xlsx files
* Fixed bug for "Set up fields"

= 1.0.6 - 2015-09-28 =
* Attribute filter shows attribute values.
* Shipping filter shows values too.

= 1.0.5 - 2015-09-09  =
* Filter by product taxonomies

= 1.0.4 - 2015-09-04 =
* Export to XLS

= 1.0.3 =
* Partially support outdated Select2 (some plugins still use version 3.5.x)
* Fixed problem with empty file( preview was fine)

= 1.0.2 - 2015-08-25 =
* Added Progress bar
* Added new csv option "Populate other columns if products exported as rows"

= 1.0.1 - 2015-08-11 =
* Added Russian language


= 1.0.0 - 2015-08-10  =
* First release.