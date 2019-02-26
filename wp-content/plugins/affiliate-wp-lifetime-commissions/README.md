Lifetime Commissions
====================

Allow your affiliates to receive a commission on all future purchases by the customer

= Version 1.2.4, May 19, 2017 =
* Fix: WooCommerce 3.0+ compatibility fixes

= Version 1.2.3, February 23, 2017 =

* Tweak: Use the manage_affiliate_options capability for permissions checking in Edit Profile
* Tweak: Deprecate force_was_referred() method
* Fix: Avoid a fatal error by checking for the existence of AffiliateWP on uninstall
* Fix: Avoid a notice after deleting a user linked to an affiliate

= Version 1.2.2, January 12, 2015 =

* Fix: PHP notice during affiliate registration

= Version 1.2.1, January 8, 2015 =

* Fix: Prevent an affiliate from receiving a lifetime referral if lifetime commissions is disabled for them.
* New: Referrals generated via Lifetime Commissions are now flagged as lifetime referrals in the database. v1.3 will make use of this functionality.
* New: A user can now be linked to an affiliate via Lifetime Commissions when registering as an affiliate. Supports the standard affiliate registration form, Affiliate Forms for Ninja Forms and Affiliate Forms for Gravity Forms

= Version 1.2, December 22, 2015 =
* New: Lifetime referral rates. This can be set globally, or on a per-affiliate basis
* New: affwp_lc_lifetime_referral_rate filter for changing the affiliate's lifetime referral rate programmatically
* Tweak: The checkbox for removing data on plugin deletion has been grouped with the rest of the settings for Lifetime Commissions in the integrations tab
* Tweak: Added header above lifetime commission settings on an individual affiliate edit screen

= Version 1.1, November 27, 2015 (requires AffiliateWP v1.7.10 or higher) =
* Fix: Commission rates were being incorrectly calculated in certain scenarios
* New: Gravity Forms integration
* New: Ninja Forms integration
* New: Paid Memberships Pro integration
* New: Restrict Content Pro integration

= Version 1.0.4, November 8, 2015 =
* Fix: Prevent lifetime affiliate from being overridden

= Version 1.0.3, November 14, 2014 =
* Fix: Fatal error caused by error checking in v1.0.2 when no linked affiliate ID is entered

= Version 1.0.2, November 14, 2014 =
* Fix: Admin couldn't link a user to their own affiliate ID from the user profile screen
* New: Error message is shown on profile screen if the affiliate ID is invalid

= Version 1.0.1, November 2, 2014 =
* Fix: If an affiliate was logged in they could earn a commission on their own purchase
* Fix: If an admin was also an affiliate, the option to link/de-link an affiliate wasn't shown on the edit profile screen

= Version 1.0, October 21, 2014 =
* Initial release
