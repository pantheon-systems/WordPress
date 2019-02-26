# Fixes
* [wpmlcore-5426] Fix a browser redirection error when the first browser language does not match any available language.
* [wpmlcore-5424] Fixed fatal error occurring whenever parse_url function returns false, due to malformed URL passed to our URL Converter
* [wpmlcore-5423] Resolved `Catchable fatal error: Method WPML_Admin_Menu_Item::__toString() must return a string value`
* [wpmlcore-5421] Fixed a 404 error or wrong redirection when a translation has the same slug as the original and the post type is "displayed as translated".