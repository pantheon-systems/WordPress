# Features
* [wpmlcore-4321] Updated the encryption method for passing data between different domains to use the openssl AES-256-CTR algorythm (compliant with mcrypt_encrypt being removed from PHP in version 7.2)
* [wpmlcore-4313] Add support for post format archives in the language switcher
* [wpmlcore-4301] Changed translator notification email format to HTML
* [wpmlcore-4177] Added 'copy once' option to custom field translation that allows to copy from original and then translate independently
* [wpmlcore-4104] Added Catalan support
* [wpmlcore-3947] Moved the taxonomy synchronization logic from WCML to WPML
* [wpmlcore-3805] Added the parent theme's name to the debug information
* [wpmlcore-3315] Allow to select the flags for languages from pre-defined list
* [wpmlcore-1664] Added an "active/inactive" filter in theme and plugins localization screen to simplify selection
* [wpmlcore-1654] Added auto-generation of Slug when translating Terms using Taxonomy Translation

# Fixes
* [wpmlcore-4416] Fix the language switcher preview not updated in some circumstances
* [wpmlcore-4385] Fixed issue with auto sign-in and language per domain on old browser versions
* [wpmlcore-4384] Fixed HTML in the "Auto sign-in and sign-out users from all domains" tooltip
* [wpmlcore-4327] Fixed a problem saving a layout when Avia Layout Builder Debug is enabled
* [wpmlcore-4322] Fixed issue in admin user language in Greek
* [wpmlcore-4320] Fix retrieving of post id by url in secondary language
* [wpmlcore-4272] Fixed issue with WPML language cookies in cached environments. When user is not logged in, cookies will be stored with JS and optionally
* [wpmlcore-4268] Fixed issues when WPML's plugin folder is a symbolic link
* [wpmlcore-4254] Refactored `sunrise.php` to match domains starting with "www"
* [wpmlcore-4251] Fixed possible JS error with hierarchy-sync-message.js
* [wpmlcore-4246] Fixed fatal error `Call to undefined function wp_get_upload_dir()`
* [wpmlcore-4207] Fixed transient issue with custom language switcher templates on multisite
* [wpmlcore-4203] Fixed canonical redirection issues with languages in directories and root page
* [wpmlcore-4193] Excluded WooCommerce and Gravity Forms from the enable translation editor notice
* [wpmlcore-4180] Ignore the "Use directory for default language" setting when the root page doesn't exist or when HTML isn't set
* [wpmlcore-4121] Fixed removal of category's translations in bulk action
* [wpmlcore-3877] Fixed multisite link when WPML has languages set up as domain
* [wpmlcore-3420] Fixed \SitePress::_sync_custom_field to handle serialized data.