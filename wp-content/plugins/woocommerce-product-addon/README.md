# nm-woocommerce-personalized-product
=== WooCommerce Personalized Product Option Manager ===
Contributors: nmedia
Tags: woocommerce, pesonalized products, variations
Donate link: http://www.najeebmedia.com/donate
Requires at least: 3.5
Tested up to: 4.5
License: GPLv2
License URI: http://www.gnu.org/licenses/gpl-2.0.html


<h>Description</h>

WooCommerce Personalized Product Option Plugin allow site admin to add unlimited input fields on product page. Client personalized these product like choose a color for T-Shirt, add text on Mug, upload design for Visiting Cards etc before checkout. There are total 14 different types of inputs available in this plugin with Awesome File/Image upload form.

== Installation ==
1. Upload plugin directory to the `/wp-content/plugins/` directory
2. Activate the plugin through the `Plugins` menu in WordPress
3. After activation, you can set options from `Settings -> PersonalizedWoo` menu


== Changelog ==
= 5.7 May 9, 2016 ==
* Bug fixed: Fileuploder does not work when used in Conditional fields on iPhone/iPad, it's fixed now
= 5.6 April 27, 2016 ==
* Bug fixed: Image Cropping header and footer area is not visible
= 5.5 April 14, 2016 ==
* Bug fixed: image cropping was not working for certain links, it's fixed now.
= 5.4 March 21, 2016 ==
* Feature: Color Palette Added
= 5.3 March 10, 2016 ==
* Bug fixed: Image type validation was not working, now it's fixed
= 5.2 March 2, 2016 ==
* Feature: Color Picker now has better visual rather than only Hexcode
= 5.1 February 28, 2016 ==
* Feature: Now all images edited with Aviar Addon are renamed with orderid-product-filename when checkout completed
* Feature: Product page/Add to cart button is blocked when images/files are being uploaded.
= 5.0 January 22, 2016 (Major Update) =
* Bug fixed: Fixed price variation issue was not adding to cart, Now it is
* Bug fixed: Product speed was sometime slow down when use so many priced options, it's optimized to reduce un-necessary delay
* Bug fixed: Variable product priced option were not added correct price, now it's fixed
* Adjustment: Price tag now only shown on top after title for all product types
* Design issue: If width is not provided then it's set to 100% to make better layout for all fields
* Design issue: Now radio input is replaced with Select input to select meta group inside product page
* Cleanup: All variables are now properly set so it won't throw any Error, Notices and Warning.
= 4.9 January 19, 2016 =
* Bug fixed: Uploader area height
* Bug fixed: Cart attached meta, long file name
= 4.8 November 25, 2015 =
* Bug fixed: Conditional Logic related bug is removed
= 4.7 November 17, 2015 =
* Bug fixed: Conditional bug with hidden fields is removed
= 4.6 October 25, 2015 =
* Bug fixed: Aviar addon thumb size fixed when edit completed
* Feature: Edited image thumb will be displaed on cart page when edited with Aviary
* Bug fixed: Some typo corrected.
= 4.5 October 19, 2015 =
* Bug fixed: Dynamic prices issue fixed
* Feature: Price range will be shown on Shop page for price matrix
= 4.4 October 4, 2015 =
* Bug fixed: price variation bug fixed when added to cart.
= 4.3 18/9/2015 =
* Feature: Percentage now can be used for variations
= 4.2 15/9/2015 =
* Bug fixed: Varation prices delay LOOP is fixed
* Bug fixed: Admin UI issue fixed while extra fields drag and drop
= 4.1 27/8/2015 =
* Bug fixed: Uploaded files thumbs also renamed
* File uploader is now more secure
* BlockUI shown when variations selected
= 4.0 27/6/2015 =
* Some notices and warnings romoved
* More options in admin to clone meta and UI changes
= 3.18 1/6/2015 =
* Aviar editing with new SDK
* Layout changes for uploaded image
* Bug fixed: while importing existing data
= 3.17 22/3/2015 =
* show an alert when thumbs is not generated rather then stuck
= 3.16 21/3/2015 =
* depracated functions remove for woocommerce->add_error
* support for older versions (> 2.1) added
* file upload required bug fixed
= 3.15 4/3/2015 =
* Bug Removed while importing the Meta
= 3.14 3/3/2015 =
* Export and Import Feature added for Prodcut Meta
= 3.13 17/02/2015 =
* bug fixed when '0' is passed as value in meta *
* some warnings removed
* checked agains woocommerce major update 2.3
= 3.12 21/12/2004 =
* One time Fee is now taxable
* auto generate data names for meta inputs
* unique file_name using `wp_unique_filename` function
* conditional field now supported for `image` type input
* thumb size issue fixed when edit with Aviary
= 3.11 20/11/2014 =
* BUG: sometime files not moved to confirmed directory after order completed. Fixed
* BUG: Files upload limits were not working, Fixed
* BUG: Files with long names are trimmed to 35 characters only for display, Fixed
= 3.10 =
* admin settings tweaks
* show/hide option prices for variations
* plupload latest version 2.1.2
* option to display uploaded file thumb in cart 
* wp standard functions added instead plugin’s own
= 3.9 =
* Fixed: Cart total were updated even variation is hidden in conditional logic
* FB import addon is integrated
= 3.8 =
* Feature: set default values for text and textarea
* Feature: Number type input added with max, min and step controlling
* Fixed: image type input now have proper titles
* Fixed: plugin bugging on iphone
* Fixed: generate thumbs with random names. 
* Fixed: selected images will be shown in admin panel
* Fixed: all undefined variables and indexes errors have been removed
= 3.7 =
* Price matrix: define price based on quantity range
* Fixed fee to cart
* Attached file cost can be added into cart
= 3.6 =
* Dynamic prices issued fixed, now all currency symbols and decimals are at correct places
* Layout issues fixed to work with some themes 
= 3.5 =
* BUG Fixed: un-necessary meta values are removed from cart/checkout pages
* Dynamic price handling optimized
= 3.4 =
* Add product_id before uploaded file when order is confirmed
* `_product_attached_files` is removed from Cart page
* BUG: fixed when more then one file is uploaded these are moved to confirmed dir
* BUG: fixed error while duplicating Woo Products.
= 3.3 =
* show edited photo in cart page thumb
* allow bulk meta group to applied on product list 
* autoupload on file select
* BUG: do not show editing tools if disabled
3.2
* User new input framework (classes based)
* Pre uploaded images field
* use color picker field
* croping option
* new uploader
* Mask input 
* Max characters restriction
* Dynamic prices
* Better price/options control
* Remove unpaid order images after one day (check pending) : CP
* Move paid orders into another direcotory
* Rename uploaded images with order number prefix.
* Clone existing meta with one click
* i18n localized ready
* Download link in email
* New date format: dd/mm/yy (with digist year)
* Now Html can be used in title and description
= 3.1 =
* conditional logic for select, radio and checkbox
* BUG Fixe: validation issue with radio type is fixed
= 3.0 =
* New plugin admin interface
* drag & drop input fields
* radio button
* set max/min checkbox selection
* Photo Editing with Aviary
* Unlimited file upload instances
* CSS/Styling editor
* Sections		
* Add customized error message
* Add class name against each input wrapper
* Define width of each field
= 2.0.8 =
* HTML5 Fallback for IE
* Now Simple product meta data can be shown on cart/checkout/email
* Thumbs will be shown on cart/checkout/email
= 2.0.7 =
* Fixed: multiple pricing when using Select input type
= 2.0.6 =
* Added Datepicker input type
* Data labels are more readable
= 2.0.5 =
* now the product meta will be shown in cart page.
= 2.0.4 =
* remove JS bug when uploading and delete file, it won't show old file then
