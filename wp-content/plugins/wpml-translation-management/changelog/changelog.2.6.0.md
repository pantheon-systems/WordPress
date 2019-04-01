# Features
* [wpmltm-2481] Extracted setting tabs from the "Translation management" page to a new "Settings" page.
* [wpmltm-2457] Added a notice when translating Gutenberg's blocks in the translation editor.
* [wpmltm-2299] Added validation in Translation Basket for base64 encoded fields
* [wpmltm-2286] Swapped the strings in brackets in the translator dropdown.
* [wpmltm-2272] Added a UUID for documents sent to translation services
* [wpmltm-2261] Add a confirmation dialog when a batch is sent to a translation service
* [wpmltm-2251] Pages are now pre-selected in the Translation Management dashboard filters
* [wpmltm-2225] Make the option "Don't include already translated terms in the translation editor" enabled by default in new sites.
* [wpmltm-2201] Improvement of the word count estimation.
* [wpmltm-2173] Implemented a new Translation Manager wizard
* [wpmltm-2162] Moved the translations services "items per page" option to the standard WP screen options.
* [wpmltm-2149] Implemented refreshing of data for the active Translation Service
* [wpmltm-2121] Restored the parent filter in the Translation Management Dashboard
* [wpmltm-2056] Changed the implementation of ICanLocalize so it's handled like the other Translation Services
* [wpmltm-2019] Remove use of icl_reminders.js to prevent any cross-site scripting (XSS) vulnerabilities
* [wpmltm-1844] Implemented WPML Translation Priority taxonomy which can be used to define a priority of posts or strings sent to translation
* [wpmltm-1763] The active translation service information is refreshed when visiting the "Translation services" tab.

# Fixes
* [wpmltm-2613] Fixed translated WYSIWIG fields showing as untranslated on Translation Editor
* [wpmltm-2479] Fixed strings package jobs that could not be canceled from the translation service.
* [wpmltm-2458] Prevent issues during TP ratings synchronization.
* [wpmltm-2350] Fixed compatibility issue with "SpeakOut! Email Petitions" plugin when visiting settings page
* [wpmltm-2295] Stripped carriage return from exported XLIFF files
* [wpmltm-2282] Fixed issue with HTML entity not being converted to symbol in the Translation Jobs page
* [wpmltm-2273] Replaced create_function which is deprecated in PHP 7.2
* [wpmltm-2253] Fixed issue when names/similar fields are matched during the uploading of an XLIFF translation
* [wpmltm-2232] Fixed an issue with labels in filters of Translation Management changing language according to the admin language switcher
* [wpmltm-2207] Resolved exception with external jobs that have the status "needs_update"
* [wpmltm-2197] Removed duplicated MIME-Version in email headers