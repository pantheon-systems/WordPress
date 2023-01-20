=== Blocksy Companion ===
Tags: widget, widgets
Requires at least: 5.2
Requires PHP: 7.0
Tested up to: 6.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
Stable tag: 1.8.67

== Description ==

Blocksy Companion is a plugin that turns Blocksy theme into a powerful Swiss army knife.
It runs and adds its enhancements only if the Blocksy theme is installed and active.

= Minimum Requirements =

* WordPress 5.0 or greater
* PHP version 7.0 or greater

== Installation ==

1. Upload `Blocksy-Companion-version_number.zip` to the `/wp-content/plugins/` directory and extract.
2. Activate the plugin by going to **Plugins** page in WordPress admin and clicking on **Activate** link.

== Changelog ==
1.8.67: 2023-01-19
- Improvement: Ensure admin_body_class filter is called correctly
- Improvement: Earlier computation of trending posts results
- Improvement: Pass meta_value and meta_key fields to the blocksy_posts shortcode query

1.8.66: 2023-01-11
- Improvement: Better header sticky calculation with very high elements in the rows
- Fix: Negative margin should not break sticky header calculations

1.8.65: 2022-12-29
- Improvement: General fixes and improvements

1.8.64: 2022-12-28
- Improvement: General fixes and improvements

1.8.63: 2022-12-22
- Improvement: Correctly re-apply sticky container height in customizer
- Improvement: Don't output widgets heading tag if title is empty

1.8.62: 2022-12-15
- Improvement: More reliable checking of taxonomy in conditions manager

1.8.61: 2022-12-09
- Improvement: General fixes and improvements

1.8.60: 2022-12-08
- Improvement: Correctly output sticky row background image on responsive devices
- Improvement: Better calculation for sticky shrink on responsive devices

1.8.59: 2022-11-24
- Improvement: Better handle the integration with Nextend Social plugin and account modal

1.8.58: 2022-11-17
- Improvement: Correct redirect to dashboard on plugin activation
- Fix: Trending posts taxonomy relationship

1.8.57: 2022-11-10
- Improvement: General fixes and improvements

1.8.56: 2022-11-04
- Improvement: Sticky header shrink with border correctly position floating cart
- Improvement: Better logic for simple XML svg parsing

1.8.55: 2022-11-03
- Improvement: XML strategy for SVG dimensions when simple xml is absent
- Improvement: Correctly recalculate sticky position on page resize

1.8.54: 2022-10-27
- Improvement: Sticky header makes floating cart cut off under some specific circumstances
- Improvement: Ensure special characters in localize data is correctly sanitized
- Improvement: Better integration with Dokan plugin and account modal extension

1.8.53: 2022-10-20
- Improvement: Account modal show password strength in Sign Up tab
- Fix: Cookies consent decline button is not translatable

1.8.52: 2022-10-13
- Improvement: General fixes and improvements

1.8.51: 2022-10-07
- Improvement: Account modal - add show/hide password button in password field
- Fix: Double captcha appears on register modal

1.8.49: 2022-09-22
- Improvement: Better schema.org output for product reviews price and currency

1.8.47: 2022-09-15
- Improvement: Don't print html tags in OpenGraph output
- Improvement: Account modal integration with German Market plugin
- Improvement: Correctly add lostpassword_url action in account modal

1.8.46: 2022-08-18
- Improvement: Subscribe form small accessibility improvement
- Improvement: More reliable integration between header account and Nextend Social Login plugin
- Fix: Enabling header row sticky state border breaks the sticky state shrink

1.8.45: 2022-08-04
- Improvement: Widgets strings - better compatibility with translation plugins

1.8.44: 2022-07-27
- Improvement: Better calculate sticky top position when auto-hide effect is used
- Improvement: Better handling of conditions module when Blocksy is not active

1.8.43: 2022-07-26
- Improvement: Allow picking individual author archives in conditions module
- Improvement: Account modal incorrect overflow on smaller screens

1.8.42: 2022-07-14
- Fix: Account modal breaks if dismissed previously

1.8.41: 2022-07-08
- Improvement: General fixes and improvements

1.8.40: 2022-07-07
- Improvement: Update translation files

1.8.38: 2022-07-07
- Improvement: Icons for the footer contacts element now respect the column alignment
- Improvement: Newsletter subscribe form has better compatibility with accessibility apps
- Fix: Contacts footer element icons now show up correctly

