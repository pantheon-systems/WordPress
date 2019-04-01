# Features
* [wpmlcore-3738] Fixed issue where in some installations, the Language Switcher was missing from the secondary language menu
* [wpmlcore-3505] Add troubleshooting option `Recreate ST DB Cache tables` to re-run ST upgrade
* [wpmlcore-3494] Removed admin user language feature in sites running WordPress 4.7 or higher and included migration logic while upgrading WP. This is now integrated to WordPress itself.
* [wpmlcore-3399] Added a warning about missing menu items when only one language is configured in the site
* [wpmlcore-3372] Added more API hooks including `wpml_add_string_translation`
* [wpmlcore-3325] Changed the message style in Sync Field Functionality

# Fixes
* [wpmlcore-3682] Fix handling of slashes when copying custom fields
* [wpmlcore-3544] Automatically download WordPress mo files for active languages
* [wpmlcore-3497] Adjusted URL in wp_upload_dir return for when WPML is set up as domain
* [wpmlcore-3479] Fixed issue where language information wasn't saved when posts were created via AJAX call
* [wpmlcore-3451] Prevent double ampersand encoding in language switcher URL
* [wpmlcore-3441] Fixed issue in the WPML notice when adding posts from frontend
* [wpmlcore-3438] Fixed issue with setting static blog page when page is permanently deleted
* [wpmlcore-3434] Fixed WPML_Backend_Request::get_ajax_request_lang
* [wpmlcore-3421] Fixed page template synchronization if template is set to default
* [wpmlcore-3404] Fixed issue when duplicating posts that have comments to be duplicated
* [wpmlcore-3374] Removed the restriction of only English as default language when synchronizing WP menu
* [wpmlcore-3363] Fixed an issue with the browser redirection when using languages with regional variations (e.g. fr-CA).
* [wpmlcore-3361] Fixed a login redirection issue in sites with languages configured as domain
* [wpmlcore-3333] Fixed an issue that was happening when you try to scan strings before completing the wizard. Now it is not allowed
* [wpmlcore-3311] Fixed multisite install compatibility issue with WP 4.6+ using `sunrise`
* [wpmlcore-3266] On multisite, the main site settings are not altered anymore during sub-site setup
* [wpmlcore-3204] Fixed a compatibility issue with Yoast redirections
* [wpmlcore-3199] Removed Snoopy class and use WP_Http class instead.
* [wpmlcore-2968] Fixed a redirection issue Nginx servers with language configured as domain
* [wpmlcore-2884] In some cases (taxonomy, author, date and post type archives), the language switcher custom link for empty translation was not displayed
* [wpmlcore-2849] Fixed issue with cloning non object values for wp_query object.
* [wpmlcore-2692] Added caching for Twig templates.
* [wpmlcore-2565] Fixed a compatibility issue with `get_option('siteurl')` in sites with languages set as domain
* [wpmlcore-2535] Fixed a compatibility issue with `bloginfo('stylesheet_url')` when languages is set as domain
* [wpmlcore-2375] Fixed an URL inconsistency when using `get_page_link`
* [wpmlcore-2289] The language switcher in Twenty Sixteen's footer was cut off
* [wpmlcore-1869] The language switcher in Twenty Fifteen's footer was partially hidden by the sidebar