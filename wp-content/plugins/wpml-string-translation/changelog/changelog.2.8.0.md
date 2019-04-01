# Features
* [wpmlst-899] Added support for taxonomy base slug translation.
* [wpmlst-1591] Removed the use of the default value for string translations, which is not used as a input placeholder

# Fixes
* [wpmlst-1748] Prevent the error notice when a string was registered in a concurrent request
* [wpmlst-1682] Resolved fatal error reported using 4.0.0-b.1 when String Translation was updated before Core
* [wpmlst-1680] Fixed issue when registering admin text strings containing wildcards
* [wpmlst-1672] Fixed issue when searching for string which could return improper malformed value
* [wpmlst-1653] Fixed performance bottleneck with repetitive cache slowing down the post save action for posts/pages created with Page Builders
* [wpmlst-1651] Prevent a memory leak when saving a page builder post with a big number of elements.
* [wpmlst-1645] Fixed an issue occurring in Network sites with translations of admin strings not being shown for non-logged in users
* [wpmlst-1641] Admin String returns cached value instead of proper value in multisite environment
* [wpmlst-1637] Fixed a 404 error when a hierarchical CPT was sharing the same slug in different languages
* [wpmlst-1635] Fixed wrong slug base when the rewrite slug is changed.
* [wpmlst-1596] Resolved exception in some caching configurations not allowing the scanning of .mo files to complete