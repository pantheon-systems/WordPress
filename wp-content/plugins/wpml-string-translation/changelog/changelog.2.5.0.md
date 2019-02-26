# Features
* [wpmlst-929] Updated package registration API to give option to connect package to post.
* [wpmlst-1000] Implemented searching for strings upon clicking enter in the respective field of Strings translation admin page

# Fixes
* [wpmlst-996] Fix 'Create PO file' functionality so it includes the msgctxt when required
* [wpmlst-988] Fixed issue with DEFAULT in Text fields for compatibility with MySQL 5.7
* [wpmlst-955] Add support to translate shortcode strings used by page builders.
* [wpmlst-954] Fixed database error when running WPML reset
* [wpmlst-937] Fixed an issue when importing large `.po` files
* [wpmlst-925] Improved usage of server's resources when scanning themes or plugins for strings
* [wpmlst-920] Fixed an issue in double registering of Multilingual Widget content
* [wpmlst-907] Fixed an issue when trying to register a string with `0` as name
* [wpmlst-887] Fixed issue while dismissing admin notices from plugin that contains special chars in the title
* [wpmlst-831] Improved page loading and memory consumption in Theme's and Plugin's localization page
* [wpmlst-1547] Fixed line breaks being lost in Translation Editor
* [wpmlst-1028] Fix wrong table prefix when resetting WPML in multisite