1.8.37: 2022-06-28
- Improvement: Account register form better handle when Dokan plugin is active
- Improvement: Correct calculation of SVG dimensions during the demo import process

1.8.36: 2022-06-21
- Improvement: Automatically reset minified CSS/JS files from WPRocket after update

1.8.35: 2022-06-20
- Improvement: Add custom class argument to `blocksy_posts` shortcode
- Improvement: Display conditions module - search functionality

1.8.34: 2022-06-03
- Improvement: Better handling of empty rows in the sticky logic
- Improvement: Add loading indicator in account modal for all actions
- Improvement: Correctly open account modal when trigger is in offcanvas

1.8.33: 2022-05-25
- Improvement: Stabilize individual post selection in the conditional module

1.8.32: 2022-05-20
- Improvement: Sync for row shrink in sticky header

1.8.31: 2022-05-11
- Improvement: General fixes and improvements

1.8.30: 2022-05-11
- Improvement: General fixes and improvements

1.8.29: 2022-05-10
- Improvement: General fixes and improvements

1.8.27: 2022-04-22
- Improvement: General fixes and improvements

1.8.26: 2022-04-21
- Improvement: General fixes and improvements

1.8.26-beta2: 2022-04-20
- Improvement: General fixes and improvements

1.8.25: 2022-04-08
- Improvement: Solidify usage of classes from Blocksy theme
- Improvement: More robust widgets handling in options importer

1.8.24: 2022-04-07
- Improvement: General fixes and improvements

1.8.23: 2022-04-07
- Improvement: Smarter loading of the account modal
- Improvement: More robust data parsing in options import/export module
- Improvement: Simpler customizer load in CLI commands

1.8.22: 2022-03-25
- Fix: Blocksy posts shortcode respect pagination

1.8.21: 2022-03-23
- Fix: Blocksy posts shortcode affected by pagination if two loops are present on the same page

1.8.20: 2022-03-01
- Fix: Header row shrink does not work when set to boxed layout
- Fix: Cookies consent modal tabindex issue

1.8.19: 2022-02-18
- Improvement: Do not load all users in conditions to avoid memory limit hits
- Improvement: Remove focus lock from cookies consent popup

1.8.18: 2022-02-16
- Improvement: Account modal better compatibility with All In One WP Security plugin
- Improvement: Cookies consent popup focus lock
- Improvement: Integration with CAPTCHA 4WP in account modal

1.8.17: 2022-01-21
- Improvement: Product reviews image aspect ratio filter
- Improvement: Customizer export/import module improvement
- Improvement: Demo content importer module improvement
- Fix: Account modal does not pop up, if account element is in off canvas menu

1.8.17-beta13: 2022-01-19
- Improvement: General fixes and improvements

1.8.16: 2021-12-29
- Improvement: Handle account modal opened from offcanvas menu
- Improvement: Login modal markup improvements
- Improvement: Quote widget avatar image output

1.8.15: 2021-12-24
- Improvement: General fixes and improvements

1.8.14: 2021-12-24
- Fix: Account modal correctly compute current page URL

1.8.13: 2021-12-18
- Improvement: General fixes and improvements

1.8.12: 2021-12-17
- Improvement: General fixes and improvements

1.8.11: 2021-12-16
- Improvement: General fixes and improvements

1.8.10: 2021-12-15
- Improvement: Account modal accessibility improvement
- Improvement: Account modal default color values
- Improvement: Account modal correct input label
- Fix: My account element custom label strings are not added to the WPML
- Fix: Header -> Account element doesn't update the "Customiser state" text

1.8.10-beta1: 2021-12-10
- Improvement: Account modal accessibility improvement
- Improvement: Account modal default color values
- Improvement: Account modal correct input label
- Fix: My account element custom label strings are not added to the WPML
- Fix: Header -> Account element doesn't update the "Customiser state" text

1.8.9.9: 2021-11-26
- Improvement: Account header element add visibility option for logged in/out users
- Improvement: Update sticky header on browser resize

1.8.9.8: 2021-11-18
- Improvement: Account modal add nonce check for login, lostpassword and register forms
- Improvement: A11Y - title markup for header SVG icons

1.8.9.7: 2021-11-16
- Improvement: Better calculation for logo shrink in sticky header

1.8.9.6: 2021-11-16
- New: Integrate account header element with iThemes Security

1.8.9.5: 2021-10-27
- Improvement: Update plugin text domain from blc to blocksy-companion
- Improvement: Product reviews extension add `rel="sponsored"` attribute option
- Fix: Product Reviews title does not show up

