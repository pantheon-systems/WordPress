# Fixes
* [wpmlcore-2828] Fixed malformed pagination URL when using pretty permalinks and language as a parameter
* [wpmlcore-2824] Improved caching of converted URLs
* [wpmlcore-2823] On front page with paginated content, we had an unexpected redirection in secondary language from 2nd page to 1st page (pretty permalinks and language as a parameter)
* [wpmlcore-2822] The second page of paginated content was not accessible in secondary language with same slug in translated content
* [wpmlcore-2821] Fixed malformed pagination URL when using pretty permalinks and language as a parameter
* [wpmlcore-2816] Fixed malformed pagination URL when using pretty permalinks and language as a parameter
* [wpmlcore-2805] Resolved issue with WooCommerce Cart not showing products when switching between languages in domains
* [wpmlcore-2802] Corrected wrong paginated link with plain URL and language as a parameter
* [wpmlcore-2795] `is_front_page()` did not work with "lang" parameter and another parameter
* [wpmlcore-2793] Language as parameter, conflicts with browser-redirect and manually set home page
* [wpmlcore-2792] `PHP Notice: Undefined offset: 0 in sitepress.class.php on line 2345`