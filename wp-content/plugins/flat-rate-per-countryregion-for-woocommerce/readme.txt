=== Flat Rate per State/Country/Region for WooCommerce ===
Contributors: webdados
Tags: woocommerce, shipping, delivery, ecommerce, e-commerce, country, countries, region, continent, continents, world, states, state, districts, webdados
Author URI: https://www.webdados.pt
Plugin URI: https://www.webdados.pt/produtos-e-servicos/internet/desenvolvimento-wordpress/flat-rate-per-countryregion-woocommerce-wordpress/
Requires at least: 4.7
Tested up to: 5.0.1
Stable tag: 2.5.3.1

This plugin allows you to set a flat delivery rate per States, Countries or World Regions on WooCommerce.

== Description ==

If you need a simple way to specify a delivery flat rate, based on the state and/or country and/or world region of the delivery address, on WooCommerce for WordPress, this plugin is for you!

A simple example of this plugin usage is to set a value for delivery in a specific state (e.g. Lisbon), the rest of your own country (e.g. Portugal), a different value for your continent (e.g. Europe) and a fallback one for the rest of the world.

You can create groups for states, countries and world regions and specify delivery rates for them.

For each group you can choose either to apply the shipping fee for the whole order or multiply it per each item. You can also set a total order value from which the shipping will be free.

= Are you already issuing automatic invoices on your WooCommerce store? =

If not, get to know our new plugin: [Invoicing with InvoiceXpress for WooCommerce](https://wordpress.org/plugins/woo-billing-with-invoicexpress/)

= Features: =

* Create any number of states groups/rules and set a specific delivery rate for each group;
* Create any number of countries groups/rules and set a specific delivery rate for each group;
* Create any number of world regions groups/rules and set a specific delivery rate for each group;
* Specify a fallback "Rest of the World" rate for any destinations not specified on the groups;
* For each group/rule, apply the shipping fee for the whole order or multiply it per each item;
* For each group/rule, set total order value from which the shipping is free;
* For each group/rule, set shipping classes for which the shipping is free;
* For each group/rule, set shipping classes for which the shipping is not available (this may be useful for disabling shipping of certain products to certain destinations, if this plugin is the only one being used for shipping cost calculations);
* WPML Compatible;
* PHP7 tested and compatible;
* Not to be used with WooCommerce 2.6+ Shipping Zones (but an alternative to it);

== Installation ==

* Use the included automatic install feature on your WordPress admin panel and search for "Flat Rate per Country/Region for WooCommerce";
* Go to WooCommerce > Settings > Shipping > Flat Rate per State/Country/Region and enable this shipping method;
* Set up the rate for the "Rest of the World"
* Define how many world region, country and/or state different rules you'll need and "Save changes"
* You'll now be able to set up the rates settings for each regions/country/state rule independently (don't forget to "Save changes" again when you're done)

== Frequently Asked Questions ==

= Is this shipping method compatible with WooCommerce 2.6+ Shipping Zones? =

Not with the Shiping Zones, no, but it's compatible with WooCommerce 2.6+.
This plugin was designed pre WooCommerce 2.6 in part to solve the problem that the new Shipping Zones have now solved. It makes no sense to make it work with the new Shipping Zones, but you can still use this shipping method as an alternative if you want to.
The plugin will still be mantained, but with little to no updates from now on.

= [WPML] How can I translate the Shipping Method title? =

After setting everything up you should go to WPML > String Translation and translate the titles there.

Because of the way the WooCommerce Multilingual plugin registers the Shipping Method titles (assuming each methos can only have one title) and because our plugin changes title depending on the State/Country of destination, the strings that must be translated are the ones inside the "woocommerce_flatrate_percountry" domain and not the ones on the "woocommerce" domain.

= Why is there no more FAQs? =

No other question is frequent. Ask us something ;-)

== Changelog ==

= 2.5.3.1 =
* readme.txt small fix

= 2.5.3 =
* Tested with WooCommerce 3.5.2
* Bumped `WC tested up` tag
* Bumped `Requires at least` tag

= 2.5.2 =
* Bumped `WC tested up to` tag

= 2.5.1 =
* Fix: In some server configurations (suhosin), some settings were not being saved (for WPML users: you will have to recheck your rule names translations)
* Bumped `WC tested up to` tag
* Bumped `Requires at least` tag

= 2.5 =
* It's now possible to exclude some shipping classes from group/rule (this may be useful for disabling shipping of certain products to certain destinations, if this plugin is the only one being used for shipping cost calculations)
* Small fix on WooCommerce 3.3 and above
* Bumped `WC tested up to` tag

= 2.4.9.1 =
* Tested with WooCommerce 3.3
* Bumped `Tested up to` tag