1.8.9.4: 2021-10-21
- Improvement: General fixes and improvements

1.8.9.3: 2021-10-20
- New: User condition for post author
- Improvement: Custom post types taxonomy condition per taxonomy
- Improvement: Allow shortcode in widgets (About Me, Quote, Contact Info)
- Improvement: Allow shortcode in contacts header element
- Improvement: Newsletter subscribe extension design options
- Improvement: Product reviews extensions entity option
- Improvement: Demo importer better detect plugin dependencies
- Improvement: Posts widget update comments translation strings

1.8.9.3-beta14: 2021-10-19
- Improvement: Allow shortcode in widgets (About Me, Quote, Contact Info)
- Improvement: Allow shortcode in contacts header element
- Improvement: Newsletter subscribe extension design options
- Improvement: Demo importer better detect plugin dependencies
- Improvement: Posts widget update comments translation strings

1.8.9.2: 2021-10-01
- Improvement: Account header element aria-label attribute

1.8.9.1: 2021-09-30
- New: Trending posts module title option to change wrapper tag

1.8.9: 2021-09-21
- Improvement: Customizer export/import functionality

1.8.8.8: 2021-09-17
- Improvement: General fixes and improvements

1.8.8.7: 2021-09-14
- Improvement: General fixes and improvements

1.8.8.6: 2021-09-09
- New: Product reviews extension add Review Entity option

1.8.8.5: 2021-09-07
- Improvement: Product reviews extension schema org markup

1.8.8.4: 2021-09-03
- Improvement: Better check for current language in conditions module

1.8.8.3: 2021-08-31
- Improvement: Proper image attachment id default

1.8.8.2: 2021-08-30
- Improvement: Block widgets and legacy widgets integration
- Improvement: Post reviews extension schema org markup
- Improvement: Social widget add nofollow option
- Fix: Upon deactivation of companion pro and selecting contact support, it shows a restricted webpage

1.8.8.1: 2021-08-23
- New: Schema org markup for product reviews
- Fix: Handle missing plugin in starter sites installer

1.8.8: 2021-08-20
- New: Random order for posts widget
- Fix: Trending module products category source
- Fix: Header account items label position
- Fix: WP Optimize clean caches correctly

1.8.8-beta7: 2021-08-17
- Fix: Header account items label position
- Fix: WP Optimize clean caches correctly

1.8.7.5: 2021-08-02
- Improvement: General fixes and improvements

1.8.7.4: 2021-07-30
- Improvement: General fixes and improvements

1.8.7.3: 2021-07-29
- Improvement: General fixes and improvements

1.8.7.3-beta2: 2021-07-28
- Improvement: General fixes and improvements

1.8.7.2: 2021-07-26
- Improvement: Properly compute current url for lazy loaded account html

1.8.7.1: 2021-07-23
- Improvement: General fixes and improvements

1.8.7: 2021-07-22
- New: Account modal inputs border and background color options
- Improvement: Allow opening account modal from custom places with a specific initial view

1.8.7-beta6: 2021-07-16
- New: Account modal inputs border and background color options
- Improvement: Allow opening account modal from custom places with a specific initial view

1.8.7-beta4: 2021-07-08
- New: Account modal inputs border and background color options

1.8.6.4: 2021-06-24
- Improvement: Add CSRF nonce check for customizer importer

1.8.6.3: 2021-06-16
- Improvement: General fixes and improvements

1.8.6.2: 2021-06-11
- New: Trending posts extension custom module label option
- New: Integration with Simple Custom Post Order plugin
- New: Implement filter for blocksy posts shortcode wp query args
- New: Header account item redirect URL filter
- Improvement: More options for the trending posts block
- Fix: Social widget alignment when inside footer column

1.8.6.2-beta8: 2021-06-09
- New: Integration with Simple Custom Post Order plugin
- New: Implement filter for blocksy posts shortcode wp query args
- New: Header account item redirect URL filter
- Improvement: More options for the trending posts block
- Fix: Social widget alignment when inside footer column

1.8.6.1: 2021-05-12
- Improvement: General fixes and improvements

1.8.6: 2021-05-11
- Improvement: General fixes and improvements

1.8.6-beta1: 2021-05-07
- Improvement: General fixes and improvements

1.8.5: 2021-05-01
- Fix: Correctly clean SiteGround caches

