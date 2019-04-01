=== AffiliateWP MLM ===
Contributors: ramiabraham, sumobi
Tags: AffiliateWP, affiliate, ecommerce, e-commerce, e commerce, selling, membership, mlm, multi-level, multi level marketing, referrals, marketing, affiliatewp, affiliates, woo, woocommerce, paid membership pro, paid memberships pro, memberpress, gravity forms
Requires at least: 3.3
Tested up to: 4.7.3
Stable tag: 1.1.2
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Turn your Affiliate Network into a full blown Multi-Level Marketing system, where your Affiliates can earn commissions on the referrals made by their Sub Affiliates on multiple levels. 

== Description ==


Turn your Affiliate Network into a full blown Multi-Level Marketing system, where your Affiliates can earn commissions on the referrals made by their Sub Affiliates on multiple levels.

**Supported Integrations**

1. WooCommerce
2. Paid Memberships Pro
3. MemberPress
4. Gravity Forms
5. Easy Digital Downloads
6. GeoDirectory
7. MemberMouse
8. Formidable Forms Pro
9. iThemes Exchange
10. Jigoshop
11. Marketpress
12. Ninja Forms
13. Restrict Content Pro
14. s2Member
15. Shopp
16. WP EasyCart
17. WP eCommerce

**Compatible AffiliateWP Add-ons**

1. Recurring Referrals
2. Allow Own Referrals
3. Store Credit
4. PayPal Payouts
5. Order Details
6. BuddyPress (Integration)
7. Affiliate Area Shortcodes (Integration)
8. Performance Bonuses (Integration)
9. Ranks (Integration)
10. Invites (Integration)
11. Affiliate Forms for Gravity Forms
12. Affiliate Forms for Ninja Forms
13. Lifetime Commissions (Integration)

