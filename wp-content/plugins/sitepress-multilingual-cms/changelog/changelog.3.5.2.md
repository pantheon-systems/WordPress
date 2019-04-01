# Features
* [wpmlcore-3316] Added `wpml_user_switch_language`.
* [wpmlcore-3227] Reduced the number of queries in the posts listing pages.
* [wpmlcore-3160] Added `wpml_is_translated_taxonomy`.
* [wpmlcore-1101] Added a spinner and disabled buttons in the WPML installation wizard.

# Fixes
* [wpmlcore-3334] Fixed some issues when translating **from a non-default language** and MO files are used for gettext strings (introduced in WP 4.6).
* [wpmlcore-3320] Users can now login across domains when WPML is configured to use languages per domain (it uses the "window.postMessage" web API).
* [wpmlcore-3310] Post meta won't return an empty value after automatic post meta synchronization is ran.
* [wpmlcore-3244] Fixed an issue with the re-initialization of WPML in Network install, after resetting settings of main sub-site.
* [wpmlcore-3143] Add filters for the Events Calendar and Events Pro plugins to use for recurring events
* [wpmlcore-3009] Fixed duplicated language querystring argument when redirecting to a child page.
* [wpmlcore-3006] Improved usability of the admin notice "Term hierarchy synchronization".
* [wpmlcore-2682] Fixed an issue with the browser redirection when using languages with regional variations (e.g. fr-CA).
* [wpmlcore-2663] Fixed an issue with the browser redirection when using languages in directories.