1.8.4: 2021-05-01
- Improvement: General fixes and improvements

1.8.0: 2021-04-28
- New: Account custom link pass through WPML
- New: Ratio option for Advertisement widget
- New: Header transparent/sticky condition filter
- New: Implement PolyLang, TranslatePress and WPML conditions
- Improvement: Starter sites improvements
- Improvement: Product reviews affiliate link - target option
- Improvement: Account element - sign up functionality compatibility with WooCommerce
- Improvement: Starter site importer minor improvements
- Fix: Correctly initialise widgets in customizer
- Fix: Don't look at current screen when inserting sticky CSS
- Fix: Product reviews extension featured image
- Fix: Sticky header issue when logo has margin

1.8.0-beta6: 2021-04-23
- New: Implement PolyLang, TranslatePress and WPML conditions
- Improvement: Starter sites improvements

1.7.63.8: 2021-04-09
- New: Account custom link pass through WPML
- New: Ratio option for Advertisement widget
- New: Header transparent/sticky condition filter
- New: Implement PolyLang and WPML conditions
- Improvement: Product reviews affiliate link - target option
- Improvement: Account element - sign up functionality compatibility with WooCommerce
- Improvement: Starter site importer minor improvements
- Fix: Correctly initialise widgets in customizer
- Fix: Don't look at current screen when inserting sticky CSS
- Fix: Product reviews extension featured image
- Fix: Sticky header issue when logo has margin

1.7.63.7: 2021-04-06
- New: Account custom link pass through WPML
- New: Ratio option for Advertisement widget
- New: Header transparent/sticky condition filter
- New: Implement PolyLang and WPML conditions
- Improvement: Product reviews affiliate link - target option
- Improvement: Account element - sign up functionality compatibility with WooCommerce
- Improvement: Starter site importer minor improvements
- Fix: Correctly initialise widgets in customizer
- Fix: Don't look at current screen when inserting sticky CSS
- Fix: Product reviews extension featured image
- Fix: Sticky header issue when logo has margin

1.7.63.6: 2021-04-01
- New: Account custom link pass through WPML
- New: Ratio option for Advertisement widget
- New: Header transparent/sticky condition filter
- New: Implement PolyLang and WPML conditions
- Improvement: Product reviews affiliate link - target option
- Improvement: Account element register tab and WooCommerce
- Improvement: Starter site importer minor improvements
- Fix: Correctly initialise widgets in customizer
- Fix: Don't look at current screen when inserting sticky CSS
- Fix: Product reviews extension featured image
- Fix: Sticky header issue when logo has margin

1.7.63: 2021-02-24
- Improvement: Cookies consent extension use SameSite=Lax in cookies
- Fix: Correctly order scripts in trending and cookies consent

1.7.62: 2021-02-23
- Fix: Dashboard JavaScript fixes

1.7.61: 2021-02-23
- Fix: Dashboard JavaScript fixes

1.7.60: 2021-02-22
- Improvement: Refactor account header element
- Fix: Account element custom link

1.7.59.1: 2021-02-20
- Improvement: Refactor account header element
- Fix: Freemius optin screen
- Fix: Account element custom link

1.7.59: 2021-02-16
- Improvement: Introduce slider view to blocksy_posts shortcode
- Improvement: Introduce exclude_term_ids in blocksy_posts shortcode

1.7.58: 2021-02-11
- Fix: Demo importer Brizy handling

1.7.57: 2021-02-09
- Improvement: General fixes and improvements

1.7.56: 2021-02-07
- Improvement: General fixes and improvements

1.7.55: 2021-02-07
- New: Allow multiple post_type entries in blocksy_posts shortcode
- Improvement: Correctly handle images export/import in customizer
- Improvement: WooCommerce: 3 new mini-cart icons
- Fix: Account header element avatar size

1.7.54.2: 2021-02-02
- New: Allow multiple post_type entries in blocksy_posts shortcode
- Improvement: Correctly handle images export/import in customizer
- Improvement: WooCommerce: 3 new mini-cart icons

1.7.54: 2021-01-28
- Improvement: Improve search box when it is placed inside the middle column
- Improvement: Flush permalinks after starter site install finish

1.7.53: 2021-01-26
- New: Newsletter widget - add boxed and default container style
- Improvement: WooCommerce: improve mini cart colors
- Improvement: WooCommerce: Floating cart scroll logic for single product

1.7.52: 2021-01-25
- New: Add term filtering for blocksy_posts shortcode

