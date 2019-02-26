Direct Link Tracking
====================

Allow affiliates to link directly to your site, from their site, without the need for an affiliate link

= Version 1.1.2, November 1st, 2017
* New: The "Direct Links" tab now shows within the Affiliate Area Tabs add-on. Affiliate Area Tabs v1.1.6 and AffiliateWP v2.1.7 required.
* Fix: PHP Notice when adding a new direct link from the admin

= Version 1.1.1, May 25th, 2017
* Fix: A double visit being recorded if an affiliate adds their affiliate ID/username to a direct link.

= Version 1.1, May 15th, 2017

* New: The direct link settings have been moved to their own individual "direct links" tab within the Affiliate Area
* New: [affiliate_direct_links] shortcode for showing an affiliate their direct link settings
* New: Domain paths are now supported
* New: HTTP to HTTPS is now supported
* New: Direct links can now be added from the main Direct Links admin screen
* New: Direct links can now be edited via their own dedicated edit screen
* New: Email notification to the site admin when a direct link has been submitted for approval
* New: Email notification to the affiliate when their direct link has been approved
* New: Email notification to the affiliate when their direct link has been rejected
* New: Specific direct links can be blacklisted
* New: A dashboard-tab-direct-links.php template file has been introduced to allow for customization
* New: A “Date Added” column has been added to the Direct Links admin screen
* New: The "Visits" admin column on the "Direct Links" screen is now sortable
* New: The "Domain" column on the "Direct Links" admin screen is now sortable
* New: Domains are now sorted by most recently added by default
* New: Direct Link Tracking can now be used with the "Fallback Referral Tracking Method" from Affiliates -> Settings -> Misc
* New: The site_url() and home_url() domains are automatically blocked and cannot be accidentally added as direct links
* New: Domains on the Affiliate Area can be removed via a new "remove” link
* New: Domains on the Edit Affiliate admin screen can be removed via a new "remove” link
* Tweak: Domain fields are now wider to match other URL fields in the admin
* Tweak: Notices shown to affiliates in the Affiliate Area have been improved
* Tweak: Overall domain validation has been improved
* Tweak: Error notices have been improved
* Fix: A scenario where cookies could be stored for an affiliate when direct link tracking is disabled for them
* Fix: A scenario where direct links could get replaced in the admin

= Version 1.0.3, November 3rd, 2016 =
* Fix: The "Direct Link Tracking Domain" input field in the Affiliate Area was not visible by default in some instances
* Fix: A bug was introduced in 1.0.2 that could allow two affiliates to submit the same domain

= Version 1.0.2, November 2nd, 2016 =
* Fix: Domains were not being added correctly in some instances
* Fix: PHP Notice

= Version 1.0.1, August 24th, 2016 =
* Fix: In some instances the admin could not add a domain to an affiliate from within the admin
* Tweak: Modified the first input field's text when adding a domain as an admin

= Version 1.0, July 14th, 2016 =
* Initial release
