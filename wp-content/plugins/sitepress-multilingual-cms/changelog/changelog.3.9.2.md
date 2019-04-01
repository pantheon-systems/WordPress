# Features
* [wpmlcore-4970] Allow the translation mode for custom fields to be unlocked when they are set by wpml-config.xml
* [wpmlcore-4953] Fixed "Copy content from" button not copying custom fields
* [wpmlcore-4563] Add two actions to sync custom fields

# Fixes
* [wpmlcore-5119] Fixed a typo in the query parser causing infinite redirection with specific setup.
* [wpmlcore-5109] Fixed an error when the menu has a language switcher with no items
* [wpmlcore-5098] Resolved exception in pagination resulting in compatibility issue with WP-PageNavi
* [wpmlcore-5095] Fixed a fatal error with WPML_LS_Templates::are_template_paths_valid
* [wpmlcore-5092] Adjusted the color picker style in the language switcher's admin page.
* [wpmlcore-5090] Improved the post selection when the slug is shared between multiple posts
* [wpmlcore-5063] Resolved errors produced when having an empty wpml-config.xml file
* [wpmlcore-5025] Fixed "Fatal error: Uncaught TypeError: Argument 1 passed to WPML_End_User_Page_Identify::__construct()"
* [wpmlcore-4973] Improved the `sanitize_title` filter for some specific locales.
* [wpmlcore-4969] Fixed wrong redirection when a 404 page is accessed with an arbitrary argument
* [wpmlcore-4956] Fixed an issue with pluggable functions when language in domains was set
* [wpmlcore-4700] Resolved exception with wrong redirection occurring on attachment pages when they have the same slug
* [wpmlcore-4690] Fixed the URL in WP SEO snippet preview when using "languages in directory"