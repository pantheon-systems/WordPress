=== AffiliateWP - Show Affiliate Coupons ===
Contributors: sumobi, mordauk
Tags: AffiliateWP, affiliate, Pippin Williamson, Andrew Munro, mordauk, pippinsplugins, sumobi, ecommerce, e-commerce, e commerce, selling, membership, referrals, marketing
Requires at least: 3.3
Tested up to: 4.9
Stable tag: 1.0.7
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Shows an affiliate their available coupon codes in the affiliate area

== Description ==

> This plugin requires [AffiliateWP](https://affiliatewp.com "AffiliateWP") in order to function.

This plugin allows you to show an affiliate any coupons that they have assigned
to them. The coupon codes will be shown in a new "coupons" tab of the affiliate
area. As well as showing the coupon code, the amount of the coupon is also shown
to the affiliate.

Note: In order for the coupons tabs to show, the affiliate must have 1 or more
coupons assigned to them.

You can also use the [affiliate_coupons] shortcode to show an affiliate their
coupons on any page.

Supported integrations:

1. WooCommerce
2. Easy Digital Downloads
3. iThemes Exchange
4. MemberPress

**What is AffiliateWP?**

[AffiliateWP](https://affiliatewp.com/ "AffiliateWP") provides a complete affiliate management system for your WordPress website that seamlessly integrates with all major WordPress e-commerce and membership platforms. It aims to provide everything you need in a simple, clean, easy to use system that you will love to use.

== Installation ==

1. Unpack the entire contents of this plugin zip file into your `wp-content/plugins/` folder locally
1. Upload to your site
1. Navigate to `wp-admin/plugins.php` on your site (your WP Admin plugin page)
1. Activate this plugin

OR you can just install it with WordPress by going to Plugins >> Add New >> and type this plugin's name

== Screenshots ==

1. The new "Coupons" tab on the affiliate dashboard

== Upgrade Notice ==

Changed the template file path priority to avoid a conflict with the Direct Link Tracking add-on

== Changelog ==

= 1.0.7 =
* Fix: PHP notice that could appear in some instances

= 1.0.6 =
* New: The "Coupons" tab now shows within the Affiliate Area Tabs add-on. Affiliate Area Tabs v1.1.6 and AffiliateWP v2.1.7 required
* Tweak: The table on the Coupons tab is now responsive

= 1.0.5 =
* Tweak: Changed the template file path priority to avoid a conflict with the Direct Link Tracking add-on

= 1.0.4 =
* New: Added support for MemberPress

= 1.0.3 =
* Fix: An issue with the tab's content not loading correctly due to recent changes made in AffiliateWP v1.8.1

= v1.0.2 =
* New: New [affiliate_coupons] shortcode to show the affiliate's coupons on any page

= v1.0.1 =
* Tweak: Adjusted template file path priority to 81

= 1.0 =
* Initial release
