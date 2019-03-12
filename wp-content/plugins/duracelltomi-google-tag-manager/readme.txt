=== DuracellTomi's Google Tag Manager for WordPress ===
Contributors: duracelltomi
Donate link: https://gtm4wp.com/
Tags: google tag manager, tag manager, gtm, google, adwords, google adwords, google ads, adwords remarketing, google ads remarketing, remarketing, google analytics, analytics, facebook ads, facebook remarketing, facebook pixel, google optimize, personalisation
Requires at least: 3.4.0
Requires PHP: 5.6
Tested up to: 5.0.3
Stable tag: 1.9.2
License: GPLv3
License URI: http://www.gnu.org/licenses/gpl.html

Advanced measurement/advertising tag management and site personalisation for WordPress with Google Tag Manager and Google Optimize

== Description ==

Google Tag Manager (GTM) is Google's free tool for everyone to manage and deploy analytics and marketing tags as well as other code snippets
using an intuitive web UI. To learn more about this tool, visit the [official website](https://www.google.com/analytics/tag-manager/).

This plugin places the GTM container code snippets onto your wordpress website so that you do not need to add this manually.
Multiple containers are also supported!

The plugin complements your GTM setup by pushing page meta data and user information into the so called data layer.
Google's official help pages includes [more details about the data layer](https://developers.google.com/tag-manager/devguide#datalayer).

You can also add your Google Optimize container with the [recommended code setup](https://support.google.com/optimize/answer/7359264?hl=en)

**Some parts of the plugin require PHP 5.6 newer.
PHP 7.0 or newer is recommended.**

Please note that PHP 5.6 is nearing its end of life cycle thus it is recommended to upgrade. If you are not sure which version you are using, please contact
your hosting provider for support.

= GTM container code placement =

The original GTM container code is divided into two parts: The first part is a javascript code snippet that is added to the `<head>`
section of every page of the website. This part is critical to enable all features of GTM, and this plugin helps to place this part
correctly on your site. The second part is an iframe snippet that acts as a failsafe/fallback should users' JavaScript be disabled.
Google recommends – for best performance – to place this code snippet directly after the opening `<body>` tag on each page.
Albeit not ideal, it will work when placed lower in the code. This plugin provides a code placement option for the second code snippet.
Inherently, Wordpress does not offer a straight-forward solution to achieve this, however Yaniv Friedensohn showed me a solution
that works with most themes without modification:

http://www.affectivia.com/blog/placing-the-google-tag-manager-in-wordpress-after-the-body-tag/

I added this solution to the plugin, currently as an experimental option.

Sites using the Genesis Framework should choose the "Custom" placement option. No theme modification is needed for this theme
however, the Google Tag Manager container code will be added automatically.

= Basic data included =

* post/page titles
* post/page dates
* post/page category names
* post/page tag names
* post/page author ID and names
* post/page ID
* post types
* post count on the current page + in the current category/tag/taxonomy
* logged in status
* logged in user role
* logged in user ID (to track cross device behaviour in Google Analytics)
* logged in user email address (to comply with [GTM terms of service](https://www.google.com/analytics/tag-manager/use-policy/) do not pass this on to Google tags)
* site search data
* site name and id (for WordPress multisite instances)

= Browser / OS / Device data =

(beta)

* browser data (name, version, engine)
* OS data (name, version)
* device data (type, manufacturer, model)

Data is provided using the WhichBrowser library: http://whichbrowser.net/

= Weather data =

(beta)

Push data about users' current weather conditions into the dataLayer. This can be used to generate weather-related
audience/remarketing lists on ad platforms and allows for user segmentation in your web analytics solutions:

* weather category (clouds, rain, snow, etc.)
* weather description: more detailed data
* temperature in Celsius or Fahrenheit
* air pressure
* wind speed and degrees

Weather data is queried from Open Weather Map. Depending on your websites traffic, additional fees may apply:
http://openweathermap.org/price

An (free) API key from OpenWeatherMap is required for this feature to work.

ipstack.com is used to determine the site visitor's location. A (free) API key from IPStack.com is required for this feature to work:
https://ipstack.com/product

= Media player events =

(experimental)

Track users' interaction with any embedded media:

* YouTube
* Vimeo
* Soundcloud

DataLayer events can be chosen to fire upon media player load, media is being played, paused/stopped and optionally when
the user reaches 10, 20, 30, ..., 90, 100% of the media duration.

Tracking is supported for embedded media using the built-in oEmbed feature of WordPress as well as most other media plugins
and copy/pasted codes. Players injected into the website after page load are not currently supported.

= Scroll tracking =

Fire tags based on how the visitor scrolls from the top to the bottom of a page.
An example would be to separate "readers" (who spend a specified amount of time on a page) from "scrollers"
(who only scroll through within seconds). You can use these events to fire Analytics tags and/or remarketing/conversion tags
(for micro conversions).

Scroll tracking is based on the solution originally created by

* Nick Mihailovski
* Thomas Baekdal
* Avinash Kaushik
* Joost de Valk
* Eivind Savio
* Justin Cutroni

Original script:
http://cutroni.com/blog/2012/02/21/advanced-content-tracking-with-google-analytics-part-1/

= Google AdWords remarketing =

Google Tag Manager for WordPress can add each dataLayer variable as an AdWords remarketing custom parameter list.
This enables you to build sophisticated remarketing lists.

= Blacklist & Whitelist Tag Manager tags and variables =

To increase website security, you have the option to white- and blacklist tags/variables.
You can prevent specific tags from firing or the use of certain variable types regardless of your GTM setup.

If the Google account associated with your GTM account is being hacked, an attacker could easily
execute malware on your website without accessing its code on your hosting server. By blacklisting custom HTML tags
and/or custom JavaScript variables you can secure the Tag Manager container.

= Integration =

Google Tag Manager for WordPress integrates with several popular plugins. More integration to come!

* Contact Form 7: fire an event upon successful form submission
* WooCommerce:
	* Classic e-commerce:
		* fire an event when visitors add products to their cart
		* capture transaction data to be passed to your ad platforms and/or Analytics
		* capture necessary remarketing parameters for Google AdWords Dynamic Remarketing
	* Enhanced e-commerce (beta):
		*	implementation of [Enhanced E-commerce](https://developers.google.com/tag-manager/enhanced-ecommerce)
		* Does not support promotions since WooCommerce does not have such a feature (yet)
		* Does not support refunds

== Installation ==

1. Upload `duracelltomi-google-tag-manager-for-wordpress` to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Go to Settings / Google Tag Manager and enter your Google Tag Manager container ID and set additional options

== Frequently Asked Questions ==

= How can I ... =

Tutorials for various Google Tag Manager settings and implementation are available on my website:
https://gtm4wp.com/how-to-articles/

= PayPal / 3rd party payment gateway transactions in WooCommerce are not being tracked in Google Analytics =

PayPal and some other 3rd party payment gateways do not redirect users back to your website upon successful transaction by default.
It offers the route back for your customer but it can happen that users close the browser before arriving at your thankyou page
(aka. order received page). This means that neither Google Analytics tags or any other tags have the chance to fire.

Enable auto-return in your payment gateway settings. This will instruct them to show a quick info page after payment
and redirect the user back to your site. This will improve the accuracy and frequency of tracked transactions.

= Why isn't there an option to blacklist tag/variable classes =

Although Google recommends to blacklist tags and variables using classes, people struggle to know
which tags/variables gets affected. Therefore I opted for individual tags and variables rather than classes
on the blacklist tabs.

Regarding variables; ensure they are not part of any critical tags as blacklisting such variables will render said tags useless.

= How can I track scroll events in Google Tag Manager? =

Google Tag Manager supports basic scroll depth tracking based on percentage or pixels natively. This plugin adds
additional scroll tracking events, more focused on capturing the users' intent and/or engagement.

There are five dataLayer events you can use in your rule definitions:

* gtm4wp.reading.articleLoaded: the content has been loaded
* gtm4wp.reading.startReading: the visitor started to scroll. The `timeToScroll` dataLayer variable stores duration since the article loaded (in seconds)
* gtm4wp.reading.contentBottom: the visitor reached the end of the content (not the page!). `timeToScroll` dataLayer variable updated
* gtm4wp.reading.pagebottom: the visitor reached the end of the page. `timeToScroll` dataLayer variable updated
* gtm4wp.reading.readerType: based on time spent since article loaded we determine whether the user is a 'scanner' or 'reader' and store this in the `readerType` dataLayer variable

Example use cases: using these events as triggers, you can fire Google Universal Analytics and/or AdWords remarketing/conversion tags
to report micro conversions and/or to serve ads only to visitors who spend more time reading your content.

= Can I exclude certain user roles from being tracked? =

This is easily managed through GTM itself. If you want to exclude logged in users or users with certain user roles,
use the corresponding dataLayer variable (visitorType) and an exclude filter in Google Tag Manager.

https://gtm4wp.com/how-to-articles/how-to-exclude-admin-users-from-being-tracked/

= How do I put the Google Tag Manager container code next to the opening body tag? =

By default the plugin places the iframe tag in the footer of the page. To change it, go to the plugin's admin section
and select "Custom" from the placement settings. Unless you use the Genesis Framework theme, you will also need to
edit your template files.

Go to `wp-content/plugins/themes/<your theme dir>` and edit `header.php`.
In most cases you will find the opening `<body>` tag here. If you can not find it, contact the author of the theme and
ask for instructions.

Create a new line right below the `body` tag and insert this line of code:

`<?php if ( function_exists( 'gtm4wp_the_gtm_tag' ) ) { gtm4wp_the_gtm_tag(); } ?>`

Be careful not to place this line within any `<div>`, `<p>`, `<header>`, `<article>` tags.
It may break your theme.

There is also an option named "Codeless" which attempts to place the container code correctly
without additional theme tweaks. It may or may not work, this is an experimental feature, use it accordingly.

= Why can't this plugin insert the container snippet after the opening body tag automatically? =

Currently WordPress has two 'commands' or 'hooks' that a programmer can use: one for the `<head>` section and
one for the bottom of `<body>`. There is no way to inject any content after the opening `<body>` tag without manually
editing your template files. Fortunately some theme authors already resolved this so in some cases you do not need
to edit your template.

I suggest that try the Custom placement (easiest) and use Google Tag Assistant Chrome browser extension to check
whether the container code is placed as expected. If it shows an error, go ahead and edit your theme manually.

= Facebook like/share/send button events do not fire for me, why? =

It is a Facebook limitation. Click event tracking is only available for html5/xfbml buttons.
If you or your social plugin inserts the Facebook buttons using IFRAMEs (like Sociable), it is not possible to track likes.

== Screenshots ==

1. Admin panel
2. Basic settings
3. Events
4. Integration panel
5. Advanced settings
6. Scroll tracking

== Changelog ==

= 1.9.2 =

* Fixed: possible PHP warning if geo data or weather data feature is turned on

= 1.9.1 =

* Fixed: handle out of quota cases with ipstack queries properly
* Fixed: proper YouTube tracking for WordPress sites and WordPress multisites installed in a subdirectory
* Fixed: properly detect client IP address and also properly escape this data while using it
* Fixed: WooCommerce checkout steps after page load did not include products in the cart
* Fixed: checkout step events for payment mode and shipping type not always fired
* Fixed: the CMD on Mac will be treated just like the Ctrl key on Windows while processing the product click event in the WooCommerce integration (thy for luzinis)
* Fixed: add currencyCode to every ecommerce action in WooCommerce integration
* Fixed: better WooCommere Quick View integration
* Fixed: possible cross site scripting vulnerability if site search tracking was enabled due to not properly escaped referrer url tracking
* Changed: code cleanup in WooCommerce integration

= 1.9 =

* Added: initial support for AMP plugin from Automattic (thx koconder for the contribution!)
* Added: option to remove tax from revenue data on order received page of WooCommerce
* Added: WooCommerce enhanced ecommerce datasets now include stock levels
* Added: new productIsVariable data layer variable is set to 1 on variable WooCommerce product pages
* Added: product impressions can now be split into multiple chunks to prevent data loss on large product category and site home pages  (thx Tim Zook for the contribution!)
  * IMPORTANT! You will need to update your GTM setup, please read the new Step 9 section of the [setup tutorial page](https://gtm4wp.com/how-to-articles/how-to-setup-enhanced-ecommerce-tracking).
* Added: you can now disable flagging of WooCommerce orders as being already tracked once. In same cases (with iDeal for example) you may need this to make purchase tracking to work.
* Added: uninstalling the plugin will now remove configured plugin options from database
* Added: new advanced plugin option: data layer variable visitorDoNotTrack will include 1 if the user has set the [do not track flag](https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/DNT) in his/her browser
* Added: new data layer event when a user has logged in on the frontend: gtm4wp.userLoggedIn
* Added: new data layer event when a new user has registered on the frontend: gtm4wp.userRegistered
* Added: new advanced plugin option: move data layer declaration and Google Tag Manager container as close as possible to the beginning of the HTML document
* Added: better WP Rocket support
* Updated: Full Google Optimize support. Now the plugin can load your Google Optimize container with the [recommended code placement](https://support.google.com/optimize/answer/7359264?hl=en)
* Updated: moved most of the inline JavaScript codes into separate .js files which should help cache plugins to do their job much better when my plugin is active
* Fixed: wrong ecomm_pagetype on product search result pages
* Fixed: PHP notice in some cases when geo data was not loaded properly
* Fixed / Added: freegeoip.net was rebranded to ipstack.com and an API key is needed now even for free usage. You can now add your API key so that weather data and geo data can be added into the data layer
* Warning: some plugin features will be remove from v1.10, most of them can be tracked now using pure Google Tag Manager triggers:
  * Social actions
  * Outbound link click events
  * Download click events
  * Email click events
* Warning: PHP 5.6 is now the minimum recommended version to use this plugin. I advise to move to PHP 7.x

= 1.8.1 =

* Added: new visitorIP data layer variable to support post-GDPR implementations where for example internal traffic exclusion has to be made inside the browser
* Fixed: JavaScript error around the variable gtm4wp_use_sku_instead
* Fixed: added _ as a valid character for gtm_auth GTM environment variable
* Fixed: corrected typo - gtm4wp.checkoutStepE**E**C
* Fixed: two strings were not recognized by WordPress Translate on the admin page
* Fixed: some other plugins call found_variation event of WooCommerce without product variation data being included
* Fixed: product name included variation name on order received page which broke GA product reports
* Fixed: in some cases, no contact form 7 data was being passed to the gtm4wp.contactForm7Submitted event
* Updated: added CDATA markup around container code for better DOM compatibility
* Updated: removed 'SKU:' prefix text from classic ecommerce dimension as it broke some enhanced ecommerce reports

= 1.8 =

* Fixed: weather data tracking codes could result in fatal PHP error
* Fixed: cart events did to fire while user pressed the Enter key in quantity fields
* Fixed: contact form 7 changed some code which prevented successful form submission tracking
* Changed: links to plugin website updated
* Changed: gtm4wp.cf7formid data layer variable now includes the ID of the form in WordPress
* Added: gtm4wp.cf7inputs includes data that has been filled in the form
* Added: [WooCommerce compatibility headers](https://docs.woocommerce.com/document/create-a-plugin/#section-10)
* Added: admin warning for WooCommerce 2.x users. This plugin will drop support for WooCommerce 2.x soon
* Added: postFormat data layer variable on singular pages
* Added: customer* data layer variables with stored billing and shipping data, total number of orders and total value of those orders (needs WooCommerce 3.x)
* Added: geo* data layer variables to get country, city, lat-lon coordinates of the visitor
* Added: visitorUsername data layer variable with the username of the logged in user
* Added: more detailed checkout reporting for WooCommerce sites
  * Add gtm4wp.checkoutStepEEC to your Ecommerce Helper trigger
  * Change a typo: gtm4wp.checkoutOptionE**C**C => gtm4wp.checkoutOptionE**E**C
* Added: option to include full product category path in enhanced ecommerce reporting (can cause performance issues on large sites!)
* Added: initial support for [Google Tag Manager Environments](https://support.google.com/tagmanager/answer/6311518?hl=en)
* Added: support for [WooCommerce Quick View plugin](https://woocommerce.com/products/woocommerce-quick-view/)
* Updated: description of code placement options to clarify what this option does
* Updated: cleanup of readme.txt, spelling and grammar improvements
* Updated: bundled WhichBrowser lib v2.0.32


= 1.7.2 =

* Fixed: in some cases, the remove item from cart link in a WooCommerce cart was not altered properly with additional tracking codes
* Fixed: product categories were empty in the cart, on the checkout pages and on the order received page for product variations
* Fixed: checkout option data included checkout step #1 if cart page was setup to be the first
* Fixed: even more WooCommerce 3.x compatibility
* Added: registration date of the logged in user can be added to the data layer
* Updated: geoplugin.net has been replaced by freegeoip.net for weather tracking which has far better quota for free usage
* Updated: AdWords dynamic remarketing data layer items on a WooCommerce product page will be shown for the root product as well on variable product pages
* Updated: Selecting a product variation will include the price of the product in AdWords dynamic remarketing data layer items
* Updated: minor code cleanup

= 1.7.1 =

* Fixed: PHP 5.3 compatible syntax in frontend.php
* Fixed: PHP error using classic ecommerce with WooCommerce 2.6.x
* Updated: Added data-cfasync='false' to all <script> elements to prevent CloudFlare to load scripts async
* Added: Warning for users of PHP 5.4 or older to consider upgrade (FYI: PHP 5.5 and older versions do not get even security fixes)

= 1.7 =

* Updated: even better WooCommerce 3.0 compatibility (WooCommerce 2.6 still supported but this support ends with the next plugin version)
* Fixed: properly escaping product category name on variable product detail pages
* Fixed: proper data layer stucture in the gtm4wp.changeDetailViewEEC event
* Added: Google Optimize page hiding snippet under Integrations tab
* Added: add to cart data for WooCommerce enhanced ecommerce tracking if user undos a cart item removal (no need to update GTM tags)
* Added: you can now enter a product ID prefix so that IDs can match with IDs in some product feeds generated by other plugins
* Added: option to track cart page as step 1 in enhanced ecommerce checkout funnel

= 1.6.1 =

* Fixed: PHP warning message on WooCommerce cart page
* Fixed: Better compatibility with WooCommerce 2.6.x :-)

= 1.6 =

* Fixed: do not block product list item clicks if ad blocker is enabled
* Fixed: only track product clicks in product lists if link points to the product detail page URL
* Fixed: PHP warning in backlogs 'Undefined variable: gtm4wp_options'
* Added: product variation support in WooCommerce integration (enhanced ecommerce implementations should add the GTM event gtm4wp.changeDetailViewEEC to the ecommerce event trigger)
* Updated: better WooCommerce 3.0 compatibility

= 1.5.1 =

* Fixed: clicks on products in product list pages redirected to undefined URLs with some themes.

= 1.5 =

Lots of WooCommerce ecommerce codes has been changed and extended, please double check your measurement after upgrading to this version!

* Added: warning message if you are using PHP 5.3 or older. Browser/OS/Device tracking needs 5.4 or newer
* Added: Email address of the logged in user into the visitorEmail dataLayer variable. Remember: to comply with GTM TOS you are not allowed to pass this data towards any Google tag but you can use this in any other 3rd party tag.
* Added: gtm4wp_eec_product_array WordPress filter so that plugin and theme authors can add their own data for enhanced ecommere product arrays
* Fixed: JavaScript error in WooCommerce stores when enhanced ecommerce enabled and a product being clicked in a widget area
* Fixed: Order data not present in some cases on the order received page
* Changed: Extended "User SKUs instead of IDs for remarketing" option to be also applied to ecommerce product data arrays
* Changed: Use wc_clean instead of the deprecated function woocommerce_clean
* Changed: New, divided GTM container implemented - a fixed part in the <head> and an iframe part placed using the container placement option you've set earlier

= 1.4 =

* Fixed: WP CLI error message
* Fixed: wrong dynamic remarketing tagging on cart and checkout pages
* Updated: WhichBrowser library to 2.0.22
* Updated: slightly changed container code snippet to prevent W3 Total Cache to alter the code which breaks proper code execution
* Updated: replaced file_get_contents() usage in weather tracking to wp_remote_get() so that it is more compatible with several WP instances
* Updated: YouTube/Video/Soundcloud tracking now tracks videos not embedded using oEmbed (like videos in a widget area)
* Updated: new Vimeo Player API implemented which should solve several issues
* Changed: adapted W3C HTML5 media player event names which changes some events (needs updating your existing GTM setup):
  * Soundcloud: finish => ended, seek => seeked
  * YouTube: playing => play, paused => pause, playback-rate-change => ratechange
  * Vimeo: seek => seeked
* Added: new placement option - 'off'. This will only generate the data layer but you will need to add the proper GTM container code snippet by hand
* Added: new data layer variable: authorID
* Added: new data layer variable: siteID to be able to track based on blog ID in a multisite environment
* Added: new data layer variable: siteName to be able to track in a multisite environment

= 1.3.2 =

* Fixed: remove cart event not fired in WooCommerce 2.6
* Fixed: ecomm_prodid.push error message on product detail pages
* Fixed: proper tracking of cart actions on the cart page for WooCommerce 2.6
* Fixed: 'Illegal string offset' errors in some cases in the cart
* Fixed: OpenWeatherMap requires a (free) API key now, you can now enter this to use weather data in data layer

= 1.3.1 =

* Fixed: "json_encode() expects parameter 2 to be long, string given" on PHP 5.3 instances
* Fixed: Fatal PHP error in cart if you enabled taxes to be included in your cart

= 1.3 =

Major changes to the Enhanced Ecommerce implementation of the WooCommerce integration!

* Fixed: proper tracking of list positions
* Fixed: opening product detail page in a new window/tab when user pressed the CTRL key
* Fixed: ecomm_totalvalue included the total price of the cart without taxes
* Fixed: ecomm_totalvalue does not take into account the quantity of ordered products on the order received page
* Fixed: php error message on product lists when AdWords dynamic remarketing was enabled on WooCommerce 2.6
* Fixed: added data-cfasync="false" to the GTM container code for better compatibility with CloudFlare
* Added: introducing tracking of list names (general product list, recent products list, featured products list, etc.)
  * Some list names (like cross-sells) will be shown as 'General Product List'. A proposed change in WooCommerce 2.6 will solve that issue
* Added: tracking product lists in widgets
* Added: tracking checkout options (payment and shipment)
* Updated: better add-to-cart / remove-from-cart management in mini cart and while updating cart content
* Updated: added currency code to each enhanced ecommerce call so that currency reporting is OK for multi currency sites
* Updated: replaced usage of get_currentuser() to keep compatibility with WordPress 4.5

= 1.2 =

* Fixed: subtabs on admin page now showing in certain cases
* Fixed: error message when running the site using WP CLI (thanks Patrick Holberg Hesselberg)
* Fixed: some typos on admin page
* Fixed: dismissable notices did not disappear in some cases
* Fixed: tracking of Twitter event cased sometimes JS errors
* Fixed: site search tracking caused sometimes PHP errors when HTTP_REFERER was not set
* Updated: preparation for translate.wordpress.org
* Added: support for multiple container IDs
* Added: added form ID when sending a Contact Form 7 form. Variable name: gtm4wp.cf7formid

= 1.1.1 =

* Fixed: PHP errors in frontend.php and admin.php

= 1.1 =

* Added: track embedded YouTube/Vimeo/Soundcloud videos (experimental)
* Added: new checkbox - use product SKU for AdWords Dynamic Remarketing variables instead of product ID (experimental)
* Added: place your container code after the opening body tag without modifying your theme files (thx Yaniv Friedensohn)
* Added: automatic codeless container code injection for Genesis framework users
* Fixed: Possible PHP error with custom payment gateway (QuickPay) on the checkout page (thx Damiel for findig this)

= 1.0 =

The plugin itself is now declared as stable. This means that it should work with most WordPress instances.
From now on each version will include features labeled as:

* Beta: the feature has been proven to work for several users but it can still have some bugs
* Experimental: new feature that needs proper testing with more users
* Deprecated: this feature will be removed in a future version

If you see any issue with beta or experimental functions just disable the checkbox. Using this error messages should disappear.
Please report all bugs found in my plugin using the [contact form on my website](https://gtm4wp.com/contact).

* Fixed: wrong GTM container code when renaming default dataLayer variable name (thx Vassilis Papavassiliou)
* Fixed: Enhanced Ecommerce product click data was "undefined" in some cases (thx Sergio Alen)
* Fixed: wrong user role detection while adding visitorType to the dataLayer (thx Philippe Vachon-Rivard)
* Changed: only add visitorId to the dataLayer if there is a logged in user
* Added: feature labels so that you can see beta, experimental and deprecated features
* Deprecated: outbound click, email click and download click events. You should use GTM trigger events instead

= 0.9.1 =

* Fixed: PHP error message: missing get_shipping function using WooCommerce 2.3.x

= 0.9 =

* Added: visitorId dataLayer variable with the ID of the currently logged in user to track userID in Google Analytics
* Added: WordPress filter hook so that other templates and plugins can get access to the GTM container code before outputting it
* Fixed: 'variation incorrect' issue by Sharken03
* Fixed: error messages in WooCommerce integration when product has no categories
* Fixed: add_inline_js errors in newer versions of WooCommerce
* Fixed: error message when some device/browser/OS data could not be set
* Fixed: tracking Twitter events was broken

= 0.8.2 =

* Fixed: broken links when listing subcategories instead of products (thanks Jon)
* Fixed: wheather/weather typo (thanks John Hockaday)
* Fixed: wrong usage of get_the_permalink() instead of get_permalink() (thanks Szepe Viktor)

= 0.8.1 =

* Fixed: PHP error in enhanced ecommerce implementation when using layered nav widget

= 0.8 =

* Updated: Added subtabs to the admin UI to make room for new features :-)
* Updated: WhichBrowser library to the latest version
* Added: You can now dismiss plugin notices permanently for each user
* Added: weather data. See updated plugin description for details
* Added: Enhanced E-commerce for WooCommerce (experimental!)
* Fixed: PHP notice in frontend.php script. Credit to Daniel Sousa

= 0.7.1 =

* Fixed: WooCommerce 2.1.x compatibility

= 0.7 =

* Updated/Fixed: dataLayer variables are now populated at the end of the head section. Using this the container code can appear just after the opening body tag, thus Webmaster Tools verification using Tag Manager option will work
* Added: blacklist or whitelist tags and macros to increase security of your Tag Manager setup


= 0.6 =

* Updated: better add-to-cart events for WooCommerce, it includes now product name, SKU and ID
* Added: browser, OS and device data to dataLayer variables
* Added: postCountOnPage and postCountTotal dataLayer variables to track empty categories/tags/taxonomies

= 0.5.1 =

* Fixed: WooCommerce integration did not work on some environments

= 0.5 =
* Added: scroll tracking
* Fixed: social tracking option on the admin panel was being shown as an edit box instead of a checkbox
* Fixed: WooCommerce transaction data was not included in the dataLayer if you selected "Custom" code placement
* Fixed: do not do anything if you enabled WooCommerce integration but did not activate WooCommerce plugin itself
* Updated: do not re-declare dataLayer variable if it already exists (because another script already created it before my plugin was run)

= 0.4 =
* Added: you can now select container code placement. This way you can insert the code snippet after the opening body tag. Please read FAQ for details
* Added: initial support for social event tracking for Facebook and Twitter buttons. Please read FAQ for details
* Updated: event name on successful WooCommerce transaction: OrderCompleted -> gtm4wp.orderCompleted
* Fixed: frontend JS codes did not load on some WordPress installs

= 0.3 =
* Updated: admin page does not show an alert box if Tag Manager ID or dataLayer variable name is incorrect. Instead it shows a warning line below the input field.
* Updated: rewritten the code for WooCommerce dynamic remarketing. Added tag for homepage and order completed page.

= 0.2 =
* ! BACKWARD INCOMPATIBLE CHANGE ! - Names of Tag Manager click events has been changed to comply with naming conventions:
	* ContactFormSubmitted -> gtm4wp.contactForm7Submitted
	* DownloadClick -> gtm4wp.downloadClick
	* EmailClick -> gtm4wp.emailClick
	* OutboundClick -> gtm4wp.outboundClick
	* AddProductToCart -> gtm4wp.addProductToCart
* Updated: click events are now disabled by default to reflect recently released Tag Manager auto events. I do not plan to remove this functionality. You can decide which solution you would like to use :-)
* Updated: language template (pot) file and Hungarian translation
* Added: new form move events to track how visitors interact with your (comment, contact, etc.) forms
* Added: event names to admin options page so that you know what events to use in Google Tag Manager
* Added: Google Tag Manager icon to admin settings page
* Added: Settings link to admin plugins page
* Fixed: null value in visitorType dataLayer variable if no logged in user exists (now 'visitor-logged-out')

= 0.1 =
* First beta release

== Upgrade Notice ==

= 1.9.2 =

Fixed possible PHP warning if geo data or weather data feature is turned on

= 1.9.1 =

Bugfix version

= 1.9 =

New AMP GTM support, full Google Optimize support, lots of WooCommerce tracking improvements.

= 1.8.1 =

Bugfix version fixing some issues around WooCommerce tracking and GTM environments. Also adds IP address into the data layer.

= 1.8 =

Lots of new features and some important changes, please read the changelog to ensure your measurement does not break

= 1.7.2 =

Bugfix release: many little fixes, event better WooCommerce 3.x compatibility

= 1.7.1 =

Bugfix release: better PHP 5.3 and WooCommerce 2.6.x compatibility

= 1.7 =

Better WooCommerce 3.x compatibility and new features

= 1.6.1 =

Bugfix release.

= 1.6 =

If you are using WooCommerce and enhanced ecommerce, please add gtm4wp.changeDetailViewEEC to the ecommerce helper trigger

= 1.5.1 =

Fixed: clicks on products in product list pages redirected to undefined URLs with some themes.

= 1.5 =

Lots of WooCommerce ecommerce codes has been changed and extended, please double check your measurement after upgrading to this version!

= 1.4 =

Several additions and fixes, breaking changes on media player tracking, please read changelog before upgrade

= 1.3.2 =

Quickfix release for 1.3.x: major changes and improvements in the enhanced ecommerce implementation for WooCommerce. If you are already using this beta feature, please read the changelog before upgrading!

= 1.3.1 =

Quickfix release for 1.3: major changes and improvements in the enhanced ecommerce implementation for WooCommerce. If you are already using this beta feature, please read the changelog before upgrading!

= 1.3 =

Major changes and improvements in the enhanced ecommerce implementation for WooCommerce. If you are already using this beta feature, please read the changelog before upgrading!

= 1.2 =

New release with lots of fixes from the past months and new features like multiple container support!

= 1.1.1 =

This is a bugfix release to address some issues with v1.1

= 1.1 =

New! Track popular media players embedded into your website!

= 1.0 =

First stable release, please read changelog for details!

= 0.9.1 =

Bugfix release for WooCommerce users with ecommerce tracking enabled

= 0.9 =

Many bug fixes, important fixes for WooCommerce users

= 0.8.2 =

Another bugfix release for WooCommerce users with Enhanced Ecommerce enabled

= 0.8.1 =

Bugfix release for WooCommerce users with Enhanced Ecommerce enabled

= 0.8 =

This version introduces Enhanced E-commerce implementation for WooCommerce. Please note that this
feature of the plugin is still experimental and the feature of Google Analytics is still in beta.
Read the plugin FAQ for details.

= 0.7.1 =

If you are using WooCommerce and updated to 2.1.x you SHOULD update immediately.
This release includes a fix so that transaction data can be passed to GTM.

= 0.7 =

Improved code so that Webmaster Tools verification can work using your GTM container tag.
Blacklist or whitelist tags and macros to increase security of your Tag Manager setup.
Fixed: WhichBrowser library was missing from 0.6

= 0.6 =

Improved add-to-cart events for WooCommerce, added browser/OS/device infos and post count infos.

= 0.5.1 =

Bug fix release for WooCommerce users.

= 0.5 =
Besides of some fixes this version includes scroll tracking events for Google Tag Manager.

= 0.4 =
Important change: Tag Manager event name of a WooCommerce successful order has been changed.
See changelog for details.

= 0.3 =
This is a minor release. If you are using WooCommerce you should update to include more accurate AdWords dynamic remarketing feature.

= 0.2 =
BACKWARD INCOMPATIBLE CHANGE: Names of Tag Manager click events has been changed to comply with naming conventions.
See changelog for details. Do not forget to update your Tag Manager container setup after upgrading this plugin!

= 0.1 =
This is the first public beta, no upgrade is needed.