> This plugin requires [AffiliateWP](http://affiliatewp.com/ "AffiliateWP") in order to function.

**What is AffiliateWP?**

[AffiliateWP](http://affiliatewp.com/ "AffiliateWP") provides a complete affiliate management system for your WordPress website that seamlessly integrates with all major WordPress e-commerce and membership platforms. It aims to provide everything you need in a simple, clean, easy to use system that you will love to use.

== Installation ==

1. Unpack the entire contents of this plugin zip file into your `wp-content/plugins/` folder locally
1. Upload to your site
1. Navigate to `wp-admin/plugins.php` on your site (your WP Admin plugin page)
1. Activate this plugin

== Upgrade Notice ==

== Changelog ==

= 1.1.2 =

* NEW - Downline Count shortcode.
* NEW - GeoDirectory integration.
* NEW - AffiliateWP Invites integration - Set the Parent Affiliate during registration using Invite Code.
* UPDATE - AffiliateWP Lifetime Commissions integration - Option to set the Lifetime Affiliate as the Direct Affiliate.
* UPDATE - AffiliateWP Lifetime Commissions integration - Option to set the Direct Affiliate as the Lifetime Affiliate.
* UPDATE - AffiliateWP Lifetime Commissions integration - Option to set the Parent Affiliate as the Lifetime Affiliate.
* UPDATE - AffiliateWP Lifetime Commissions integration - Button to synchronize ALL Lifetime Affiliates.
* UPDATE - Enable addition of new columns to indirect referrals list in affiliate area via filter.
* FIX - Duplicate tab in Affiliate Area for AffiliateWP 2.1.7+.
* FIX - Cannot translate referral status in Referrals tab of Affiliate Area.
* FIX - Showing Parent Affiliate's name on Sub Affiliates Tree when Show Parent is disabled.
* FIX - AffiliateWP Lifetime Commissions integration - Linking guest customer emails to Upline affiliates on referral completion.

= 1.1.1 =

* NEW - AffiliateWP Performance Bonuses integration - Cycle Complete Bonus Type.
* NEW - AffiliateWP Ranks integration - Cycle Complete Rank Type.
* NEW - AffiliateWP Lifetime Commissions integration - Sync Lifetime Affiliate.
* NEW - Button to Clear ALL Affiliate Connections (Disconnect all affiliates).
* NEW - Add Referring Affiliate via the Referrer Field on the Registration Form.
* NEW - Downline Cycles feature (Forced Matrix).
* UPDATE - Display the Parent Affiliate in the Tree.
* FIX - Inconsistent avatar sizes in Tree View.
* FIX - Extra lines displayed in Tree View for some themes.
* FIX - Current affiliate incorrectly used to display tree/list.
* FIX - Errors when processing Indirect Referrals.


= 1.1 =

* NEW - Award Commissions for Referring Sub Affiliates.
* NEW - Tree View in Affiliate Area & Edit Affiliate screen.
* NEW - Sub Affiliates shortcode (Tree or List).
* NEW - Parent Affiliate shortcode.
* NEW - Direct Affiliate shortcode.
* NEW - Indirect Referrals shortcode.
* NEW - Tree View for Sub Affiliates.
* NEW - AffiliateWP Ranks Integration - Per-Level Rank Rates.
* NEW - Formidable Forms Pro integration.
* NEW - iThemes Exchange Integration.
* NEW - Jigoshop Integration.
* NEW - Marketpress integration.
* NEW - Ninja Forms integration.
* NEW - Restrict Content Pro Integration.
* NEW - s2Member Integration.
* NEW - Shopp Integration.
* NEW - WP EasyCart Integration.
* NEW - WP eCommerce Integration.
* UPDATE - Set the Parent Affiliate using the back-end "Add New Affiliate" screen.
* UPDATE - Show ALL Levels of Sub Affiliates in an affiliate's Personal Matrix.
* UPDATE - Improved performance during Indirect Referral creation.
* UPDATE - Added order notes for ALL integrations that support the feature.
* UPDATE - Moved Indirect Referrals table to Referrals tab of the Affiliate Area.
* FIX - Duplicate Indirect Referral description for mutiple product purchases.
* FIX - Replaced recursive functions to resolve server timeout issue.


= 1.0.6.1 =
* NEW - Display affiliate status in Sub Affiliates Tab of the Affiliate Area
* NEW - AffiliateWP Variable Rates integration - Per-Level Variable Rates.
* UPDATE - Percentage calculations for AffiliateWP 1.8+.
* UPDATE - Pass additional variables into affwp_mlm_insert_pending_referral filter.
* FIX - Indirect Referrals generated by Gravity Forms are stuck in Pending status.
* FIX - Bug in Affiliate Area Tab due to AffiliateWP 1.8.1 update.
* FIX - License expired message for valid licenses.


= 1.0.6 =
* NEW - AffiliateWP Ranks integration - Sub Affiliate-based Ranks.
* NEW - affwp_mlm_insert_pending_referral filter.
* UPDATE - Lifetime Commissions compatibility.
* FIX - Endless loop during affiliate connection.
* FIX - Creating referrals for downline instead of upline.
* FIX - Empty listing for deleted affiliates in Sub Affiliates table.


= 1.0.5 =
* NEW - Easy Digital Downloads integration.
* NEW - MemberMouse integration.
* NEW - Option to set the depth of the total matrix.
* UPDATE - Transferred code for Sub Affiliates BP profile tab to BuddyPress add-on.
* UPDATE - Use 1st active affiliate for fallback in affwp_mlm_find_open_affiliate().
* UPDATE - Restructured admin files.


= 1.0.4 =
* NEW - AffiliateWP Performance Bonuses integration - Sub Affiliate Bonuses.
* NEW - Affiliate Forms for Gravity Forms compatibility.
* NEW - Affiliate Forms for Ninja Forms compatibility.
* UPDATE - Connect affiliates using the "Auto Register New Users" feature.
* UPDATE - Connect affiliates using the back-end "Add New Affiliate" screen.
* UPDATE - Compress commissions to include only active affiliates.
* FIX - Per-Affiliate Rate applying to Indirect Referrals.
* FIX – Empty Sub Affiliates Tab & Shortcode when Order Details is active.


= 1.0.3 =
* NEW - AffiliateWP Performance Bonuses compatibility.
* NEW - AffiliateWP BuddyPress compatibility.
* NEW - Affiliate Area Shortcodes compatibility.
* NEW - Tiered Affiliate Rates compatibility.
* NEW - Hooks for Indirect Referrals.
* UPDATE - Improve compatibility with Recurring Referrals.
* UPDATE - Improve compatibility with AffiliateWP 1.6.3.
* UPDATE - Increase plugin security.
* FIX - MemberPress subscription referrals will not auto-complete (automatically mark as unpaid).
* FIX - Prevent overwriting Per Level Rate settings with Tiered Affiliate Rates activated.
* FIX - Extra row in Per Level Rate settings.


= 1.0.2 =
* NEW - MemberPress integration.
* NEW - Gravity Forms integration.
* UPDATE - Show the Direct Affiliate's name in Indirect Referral descriptions.
* UPDATE - Make the Direct Affiliate dropdown searchable on the Edit Affiliate Screen.
* FIX - Remove check for affiliate ID 1 when getting upline affiliate(s).


= 1.0.1 =
* FIX - Saving per-affiliate settings for databases with custom prefixes.
* FIX - License showing expired, when active.
* FIX - Woocommerce referrals will not auto-complete (automatically mark as unpaid).
* FIX - Auto-update unavailable.


= 1.0 =
* Initial release