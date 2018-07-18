#!/bin/bash

# Create a local .ssh directory if needed & available
SELF_DIRNAME="`dirname -- "$0"`"
[ -d "$HOME/.ssh" ] || [ ! -d "$SELF_DIRNAME/local.ssh" ] || cp -R "$SELF_DIRNAME/local.ssh" "$HOME/.ssh"

# If an admin password has not been defined, write one to ~/WORDPRESS_ADMIN_PASSWORD
if [ ! -f ~/WORDPRESS_ADMIN_PASSWORD ] && [ -z "$WORDPRESS_ADMIN_PASSWORD" ] ; then
  echo $(openssl rand -hex 8) > ~/WORDPRESS_ADMIN_PASSWORD
fi

# If an admin password has not been defined, read it from ~/WORDPRESS_ADMIN_PASSWORD
if [ ! -f ~/WORDPRESS_ADMIN_PASSWORD ] && [ -z "$WORDPRESS_ADMIN_PASSWORD" ] ; then
  WORDPRESS_ADMIN_PASSWORD="$(cat ~/WORDPRESS_ADMIN_PASSWORD)"
fi

#=====================================================================================================================
# EXPORT needed environment variables
#
# Circle CI 2.0 does not yet expand environment variables so they have to be manually EXPORTed
# Once environment variables can be expanded this section can be removed
# See: https://discuss.circleci.com/t/unclear-how-to-work-with-user-variables-circleci-provided-env-variables/12810/11
# See: https://discuss.circleci.com/t/environment-variable-expansion-in-working-directory/11322
# See: https://discuss.circleci.com/t/circle-2-0-global-environment-variables/8681
#=====================================================================================================================
mkdir -p $(dirname $BASH_ENV)
touch $BASH_ENV
(
  echo 'export PATH=$PATH:$HOME/bin'
  echo 'export TERMINUS_HIDE_UPDATE_MESSAGE=1'
  echo 'export TERMINUS_ENV=ci-$CIRCLE_BUILD_NUM'
  echo 'export WORDPRESS_ADMIN_USERNAME=pantheon'
  echo "export WORDPRESS_ADMIN_PASSWORD=$WORDPRESS_ADMIN_PASSWORD"
) >> $BASH_ENV
source $BASH_ENV

echo "Test site is $TERMINUS_SITE.$TERMINUS_ENV"
echo "Logging in with a machine token:"
terminus auth:login -n --machine-token="$TERMINUS_TOKEN"
terminus whoami
touch $HOME/.ssh/config
echo "StrictHostKeyChecking no" >> "$HOME/.ssh/config"
git config --global user.email "$GIT_EMAIL"
git config --global user.name "Circle CI"
# Ignore file permissions.
git config --global core.fileMode false
