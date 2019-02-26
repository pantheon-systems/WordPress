# Features
* [wpmlcore-5987] Improved inclusion of shortcodes list in XLIFF files
* [wpmlcore-5923] Added ability to translate Gutenberg's media blocks
* [wpmlcore-5890] Provide a meaningful message when Advanced Translation Editor activation fails.
* [wpmlcore-5873] Added Compatibility with Tiny Compress Images plugin
* [wpmlcore-5868] Fixed issue when validating Installer subscription on secondary languages
* [wpmlcore-5803] Extended Elementor integration to accept modules with multiple repeater fields
* [wpmlcore-5794] Added feature allowing users to translate images in non-translatable blocks for Elementor
* [wpmlcore-5791] Added feature allowing users to translate images in non-translatable blocks for Beaver Builder
* [wpmlcore-5790] Filter added to prevent publicizing of a duplicate
* [wpmlcore-5766] Implemented feature for selecting which sub-keys of custom fields should be translated
* [wpmlcore-5693] Implemented a feature for handling page builder pages which don't contain translatable elements in order to avoid sending the entire post content, but copying it instead
* [wpmlcore-5690] Fixed missing language column in Fusion Builder's library.
* [wpmlcore-5666] Added WP endpoints support
* [wpmlcore-5596] Fixed wrong default locale for Croatian
* [wpmlcore-5231] Compatibility: Unblock MO files for "gutenberg" text domain.
* [wpmlcore-4875] Fixed exception occurring when translating a post that was previously handled with a page-builder
* [wpmlcore-4682] Add feature to decode JSON encoded custom fields
* [wpmlcore-4408] Implemented a new feature in the WPML requirements notice allowing users to activate already installed plugins

# Fixes
* [wpmlcore-6078] Resolved Javascript errors with Yoast SEO when used with WordPress' classic editor
* [wpmlcore-6071] Added compatibility for the Quote and Pullquote blocks of Gutenberg
* [wpmlcore-6070] Allowed translation of emojis in the classic translation editor
* [wpmlcore-6051] Fixed an issue with synced sticky posts when in translated mode but not actually translated
* [wpmlcore-6050] Fixed issue where sticky post status isn't copied over to translations
* [wpmlcore-6045] Fixed issue when trying to change WPML settings of a non-translatable post type
* [wpmlcore-6043] Fixed an issue preventing from setting a post type to "translate" when it's not translatable in the xml file and unlocked in the settings
* [wpmlcore-6030] Moved the Minor Edit checbkox inside WPML's metabox in order to ensure compatibility with Gutenberg
* [wpmlcore-6021] Fixed database error when translating a page having Gutenberg active and Translation Management inactive
* [wpmlcore-6016] Fixed a Gutenberg compatibility issue which was causing a message related to duplicated posts to not be displayed
* [wpmlcore-6013] Fixed a 404 page when a secondary language page is accessed with the source slug and the display as translated mode is active.
* [wpmlcore-6008] Fixed the taxonomy terms list in Gutenberg editor used in a secondary language.
* [wpmlcore-6005] Fixed issue with the Browser Redirection feature which was doing unnecessary redirection on english pages to the english language
* [wpmlcore-6001] .mo file for Ukrainian language renamed since we adjusted locale to uk
* [wpmlcore-5988] Fixed the support of the Gutenberg cover block which name was changed
* [wpmlcore-5967] Fixed an issue with translated terms not being assigned in translations of posts created with Gutenberg
* [wpmlcore-5962] Improved usability and feel of WPML's tooltips
* [wpmlcore-5958] Fix canonical links on archive page for secondary languages
* [wpmlcore-5929] Fixed an issue with Elementor links which were not converted
* [wpmlcore-5872] Fixed an issue on page builder save which was forcing re-saving all translations
* [wpmlcore-5860] Added feature allowing users to translate images in non-translatable blocks for Enfold's page builder
* [wpmlcore-5859] Added feature allowing users to translate images in non-translatable blocks for WPBackery's page builder
* [wpmlcore-5855] Added feature allowing users to translate images in non-translatable blocks for Avada's Fusion Builder
* [wpmlcore-5839] Fixed an issue with the language meta-box not being refreshed when creating a page with Gutenberg
* [wpmlcore-5819] Added feature allowing users to translate images in non-translatable blocks for Divi's page builder
* [wpmlcore-5773] Removed Translation Media settings section from WPML Settings screen when attachment is set as not translated
* [wpmlcore-5772] Resolved problem with trailing slash management
* [wpmlcore-5753] Fixed with different logic for single and multisite
* [wpmlcore-5702] Fixed a performance issue while deleting attachments in bulk
* [wpmlcore-5629] Fixed issue when displaying media items and in WPML it is set as not translatable
* [wpmlcore-5575] Disable media features when "attachment" is not translatable.
* [wpmlcore-5417] Yoast SEO XML Sitemaps: fixed translated home page duplication
* [wpmlcore-5337] Fixed possible query errors when a theme or a plugin uses the same table aliases used by WPML
* [wpmlcore-5325] Fixed issue with Copying original content when Gutenberg is used
* [wpmlcore-5263] Errata page created