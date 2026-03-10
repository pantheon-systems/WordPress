#!/bin/bash

#
# The purpose of this script is to examine the base branch that this PR is
# set to merge into by usig the GitHub API. We are only querying a public
# repo here, so we do not need to use the GITHUB_TOKEN.
#

# Exit if we are not running on Circle CI.
if [ -z "$CIRCLECI" ] ; then
  exit 0
fi

# We only need to make this check for branches forked from default (right) / master (wrong).
# Skip the test for the default branch. (The .circleci directory will never be added to the master branch).
if [ "$CIRCLE_BRANCH" == "default" ] ; then
  exit 0
fi

# We cannot continue unless we have a pull request.
if [ -z "$CIRCLE_PULL_REQUEST" ] ; then
  echo "No CIRCLE_PULL_REQUEST defined; please create a pull request."
  exit 1
fi

# CIRCLE_PULL_REQUEST=https://github.com/ORG/PROJECT/pull/NUMBER
PR_NUMBER=$(echo $CIRCLE_PULL_REQUEST | sed -e 's#.*/pull/##')

# Display the API call we are using
echo curl https://api.github.com/repos/$CIRCLE_PROJECT_USERNAME/$CIRCLE_PROJECT_REPONAME/pulls/$PR_NUMBER

base=$(curl https://api.github.com/repos/$CIRCLE_PROJECT_USERNAME/$CIRCLE_PROJECT_REPONAME/pulls/$PR_NUMBER 2>/dev/null | jq .base.ref)

echo "The base branch is $base"

# If the PR merges into 'default', then it is safe to merge.
if [ "$base" == '"default"' ] ; then
  echo "It is safe to merge this PR into the $base branch"
  exit 0
fi

# Force a test failure if the PR's base is the master branch.
if [ "$base" == '"master"' ] ; then
  echo "ERROR: merging this PR into the $base branch is not allowed. Change the base branch for the PR to merge into the \"default\" branch instead."
  exit 1
fi

echo "Merging probably okay, if you are merging one PR into another. Use caution; do not merge to the \"master\" branch."