1.7.51: 2021-01-24
- Improvement: Header: Account element use user display name instead of username
- Improvement: Floating cart take sticky header height into account

1.7.50: 2021-01-24
- Fix: Demo importer: Allow nav menu items of type post_type_archive
- Fix: Allow sideloading SVG images

1.7.49: 2021-01-23
- New: Account element: add AJAX functionality
- New: Account element: add support for social login plugins
- New: Account element: Improve logged in/out state
- New: Filter for `blocksy_output_companion_notice()`
- New: Product Reviews extension
- New: blocksy_posts shortcode with customizer options inherit
- Improvement: Search box text colors
- Fix: Fix sticky effect glitch (fade and slide effects)
- Fix: Improve auto show/hide sticky header
- Fix: Fix multiple file-saver instances on the same page conflict
- Fix: Starter sites importer fix for PHP8
- Fix: Resolve caching plugins problem with cookie consent

1.7.48.1: 2021-01-19
- New: Filter for `blocksy_output_companion_notice()`
- Improvement: Search box text colors
- Fix: Fix sticky effect glitch (fade and slide effects)
- Fix: Improve auto show/hide sticky header
- Fix: Fix multiple file-saver instances on the same page conflict

1.7.47: 2021-01-05
- Improvement: Export/Import for current Elementor Kit

1.7.46: 2020-12-31
- Fix: Issue with affiliate marketing template

1.7.45: 2020-12-31
- New: Add affiliate marketing extension
- New: Add option to export widgets settings
- Fix: Fix account element colors sync not working

1.7.44: 2020-12-28
- New: Author box new social profiles: Pinterest, WordPress, GitHub, Medium, YouTube, Vimeo, VKontakte, Odnoklassniki, TikTok
- Improvement: Allow all declared taxonomies in conditions
- Improvement: Improve WooCommerce banner position

1.7.43: 2020-12-25
- New: Customizer import/export
- Improvement: Shrink logo functionality doesn't work when sticky header is set to Auto Hide/Show

1.7.42: 2020-12-21
- Improvement: General fixes and improvements

1.7.41.1: 2020-12-21
- Fix: Fix freeze for icon picker option

