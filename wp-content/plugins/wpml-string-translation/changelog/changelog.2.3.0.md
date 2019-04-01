# Fixes
* [wpmlst-664] Fixed ssue translating slugs for WooCommerce product base
* [wpmlst-655] Fixed domain name fallback to 'default' or 'WordPress' for gettext context strings
* [wpmlst-630] Fixed a glitch causing the registration of WPML-ST strings when scanning a theme
* [wpmlst-629] Removed dependency of ST with TM, causing a `Call to undefined function object_to_array()` fatal error
* [wpmlst-619] The WPML language selector properly shows language names in the right language
* [wpmlst-595] Resolved issues when deleting a Layout which has no cells (hence no package)
* [wpmlst-572] Fixed broken HTML in Auto Register Strings
* [wpmlst-547] Improved handling of strings by WP Customizer when default language is other than English
* [wpmlst-505] Add support for sending strings in any language to the translation basket
* [wpmlst-483] Fixed registering of strings with gettext_contexts
* [wpmlst-482] Add a language selector to the Admin bar menu to set the language of a package (eg. as seen on GravityForms)
* [wpmlst-475] Add a language selector to the package metabox (eg. as seen on the Layout editor)
* [wpmlst-474] Added the package language to the url to the translation dashboard (this applies to the Package box, where used by other plugins like Layouts)
* [wpmlst-471] Allow icl_register_string to register a string in any language
* [wpmlst-426] Footer in emails are now shown in the right languagegit pull --renaasas