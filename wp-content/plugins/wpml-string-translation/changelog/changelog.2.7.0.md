# Features
* [wpmlst-1568] Added filters for when shortcode attributes (from page builders) are encoded/decoded
* [wpmlst-1539] Performance improvement in automatic .mo file scanning
* [wpmlst-1526] Adjusted the option for assuming that the original language of strings is in English in the case that the default site language is different than English

# Fixes
* [wpmlst-489] Converting text widget to multilingual text widget adds another instance of the widget
* [wpmlst-1583] Fixed an issue with Elementor Pro slide's links which were not translatable
* [wpmlst-1581] Fixed an issue when trying to scan string with no item selected
* [wpmlst-1579] Remove post string packages when the related post is permanently deleted
* [wpmlst-1575] Fixed issue with strings caching in secondary languages
* [wpmlst-1573] Fixed performance bottleneck by allowing empty strings to be registered
* [wpmlst-1565] Fixed issue with String translation loading translations from db instead of .mo files when the respective options are checked in Theme and plugins localization
* [wpmlst-1549] Resolved exception resulting in many entries in wp_icl_string_urls table when Languages as Directories are configured
* [wpmlst-1518] Fixed issue with automatic .mo scanning for Child themes.
* [wpmlst-1515] Fixed issue with notice for applying fastest settings in Theme Plugin Localization screen
* [wpmlst-1480] Fixed issue with the admin being partially shown in the wrong language
* [wpmlst-1308] Fixed issues with Taxonomy label translation