1.7.41: 2020-12-18
- New: Sticky header new behavior (scroll down -> hide header, scroll top -> show header
- Fix: Don't show Config buttons if the extension is not activated

1.7.40.2: 2020-12-15
- New: Sticky header new behavior (scroll down -> hide header, scroll top -> show header
- Fix: Don't show Config buttons if the extension is not activated

1.7.40.1: 2020-12-07
- Improvement: Sticky calculation logic

1.7.40: 2020-12-04
- New: Add ratio option in Posts widget

1.7.39.1: 2020-12-02
- Improvement: Various changes across multiple components

1.7.39: 2020-11-26
- Improvement: Various changes across multiple components

1.7.38: 2020-11-23
- Improvement: Conditions position improvement
- Improvement: Caching plugins logic

1.7.38-beta1: 2020-11-20
- Improvement: Conditions position improvement
- Improvement: Caching plugins logic

1.7.37: 2020-11-20
- Improvement: General improvement

1.7.36: 2020-11-20
- Improvement: implement beta updates delivery

1.7.35: 2020-11-14
- Fix: Minor issue

1.7.34: 2020-11-14
- Fix: Minor issue

1.7.33: 2020-11-14
- Fix: Minor issues

1.7.32: 2020-11-13
- Fix: Show "Global Header" not default
- Fix: Show which footer is customized

1.7.32-beta1: 2020-11-12
- Fix: Show "Global Header" not default
- Fix: Show which footer is customized

1.7.31: 2020-11-06
- Improvement: Insignificant improvements & fixes

1.7.30: 2020-11-06
- Improvement: Various changes

1.7.29: 2020-11-05
- Improvement: Cleanup ShortPixel CDN CSS Cache on dynamic CSS regeneration
- Fix: Header sticky shrink with missing middle row

1.7.28: 2020-11-03
- Fix: Correctly check for logo presence in sticky

1.7.27: 2020-11-03
- Improvement: Contacts widget icons
- Improvement: Header sticky shrink for logo

1.7.27-beta1: 2020-11-03
- Improvement: Contacts widget icons
- Improvement: Header sticky shrink for logo

1.7.26: 2020-10-22
- New: Add ability to duplicate items in Contacts widget
- Improvement: Floating bar RTL support
- Improvement: Allow Contacts widget address bar to accept html

1.7.25: 2020-10-09
- New: Custom post types in conditions
- Fix: Transparent/sticky colors for Account element
- Fix: Quick view variable products

1.7.25-beta1: 2020-10-05
- New: Custom post types in conditions

1.7.24: 2020-09-28
- Improvement: Add Magyar translations
- Improvement: Floating cart validator issue
- Improvement: Remove minified css/js after update for WP Fastest Cache

1.7.23: 2020-09-18
- New: Add color option for close account modal button
- Improvement: Add Hummingbird plugin to clear cache plugin list
- Improvement: Header Account item transparent state color
- Improvement: Avoid redirect to dashboard when activating Companion
- Improvement: Add Swift Performance plugin to clear cache plugin list
- Improvement: Add SG Optimizer - plugin to clear cache plugin list
- Improvement: Widgets CSS improvements
- Fix: Floating cart validations
- Fix: Load CSS in Customizer correctly for Gutenberg

1.7.23-beta3: 2020-09-18
- Improvement: Widgets CSS improvements

1.7.23-beta2: 2020-09-16
- New: Add color option for close account modal button
- Improvement: Add SG Optimizer - plugin to clear cache plugin list
- Fix: Floating cart validations

1.7.23-beta1: 2020-09-07
- Improvement: Add Hummingbird plugin to clear cache plugin list
- Improvement: Header Account item transparent state color
- Improvement: Avoid redirect to dashboard when activating Companion
- Improvement: Add Swift Performance plugin to clear cache plugin list
- Fix: Load CSS in Customizer correctly for Gutenberg

1.7.22: 2020-08-24
- Improvement: Auto-regenerate Dynamic CSS after update
- Improvement: Sticky header + floating cart

1.7.21: 2020-08-20
- Fix: More fixes for transparent header

1.7.20: 2020-08-20
- Fix: Transparent header logic

1.7.19: 2020-08-20
- New: Demo importer integration with Brizy
- Improvement: Avoid inline dynamic CSS
- Improvement: Better Header Builder integration
- Improvement: Header account element
- Fix: Customizer missing Cookies consent options

1.7.19-beta1: 2020-08-13
- Improvement: Avoid inline dynamic CSS
- Improvement: Better Header Builder integration
- Improvement: Header account element
- Fix: Fix Customizer missing Cookies consent options

1.7.18: 2020-07-27
- Improvement: Companion smarter detection for Blocksy theme
- Improvement: Floating cart on out of stock

1.7.18-beta2: 2020-07-24
- Fix: Call missing functions correctly

1.7.18-beta1: 2020-07-24
- Improvement: Companion smarter detection for Blocksy theme
- Improvement: Floating cart on out of stock

1.7.17: 2020-07-20
- Improvement: Account header item hooks
- Improvement: Floating cart disable hooks
- Fix: Elementor starter site importer handle _elementor_data

1.7.16: 2020-07-20
- Improvement: Contacts widget improvements
- Fix: WP Optimize cache cleaner

1.7.16-beta1: 2020-07-18
- Improvement: Contacts widget improvements

1.7.15: 2020-07-14
- Improvement: Demo importer small improvements

1.7.14: 2020-07-13
- Improvement: Quick view popup display on mobile devices
- Improvement: Clear Litespeed & WP Optimize caches on update
- Improvement: Quick view integration with Woocommerce Custom Product Addons
- Improvement: WCAG issues with Advertisement widget
- Improvement: Update translations files

1.7.14-beta2: 2020-07-13
- Improvement: Update translations files
- Fix: Quick view build problems

1.7.14-beta1: 2020-07-10
- Improvement: Quick view popup display on mobile devices
- Improvement: Clear Litespeed & WP Optimize caches on update
- Improvement: Quick view integration with Woocommerce Custom Product Addons
- Improvement: WCAG issues with Advertisement widget

1.7.13: 2020-07-04
- Fix: Add cookie consent via action

1.7.12: 2020-07-03
- New: Add Quick View open options
- Improvement: Simpler flow for transparent header
- Fix: Posts widget count option
- Fix: Correct handling of transparent header conditions
- Fix: EDD for theme beta

1.7.12-beta3: 2020-07-02
- Fix: EDD for theme beta

1.7.12-beta2: 2020-06-30
- New: Add Quick View open options
- Improvement: Simpler flow for transparent header

1.7.12-beta1: 2020-06-30
- Fix: Posts widget count option
- Fix: Correct handling of transparent header conditions

1.7.11: 2020-06-26
- New: Trending Block Custom Post Types
- Improvement: Account header element
- Fix: Properly import _elementor_data
- Fix: Demo installer compatibility with WP importer
- Fix: Update POT file

1.7.11-beta1: 2020-06-22
- Fix: Demo installer compatibility with WP importer

1.7.10: 2020-06-19
- Fix: Properly import _elementor_data

1.7.10-beta1: 2020-06-17
- Fix: Properly import _elementor_data
- Fix: Update POT file

1.7.9: 2020-06-12
- Improvement: Account element
- Fix: Handle Elementor imports
- Fix: Posts widget post_source reference

1.7.9-beta2: 2020-06-09
- Improvement: Account element
- Fix: Handle Elementor imports

1.7.9-beta1: 2020-06-01
- Fix: Posts widget post_source reference

1.7.8: 2020-05-28
- New: Beta updates system
- Improvement: Header account element
- Improvement: Improve demo import attachments on multisite

1.7.7: 2020-05-22
- Improvement: Better support for SVG

1.7.6: 2020-05-21
- Improvement: Notify when connection to demo.ct.com is not fine
- Fix: UTF-8 charset for demo import streamer
- Fix: Mailchimp JSONP error
- Fix: Make sure the demo importer changes elementor CSS to reference proper URLs
- Fix: OG tags for non-single pages

1.7.5: 2020-04-23
- Fix: Proper check for missing extensions

1.7.4: 2020-04-22
- New: Transparent Header with conditions
- Improvement: Mailchimp Widget handle GDPR fields
- Improvement: Floating cart change scroll observe by add to cart button

1.7.3: 2020-04-10
- Improvement: Posts widget meta date
- Improvement: Minor code improvements

1.7.2: 2020-04-03
- Improvement: Posts widget with post type, excerpt, custom query
- Improvement: About Me widget image size option
- Fix: Mailchimp widget submit without js
- Fix: WooCommerce ratio option on import

1.7.1: 2020-03-26
- New: Add Opengraph meta data option
- New: Add alignment & image size option for About Me widget
- Improvement: Refactor Mailchimp extension to better support RTL
- Fix: Themes Live Preview with Blocksy active

1.7.0: 2020-03-09
- Fix: Calendar widget issue
- Fix: Don't include sticky posts in Posts widget with custom query
- Fix: Quote widget inside sidebar with separated widgets
- Fix: Subscribe form allow html in content

1.6.7: 2020-01-30
- New: Posts widget - add custom option to add posts by ID
- Improvement: No longer require `allow_url_fopen` on demo install

1.6.6: 2020-01-17
- Fix: Instagram section sync
- Improvement: Widgets options cleanup

1.6.5: 2020-01-17
- Fix: Critical bug that impeded updates to be received by EDD

1.6.4: 2020-01-16
- Improvement: Update to latest Freemius SDK
- Fix: Properly check for plugin field in update action

1.6.3: 2019-12-29
- Improvement: Generate dynamic css on theme update
- Improvement: Small styles adjustments

1.6.2: 2019-12-27
- Improvement: Move Instagram settings in extension screen
- Improvement: `allow_url_fopen` notification in demos screen
- Improvement: Quote smaller image size
- Improvement: Better social icons handling in widgets
- Improvement: On demo install finish, regenerate dynamic CSS files
- Improvement: On demo install finish, purge all caches

1.6.1: 2019-11-28
- New: Instagram custom transient timeout
- New: Post metabox switch for disabling subscribe form
- Fix: Dynamic css file proper url scheme
- Fix: Woo floating bar styles

1.6.0: 2019-11-22
- New: Add new social networks
- Improvement: Enqueue global.css only in frontend
- Improvement: Posts widget enhancements
- Improvement: Posts widget put image and title in the same anchor tag

1.5.9: 2019-11-13
- New: Add new social icons
- Improvement: Support for WordPress 5.3

1.5.8: 2019-11-07
- New: Implement Mailchimp AJAX submit
- Fix: Instagram widget do not output empty images withour `src`
- Fix: Properly load dinamic styles for floating cart
- Fix: Validation error with attrs spacing

1.5.7: 2019-11-02
- Fix: Always parent theme check

1.5.6: 2019-11-01
- New: Add `wp-editor` for cookies content boxes
- New: Xing social network
- New: Implement dynamic CSS output in files
- Improvement: Quote widget option for disabling author label
- Improvement: Posts widget handling for category
- Fix: Demo install SSL issue
- Fix: Quick View modal add to cart button is not working

1.5.5: 2019-10-18
- New: Floating cart for products
- Improvement: Deactivate demo plugins on demo uninstall
- Improvement: Scope companion to sites, not whole network
- Improvement: Add theme minimum supported version
- Fix: Enable Woo wizzard back

1.5.4: 2019-10-10
- New: Improved content importer for demo install
- New: Modify demo install screen
- Improvement: Customizer sync helpers for handling `CT_CSS_SKIP_RULEDEFAULT`

1.5.3: 2019-10-02
- Fix: Properly access global classes inside namespace

1.5.2: 2019-10-01
- New: Clear cache on theme and plugin update
- New: Add shadow for Mailchimp form
- Improvement: Change class of the panel

1.5.1: 2019-09-24
- Improvement: Better animations for quick view modal

1.5.0: 2019-09-20
- New: Compatibility with Blocksy 1.5.0
- Improvement: Better handling for social icons
- Improvement: Support for responsive color picker

1.1.8: 2019-08-20
- Fix: Remove Google+ social network
- Fix: Scripts loading order, makes sure `ct-events` are always present

1.1.7: 2019-08-12
- New: Mailchimp extension customizable placeholders for fields
- Improvement: Use only one translation domain

1.1.6: 2019-08-05
- Improvement: Move user meta social networks from theme
- Fix: Small fixes in styles

1.1.5: 2019-08-01
- New: Option for changing cookies consent on forms
- Fix: `blocksy_get_colors()` call with proper defaults
- Fix: Do not focus on quantity field on quick view open
- Fix: Initialize quick view on infinite scroll load

1.1.4: 2019-07-15
- Fix: Quick view UI when not in Shop
- Fix: Cookie Notice readme for WP Fastest Cache

1.1.3: 2019-07-10
- Fix: Demo install process avoid notices

1.1.2: 2019-07-05
- Improvement: Add RSS social network to About me
- Fix: About me widget socials

1.1.1: 2019-06-30
- Fix: Proper capabilities check for extensions API

1.1.0: 2019-06-27
- New: Demo Install Engine

1.0.11: 2019-06-18
- New: Author widget
- New: Quote widget
- New: About me widget
- New: Facebook Like box widget
- Improvement: Highlight Blocksy widgets and reorder
- Improvement: Shorten Instagram transients period
- Improvement: Instagram add clear caches button
- Improvement: Type option on posts widget

1.0.10: 2019-06-05
- Improvement: Dashboard visual changes
- Fix: Properly enqueue Elementor CSS
- Fix: Instagram images glitch when lazy load is disabled

1.0.9: 2019-05-23
- New: Introduce a way for extensions to run code on activation and deactivation
- Improvement: Cookie notification integration with W3 Total Cache, WP Super Cache and WP Rocket
- Improvement: Better way to translate content in JSX
- Fix: Jetpack and Gutenberg interfering with `print_footer_scripts` hook

1.0.8: 2019-05-20
- New: WooCommerce Extra extension with Quick View button for products
- New: Add changelog for companion plugin in dashboard
- Improvement: Disable Read Progress Bar from pages
- Improvement: Improve readme.txt output for plugin updates

1.0.7: 2019-05-11
- Improvement: Move Mailchimp in footer

1.0.6: 2019-05-11
- Improvement: Use WP's global React and ReactDOM
- Improvement: Include gulpfile.js and package.json files in the final build

1.0.5: 2019-05-10
- New: EDD Integration

1.0.4: 2019-05-09
- Fix: Read progress bar check for els presence
- Fix: Small fixes for Instagram block
- Fix: Proper lazy load attributes for sync logic

1.0.3: 2019-05-02
- New: Checkbox for consent
- Improvement: Tested with WordPress 5.2
- Improvement: Support Blocksy child themes variations

1.0.2: 2019-05-01
- New: Google Analytics script
- New: Instagram extension with block and widget
- New: Allow SVG uploads
- New: Read Progress extension
- New: Mailchimp subscribe extension
- New: Cookies consent extension
- New: Elementor Columns Fix switch

1.0.1: 2019-04-11
- Improvement: Instagram widget text & defaults changes
- Fix: Remove `gz` files from build

1.0.0: 2019-04-10
- New: Initial Release
