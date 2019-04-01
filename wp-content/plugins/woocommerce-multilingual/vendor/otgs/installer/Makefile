# Tutorial: http://www.cs.colby.edu/maxwell/courses/tutorials/maketutor/
# Docs: https://www.gnu.org/software/make/

.PHONY: precommit
.PHONY: dupes comp jest phpunit test dev prod
.PHONY: setup test

setup:: vendor/autoload.php
setup:: yarn.lock
setup:: githooks

#test:: jest
test:: phpunit

precommit:: validate-composer
precommit:: validate-yarn
precommit:: dupes
precommit:: compatibility


# precommit

dupes: vendor/autoload.php
	./.make/check-duplicates.sh

compatibility: vendor/autoload.php
	./.make/check-compatibility.sh

validate-composer: composer.lock
	./.make/check-composer.sh

validate-yarn: yarn.lock
	./.make/check-yarn.sh


# Dependency managers

## Composer

composer.lock: composer.json
	composer install
	touch $@

composer.json:
	composer init -q

vendor/autoload.php: composer.lock
	composer install
	touch $@

## Yarn

yarn.lock: package.json
	yarn install
	touch $@

# Setup

githooks:
ifndef CI
	find .git/hooks -type l -exec rm {} \;
	find .githooks -type f -exec ln -sf ../../{} .git/hooks/ \;
else
	echo 'Skipping in CI'
endif

# Tests

jest: yarn.lock
	yarn run test

phpunit: vendor/autoload.php
	vendor/bin/phpunit --fail-on-warning

# Install
install:
	composer install
	yarn install

# Build

dev prod: yarn.lock
	yarn run build:$@
