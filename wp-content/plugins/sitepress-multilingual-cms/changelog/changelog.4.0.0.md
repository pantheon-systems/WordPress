# Features
* [wpmlcore-5313] Removed jQuery and JQuery Cookie dependencies in the browser redirection script.
* [wpmlcore-5288] Fixed an issue in the page builders integration which was translating non-translatable attributes whenever a translatable one shares the same attribute value
* [wpmlcore-5241] Preserve URL query arguments in browser redirection
* [wpmlcore-5112] Created a Translation Management role
* [wpmlcore-5065] Fixed an issue with the WPML metabox not being refreshed after a post is created with Gutenberg
* [wpmlcore-4978] Changed language order from horizontal to vertical for the WPML Configuration Wizard and the WPML/Languages Add/Remove languages listing
* [wpmlcore-4963] Implemented "copy-once" for Terms meta
* [wpmlcore-4382] Fixed 500 page error caused by a WPML filter being called while WP is flushing rewrite rules
* [wpmlcore-4358] Extend the filter hook `wpml_permalink` with a 3rd argument to have a full resolution of the URL.

# Fixes
* [wpmlcore-5386] Fixed WP Menus Sync menu item being displayed before WPML wizard is completed
* [wpmlcore-5335] Fix wpml Elementor page builder module to accept changes from tab module
* [wpmlcore-5328] Added custom encoding/decoding for Visual Composer's gallery and image carousel
* [wpmlcore-5304] Fixed a performance hit when saving the translation editor with a lot of page builder fields.
* [wpmlcore-5289] Removed the "Translation of" dropdown when creating a new post
* [wpmlcore-5286] Removed WPML Installer priority in favor of the instance with higher version
* [wpmlcore-5282] Fixed deprecated constructor in dqml2tree library
* [wpmlcore-5279] Fixed JS errors when adding a post translation in a Gutenberg page
* [wpmlcore-5265] Fixed errors when adding translations to Gutenberg pages
* [wpmlcore-5260] Fixed untranslated URL in Yoast preview admin section
* [wpmlcore-5251] Fixed a column length issue preventing from setting the language for page builder packages
* [wpmlcore-5250] Fixed issue with blank entries in Yoast SEO Sitemaps when a language is set as hidden
* [wpmlcore-5242] Fixed wrong category being assigned to a page builder translated post
* [wpmlcore-5236] Fixed issue when redirecting untranslated pages with similar slugs
* [wpmlcore-5234] Fixed compatibility issue with WordPress 3.9.x or before
* [wpmlcore-5230] Fixed wrong redirection when accessing URL without language parameter
* [wpmlcore-5227] Added encoding condition attribute in wpml-config.xml file in order to conditionally encode a field
* [wpmlcore-5224] Added languages label in screen options meta box
* [wpmlcore-5214] Fixed a wrong term edit link in the admin language switcher.
* [wpmlcore-5212] Fixed issue when using single sign-on having many languages
* [wpmlcore-5211] Fixed the list view of Media when display-as-translated is enabled for Attachments
* [wpmlcore-5209] Fixed issue with Ajax calls not returning results for post types set to be displayed as if translated
* [wpmlcore-5203] Add a filter to copy a post to another language and include option to mark as a duplicate
* [wpmlcore-5195] Fixed issue with wrong links pointing to domains when domain mapping is used in a network site
* [wpmlcore-5178] Fixed malformed REST URL when using language as parameter
* [wpmlcore-5154] Fixed the term links on the admin with a secondary language when the taxonomy is displayed as translated.
* [wpmlcore-5153] Introduced a new action hook "wpml_set_translation_mode_for_post_type"
* [wpmlcore-5149] Added informational notice appearing when changing the default language while the option to "use translation if available or fallback to default language" is enabled
* [wpmlcore-5118] Resolved an exception allowing the same domains to be used in sub-sites when using Multisites and inc/tools/sunrise.php
* [wpmlcore-5110] Fixed some admin language issues during WPML setup.
* [wpmlcore-5104] Fixed the sample permalink in secondary language on the post edit screen.
* [wpmlcore-5102] Guess the user language when the locale is not matching in `icl_languages`.
* [wpmlcore-5089] Fixed the language switcher when the query_vars has more than one post type
* [wpmlcore-5060] Removed usage of the deprecated PHP function each() for compatibility with PHP 7.2
* [wpmlcore-5028] Fixed some issues with the trailing slash when a URL is converted
* [wpmlcore-4418] Fixed issue with translated term slugs being wrongly auto-filled in Taxonomy translation page with encoded values
* [wpmlcore-4138] Fixed issue with Sticky posts not being listed in admin when filtering posts by "All Languages"