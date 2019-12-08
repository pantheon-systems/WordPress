#!/bin/sh
version=`cat "fastly.php" | grep '\@version' | perl -pe 's/.*\@version ([^ ]+).*$/$1/'`
file="fastly-${version}.zip"
rm "$file" >/dev/null 2>&1 
zip -r "${file}" . -x "build.sh" -x ".*" -x "*.zip"
