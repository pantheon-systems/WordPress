#!/bin/bash

# Echo commands as they are executed, but don't allow errors to stop the script.
set -x

if [ -z "$TERMINUS_SITE" ] || [ -z "$TERMINUS_ENV" ]; then
  echo "TERMINUS_SITE and TERMINUS_ENV environment variables must be set"
  exit 1
fi

# Only delete old environments if there is a pattern defined to
# match environments eligible for deletion. Otherwise, delete the
# current multidev environment immediately.
#
# To use this feature, set MULTIDEV_DELETE_PATTERN to '^ci-' or similar
# in the CI server environment variables.
if [ -z "$MULTIDEV_DELETE_PATTERN" ] ; then
  terminus env:delete $TERMINUS_SITE.$TERMINUS_ENV --delete-branch --yes
  exit 0
fi

# List all but the newest two environments.
OLDEST_ENVIRONMENTS=$(terminus env:list "$TERMINUS_SITE" --format=list | grep -v dev | grep -v test | grep -v live | sort -k2 | grep "$MULTIDEV_DELETE_PATTERN" | sed -e '$d' | sed -e '$d')

# Exit if there are no environments to delete
if [ -z "$OLDEST_ENVIRONMENTS" ] ; then
  exit 0
fi

# Go ahead and delete the oldest environments.
for ENV_TO_DELETE in $OLDEST_ENVIRONMENTS ; do
  terminus env:delete $TERMINUS_SITE.$ENV_TO_DELETE --delete-branch --yes
done
