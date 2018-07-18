#!/bin/bash

###
# Prepare a Pantheon site environment for the Behat test suite, by pushing the
# requested upstream branch to the environment. This script is architected
# such that it can be run a second time if a step fails.
###

set -ex

if [ -z "$TERMINUS_SITE" ] || [ -z "$TERMINUS_ENV" ]; then
	echo "TERMINUS_SITE and TERMINUS_ENV environment variables must be set"
	exit 1
fi

###
# Create a new environment for this particular test run.
###
terminus --yes env:info $TERMINUS_SITE.$TERMINUS_ENV 2>/dev/null || terminus --yes env:create $TERMINUS_SITE.dev $TERMINUS_ENV
terminus --yes env:wipe $TERMINUS_SITE.$TERMINUS_ENV

###
# Get all necessary environment details.
###
PANTHEON_GIT_URL=$(terminus connection:info $TERMINUS_SITE.$TERMINUS_ENV --field=git_url)
PANTHEON_SITE_URL="$TERMINUS_ENV-$TERMINUS_SITE.pantheonsite.io"
BASH_DIR="$( cd -P "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"

###
# Switch to git mode for pushing the files up
###
terminus --yes connection:set $TERMINUS_SITE.$TERMINUS_ENV git

###
# Push the upstream branch to the environment
###
git remote add pantheon $PANTHEON_GIT_URL
git push -f pantheon $CIRCLE_BRANCH:$TERMINUS_ENV

###
# Switch to SFTP mode so the site can install plugins and themes
###
terminus --yes connection:set $TERMINUS_SITE.$TERMINUS_ENV sftp
