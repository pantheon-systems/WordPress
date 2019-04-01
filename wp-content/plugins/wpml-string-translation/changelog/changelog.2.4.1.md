# Features
* [wpmlst-888] Improved caching of strings per page, it requires less memory in db now.

# Fixes
* [wpmlst-886] Improved caching of strings per page, to not flood tables with duplicated data and not cause performance issues
* [wpmlst-882] Improved handling of the the admin notice Something doesn't look right with "Caching of String Translation plugin"
* [wpmlst-881] Removed leading backslash `\` to avoid warnings in PHP <5.3
* [wpmlst-880] Fixed error appearing during plugin update