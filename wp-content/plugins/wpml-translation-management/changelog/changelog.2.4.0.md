# Features
* [wpmltm-1815] Added the Translation Feedback module
* [wpmltm-1811] Removed dependency with WPML Page Builder, as is now merged into String Translation
* [wpmltm-1770] Provided a user interface to create and edit a custom wpml-config.xml. The configuration in this file will override any existing settings from plugins or themes.

# Fixes
* [wpmltm-1867] Fixed a malformed XLIFF file with ampersand in attributes
* [wpmltm-1865] Fixed a too greedy logic to disconnect duplicates which are sent to translation
* [wpmltm-1842] Fixed a PHP Notice in the translation queue when filtering by package
* [wpmltm-1831] Prevent multiple occurrences of the language parameter in page builders' links.
* [wpmltm-1828] Fixed an issue with translation job impossible to complete because of a `#` URL
* [wpmltm-1825] Fixed a PHP notice thrown when sending new translation jobs
* [wpmltm-1822] Fixed link to the communication log when the log is empty and the logging disabled
* [wpmltm-1820] Fixed an issue preventing to resend a translation waiting for translator
* [wpmltm-1798] Blocked duplication for external types
* [wpmltm-1784] Fixed a compatibility issue which was requiring 2 saves to actually update the translation
* [wpmltm-1783] Fix the problem with pages duplicating when a list was filtered
* [wpmltm-1762] Fixed the translation status when the source post excerpt is updated
* [wpmltm-1747] Fixed an issue with "Translation is complete" checkbox when text tab is active in translation editor
* [wpmltm-1479] Handled a fatal error thrown when the translation proxy is not available