= 2.4.9 =
* Removed the translation files from the plugin `lang` folder (the translations are now managed on [WordPress.org's GlotPress tool](https://translate.wordpress.org/projects/wp-plugins/flat-rate-per-countryregion-for-woocommerce) and will be automatically downloaded from there)
* Tested with WooCommerce 3.2
* Added `WC tested up to` tag on the plugin main file
* Bumped `Tested up to` tag

= 2.4.8.2 =
* Fix fatal error when WC()->countries is still not defined on WooCommerce 3.1.0
* Bumped `Tested up to` tag

= 2.4.8.1 =
* Fixing WordPress.org plugin page because the repo was Cuckoo on the 2.4.8 update

= 2.4.8 =
* Fixed a bug where, in some situations with WPML active, the shipping method label wouldn't show up 

= 2.4.7 =
* Tested with WooCommerce 3.0.0-rc.2
* Bumped `Tested up to` tag
* Small Spanish translation fix

= 2.4.6.2 =
* Bumped `Tested up to` tag

= 2.4.6.1 =
* Version number fix

= 2.4.6 =
* It's now possible to disable this shipping method for the "Rest of the World" 
* Bumped "Requires at least" and "Tested up to" versions
* Updated FAQ regarding WooCommerce 2.6+ Shipping Zones

= 2.4.5.2 =
* Fix: Some PHP notices were suppressed
* Spanish translation (Gracias Rafa Gallego)

= 2.4.5.1 =
* Version number fix

= 2.4.5 =
* Dutch translation (Thanks to Arman Nassiri)

= 2.4.5 =
* Bug fix: Quick fix to avoid fatal error when used with Polylang. No support for Polylang at this moment.

= 2.4.4 =
* WordPress 4.4, WooCommerce 2.4.12 and PHP 7 compatibility check - All good!
* Bug fix: A warning was generated when the 'State or Country or Region name or "Rest of the World"' option for title was chosen on the configurations page, a region rule exists and that region was chosen by the client on the website frontend.

= 2.4.3 =
* Bug fix: The countries list on the configuration screen was not hidden by default
* Bumped required WordPress version to match the same requirements WooCommerce has (4.1)

= 2.4.2 =
* WPML: Reminder to translate the strings after saving the settings
* Right sidebar on the settings page with technical support links

= 2.4.1 =
* Better installation instructions
* Added WPML instructions to the FAQ
* Bug fix: Taxes were always being added to the cost regardless of the settings
* Bug fix: All shipping method labels were being override when WPML is active and not only this plugin ones
* Fix: PHP notice when creating new State rules on the settings page

= 2.4 =
* WPML "now the correct way" integration so that the Shipping Method title can be translated
* WooCommerce Product Bundles integration (Thanks to Mathias Philippe https://wordpress.org/support/profile/mphilippe)
* European Union countries now fetched from WooCommerce
* New special region: European Union + Monaco and Isle of Man (EU VAT)
* Added the list of countries not assigned to any region - For information propouses only
* When setting Shipping Classes for free shipping it's now possible to set either all items on the cart must belong to the classes or if only one is enough to set the fee as free
* Fix: incorrect States rules counter when trying to calculate the rate on the frontend
* Quick Fix: wp_error testing when getting WooCommerce Shipping Classes on Multisite (we still can't get the terms, but the error is now supressed)
* Fix: "get_translated_term" internal function was missing and getting WooCommerce Shipping Classes on a WPML install was throwing a fatal error
* Several minor code syntax tweaks

= 2.3.2 =
* WPML compatibility (beta)
* Fix: Some PHP notices were suppressed

= 2.3.1 =
* Fix: Great Britain was declared on the European Union group as UK and not GB

= 2.3 =
* It's now possible to set a free shipping fee (for the all order), if there's at least one item that belongs to a specific shipping class. This is set per rule.
* Fix: Free shipping fee for "rest of the world" order above some amount was not working

= 2.2 =
* WordPress Multisite support

= 2.1 =
* You can now set a name per each rule and then choose to show that field as the title on the checkout

= 2.0.1 =
* The wrong files were uploaded on 2.0. This is the correct version. Please upgrade.

= 2.0 =
* It's now also possible to set rates for states
* Apply rate "Per order" / "Per item" setting is now individual for each group
* Possibility to set a total order value from which the shipping fee is free, also individual for each group
* New special "European Union" region group
* Some tweaks on the settings screen for improved usability
* Fix: changed the textdomain argument to string instead of variable

= 1.4.2 =
* Fix: In some server configurations the single countries field was not being saved to the database

= 1.4.1 =
* Fix: Minor localization issues solved

= 1.4 =
* It's now possible to remove the "(Free)" text from the shipping label if the rate is 0. This can be useful if you need to get a quote for the shipping cost from the carrier. (Thanks Saad Sohail)
* (Temporary) plugin icon for the plugin installer
* Fix: the setlocale() function now uses only LC_COLLATE instead of LC_ALL in order to get the countries array sorted correctly in all languages. LC_ALL would cause interference with WPML. (Thanks Mihai Grigori from OnTheGoSystems)

= 1.3 =
* Fix: Free shipping was not possible because stupid php considers "0" as empty. (Thanks Simone Mastrogiacomo)

= 1.2 =
* It's now possible to choose either to apply the shipping fee for the whole order or multiply it per each item.
* There's new options regardin the title the costumer will see suring checkout.

= 1.1 =
* It's now possible to choose either to show the region or country name when a region or "rest of the world" rate is used.
* Fix: make sure the price fields now respect the localized settings.
* Fix: when changing countries on the checkout page the plugin does not stall the ajax call anymore and the totals are updated.

= 1.0 =
* Initial release.