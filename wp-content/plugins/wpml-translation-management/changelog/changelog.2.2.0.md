# Features
* [wpmltm-1391] The date in "Last time translations were picked up" now displays the actual time stamp of the last pickup
* [wpmltm-1179] Translation Management now logs messages exchanged with the translation service
* [wpmltm-1094] Add ability to set the 'field type' to wpml-config.xml filter

# Fixes
* [wpmltm-1442] Improved feedback message when sending jobs to a translation service fails
* [wpmltm-1408] Improved the admin notice when the XLIFF is missing the `target` element, or the element is empty
* [wpmltm-1401] Fixed caching issue on WP-Engine when new terms are created.
* [wpmltm-1400] Fixed issue when translation job wrongly updated when sending batch failed.
* [wpmltm-1395] Fixed creation of slug when "Copy from original language if translation language uses encoded URLs" is selected.
* [wpmltm-1390] Don't display the export XLIFF section on the Translations Queue page when the user doesn't have any translation languages
* [wpmltm-1351] Fixed `\WPML_Admin_Post_Actions::get_trid_from_referer`function to get trid form refferer only if needed
* [wpmltm-1343] Keep translation status when it's updated.
* [wpmltm-1339] The "Check all" checkbox in WPML -> Translations (Translations Queue) page now selects all jobs
* [wpmltm-1256] Fixed broken translation jobs display when "no results" were rendered previously.
* [wpmltm-1212] Fixed wrong post edit link for translator when lang_from and lang_to are equals
* [wpmltm-1160] Fix links to translation editor.
* [wpmltm-1134] The upper "Apply" button in WPML -> Translations (Translations Queue) now works as expected