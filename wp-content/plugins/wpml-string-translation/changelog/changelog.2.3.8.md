# Fixes
* [wpmlst-817] Fixed possible XSS issue in the taxonomy label translation.
* [wpmlst-798] Added object caching and optimized code for getting translated strings
* [wpmlst-793] Added migration logic to reuse existing string translations if they exist
* [wpmlst-784] Don't show translated blog name and description on customize.php
* [wpmlst-778] CPT archive slug wrongly translated when `has_archive` starts with the rewrite slug of CPT