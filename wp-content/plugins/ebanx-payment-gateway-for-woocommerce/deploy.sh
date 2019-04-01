#!/usr/bin/env bash

if [[ -z "$TRAVIS" ]]; then
	echo "Script is only to be run by Travis CI" 1>&2
	exit 1
fi

if [[ -z "$WP_ORG_USERNAME" ]]; then
    echo "WordPress.org username not set" 1>&2
    exit 1
fi

if [[ -z "$WP_ORG_PASSWORD" ]]; then
    echo "WordPress.org password not set" 1>&2
    exit 1
fi

if [[ -z "$TRAVIS_TAG" ]]; then
    echo "Build must be tag" 1>&2
    exit 0
fi

SLUG="ebanx-payment-gateway-for-woocommerce"
SVN_ROOT_PATH="$TRAVIS_BUILD_DIR/build/svn"

# Clean up any previous svn dir
rm -fR $SVN_ROOT_PATH

echo "Performing Checkout from http://svn.wp-plugins.org/$SLUG"
# Checkout the SVN repo
svn co -q "http://svn.wp-plugins.org/$SLUG" $SVN_ROOT_PATH

# Erase trunk
rm -fR "$SVN_ROOT_PATH/trunk"
# Create trunk directory
mkdir $SVN_ROOT_PATH/trunk

echo "Synchronizing trunk"
# Copy our new version of the plugin into trunk
rsync -r -p --exclude=build --exclude=tests --exclude=_vendor $TRAVIS_BUILD_DIR/* $SVN_ROOT_PATH/trunk

# Erase possible broken version
rm -fR "$SVN_ROOT_PATH/tags/$TRAVIS_TAG"
# Add new version tag
mkdir $SVN_ROOT_PATH/tags/$TRAVIS_TAG

echo "Synchronizing tag $TRAVIS_TAG"
# Copy our new version of the plugin into tag directory
rsync -r -p --exclude=build --exclude=tests --exclude=_vendor $TRAVIS_BUILD_DIR/* $SVN_ROOT_PATH/tags/$TRAVIS_TAG

echo "Generating diff"
# Add new files to SVN
svn stat $SVN_ROOT_PATH | grep '^?' | awk '{print $2}' | xargs -I x svn add x@
# Remove deleted files from SVN
svn stat $SVN_ROOT_PATH | grep '^!' | awk '{print $2}' | xargs -I x svn rm --force x@

echo "Uploading to SVN"
# Commit to SVN
svn ci --no-auth-cache --username $WP_ORG_USERNAME --password $WP_ORG_PASSWORD $SVN_ROOT_PATH -m "Deploy version $TRAVIS_TAG"

echo "Cleaning temp dirs"
# Remove SVN temp dir
rm -fR $SVN_ROOT_PATH

rm -rf vendor
mv _vendor vendor
