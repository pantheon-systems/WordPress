# Fixes
* [wpmlst-469] Solved `Warning: in_array() expects parameter 2 to be array, null given`
* [wpmlst-467] Improve performance of string translation
* [wpmlst-462] Fixed too many SQL queries when the user's administrator language is not one of the active languages
* [wpmlst-461] Improved performance with slug translation
* [wpmlst-460] Fixed `icl_register_string` to reduce the number of SQL queries
* [wpmlst-432] Clear the current string language cache when switching languages via 'wpml_switch_language' action