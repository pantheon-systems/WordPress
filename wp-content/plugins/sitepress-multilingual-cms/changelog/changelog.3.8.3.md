# Fixes
* [wpmlcore-4846] Fixed fatal error occurring after updating an original post
* [wpmlcore-4833] [Security] Validated and escaped the value assigned to the current language, especially when read from GET or POST requests. Credit for this goes to Jouko Pynnonen (https://klikki.fi) who reported the possible exploit.
* [wpmlcore-4820] Fixed cache issue with Language Switcher when new languages are activated