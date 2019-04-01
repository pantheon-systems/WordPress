# Features
* [wpmlcore-4763] Fixed issue when setting WPML capabilities to administrator users
* [wpmlcore-4722] Performance improvements when accessing database
* [wpmlcore-4712] Added the `get_translatable_documents_all` filter to allow modifying the translatable post types after reading the "read-only" configuration
* [wpmlcore-4637] Added a compatibility class for Google XML Sitemaps plugin
* [wpmlcore-4627] Added the Translation Feedback module
* [wpmlcore-4619] Removed dependency with WPML Page Builder, as is now merged into String Translation
* [wpmlcore-4618] Added Yoast's notice which asks users to upgrade their PHP Version (if it's too old)
* [wpmlcore-4616] Added a filter to blacklist URLs handled by the "Absolute Links" logic (needed when dealing with WooCommerce endpoints).
* [wpmlcore-4615] Fixed the Twig version to `~1.32.0` to avoid compatibility issues with old versions of PHP
* [wpmlcore-4597] Added support for Installer's channel, allowing to download and install beta versions of WPML and its add-ons
* [wpmlcore-4594] Added support for assigning a language to any widget, in order to limit the display of widgets only to specific languages
* [wpmlcore-4537] Added support to custom XML
* [wpmlcore-4521] When configuring a static HTML page as a root page, WPML will validate this setting before saving it
* [wpmlcore-4354] Display invitation for end-users registration

# Fixes
* [wpmlcore-4765] Fix TM editor notice so it doesn't show again if the user logs out and logs back in
* [wpmlcore-4762] Use the clean content from Enfold builder when registering page builder strings
* [wpmlcore-4761] Fix duplicating a page that is using the Enfold page builder
* [wpmlcore-4737] Fixed a broken filter in get_page_by_path() with WP >= 4.7
* [wpmlcore-4726] Decrease number of queries when WPML is not configured
* [wpmlcore-4719] Fixed synchronization of menu order when site has more than 2 languages
* [wpmlcore-4701] Fixed gettext strings in "WPML > Taxonomy Translation" page
* [wpmlcore-4699] Prevent loading `plugins/sitepress-multilingual-cms/res/js/sitepress.js` from the front-end when not needed
* [wpmlcore-4698] Prevent loading `sitepress-multilingual-cms/templates/language-switchers/legacy-dropdown/script.js` when not needed
* [wpmlcore-4670] Resolved notice when trying to delete a taxonomy term which is not set to be translated
* [wpmlcore-4658] Fix translating link targets in custom fields when there is more than one value for the meta key
* [wpmlcore-4657] Fixed link in "Use the Category translation table for easier translation"
* [wpmlcore-4656] Increased the language cookie script priority causing wrong string translation in AJAX request
* [wpmlcore-4655] Fixed domain in the plugins_url function to match the current domain URL
* [wpmlcore-4626] Fixed an issue with a trailing slash on the preview URL
* [wpmlcore-4614] Fixed PHP warning when calling the `wp_list_pages` function
* [wpmlcore-4613] Fix single quote issue with Enfold for shortcode attributes
* [wpmlcore-4595] Removed notice when a translation for a language cannot be downloaded
* [wpmlcore-4584] Most used tags in autocomplete input are displayed in correct language now
* [wpmlcore-4581] Passing data between separate domains for different language didn't work properly
* [wpmlcore-4542] Fixed a fatal error with PHP 7.1 during WPML installation
* [wpmlcore-4531] Fixed a compatibility issue when inserting a post from the frontend
* [wpmlcore-4528] Fixed missing body classes on root page with custom page template
* [wpmlcore-4527] Fixed an issue to open the translation preview with languages in domains and SSO enabled
* [wpmlcore-4482] Removed the `vendor/wimg` directory from the deployed package as it's only needed for development purpose
* [wpmlcore-4449] Fixed issue with get_pagenum_link when trying to convert URL and language is set as parameter
* [wpmlcore-4389] Fixed the secondary home URL trailing slash in WP SEO sitemap
* [wpmlcore-4370] Fixed the category sitemap when using a root page
* [wpmlcore-4253] Fixed flag for Yiddish
* [wpmlcore-4228] Fixed unexpected redirection with first level pages
* [wpmlcore-4182] Fix translating of DIVI taxonomies
* [wpmlcore-3917] Fixed issue when building a WP_Query with taxonomy and suppress filters parameters
* [wpmlcore-3911] Fixed the private status synchronization when source is changing from "private" to "publish"
* [wpmlcore-2475] Fixed the browser redirection when the secondary language is in a different domain