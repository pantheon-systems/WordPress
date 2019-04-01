# Features
* [wpmlcore-4860] Fix parameters count not matching placeholders count in wpdb::prepare

# Fixes
* [wpmlcore-4918] Resolved issues introduced by change in behaviour of esc_sql() in WordPress 4.8.3
* [wpmlcore-4907] Fixed usage of `wp_doing_ajax` on versions of WordPress older than 4.7
* [wpmlcore-4866] Fixed admin strings not being added to the translatable strings, when defined through the custom XML configuration
* [wpmlcore-4858] Fixed notices thrown in WordPress 4.9 by the WPML installation wizard
* [wpmlcore-4855] Fixed issue that was stopping the Add Gallery button from working for NexGen Gallery
* [wpmlcore-4836] Add help for Encode URLs and Language Tags on the Edit Languages page
* [wpmlcore-4822] Removed non-required dependency of Underscore.js in order to resolve Compatibility issues.
* [wpmlcore-4813] Fixed issue with Saving settings in "Translate texts in admin screens" page when handling recursive objects
* [wpmlcore-4736] Fixed notice thrown after completing the first installation wizard and NONCE_SALT constant is not defined
* [wpmlcore-4716] Resolved exception with wrong template being applied to specific Custom Post Types
* [wpmlcore-4488] Fixed bugs related to uploading flags for custom languages
* [wpmlcore-4031] Nonce check when changing a post's language
* [wpmlcore-3028] Fixed issue with home_url filter returning wrong url when 'relative' mode is selected