=== WordPress Native PHP Sessions ===
Contributors: getpantheon, outlandish josh, mpvanwinkle77, danielbachhuber
Tags: comments, sessions
Requires at least: 3.0.1
Tested up to: 4.9
Stable tag: 0.6.6
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Use native PHP sessions and stay horizontally scalable. Better living through superior technology.

== Description ==

[![Build Status](https://travis-ci.org/pantheon-systems/wp-native-php-sessions.svg?branch=master)](https://travis-ci.org/pantheon-systems/wp-native-php-sessions) [![CircleCI](https://circleci.com/gh/pantheon-systems/wp-native-php-sessions/tree/master.svg?style=svg)](https://circleci.com/gh/pantheon-systems/wp-native-php-sessions/tree/master)

WordPress core does not use PHP sessions, but sometimes they are required by your use-case, a plugin or theme.

This plugin implements PHP's native session handlers, backed by the WordPress database. This allows plugins, themes, and custom code to safely use PHP `$_SESSION`s in a distributed environment where PHP's default tempfile storage just won't work.

Note that primary development is on GitHub if you would like to contribute:

https://github.com/pantheon-systems/wp-native-php-sessions

== Installation ==

1. Upload to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress

That's it!

== Contributing ==

The best way to contribute to the development of this plugin is by participating on the GitHub project:

https://github.com/pantheon-systems/wp-native-php-sessions

Pull requests and issues are welcome!

You may notice there are two sets of tests running, on two different services:

* Travis CI runs the [PHPUnit](https://phpunit.de/) test suite.
* Circle CI runs the [Behat](http://behat.org/) test suite against a Pantheon site, to ensure the plugin's compatibility with the Pantheon platform.

Both of these test suites can be run locally, with a varying amount of setup.

PHPUnit requires the [WordPress PHPUnit test suite](https://make.wordpress.org/core/handbook/testing/automated-testing/phpunit/), and access to a database with name `wordpress_test`. If you haven't already configured the test suite locally, you can run `bash bin/install-wp-tests.sh wordpress_test root '' localhost`.

Behat requires a Pantheon site. Once you've created the site, you'll need [install Terminus](https://github.com/pantheon-systems/terminus#installation), and set the `TERMINUS_TOKEN`, `TERMINUS_SITE`, and `TERMINUS_ENV` environment variables. Then, you can run `./bin/behat-prepare.sh` to prepare the site for the test suite.

== Frequently Asked Questions ==

= Why not use another session plugin? =

This implements the built-in PHP session handling functions, rather than introducing anything custom. That way you can use built-in language functions like the `$_SESSION` superglobal and `session_start()` in your code. Everything else will "just work".

= Why store them in the database? =

PHP's fallback default functionality is to allow sessions to be stored in a temporary file. This is what most code that invokes sessions uses by default, and in simple use-cases it works, which is why so many plugins do it.

However, if you intend to scale your application, local tempfiles are a dangerous choice. They are not shared between different instances of the application, producing erratic behavior that can be impossible to debug. By storing them in the database the state of the sessions is shared across all application instances.

== Troubleshooting ==

If you see an error like "Fatal error: session_start(): Failed to initialize storage module: user (path: ) in .../code/wp-content/plugins/plugin-that-uses-sessions/example.php on line 2" you likely have a plugin in the mu-plugins directory that is instantiating a session prior to this plugin loading. To fix, you will need to deactivate this plugin and instead load it via an mu-plugin that loads first, e.g. create an mu-plugin called 00.php and add a line in it to include the wp-native-php-sessions/pantheon-sessions.php file and the problem should disappear.


== Changelog ==

= 0.6.6 (March 8th, 2018) =
* Restores session instantiation when WP-CLI is executing, because not doing so causes other problems.

= 0.6.5 (February 6th, 2018) =
* Disables session instantiation when `defined( 'WP_CLI' ) && WP_CLI` because sessions don't work on CLI.

= 0.6.4 (October 10th, 2017) =
* Triggers PHP error when plugin fails to write session to database.

= 0.6.3 (September 29th, 2017) =
* Returns false when we entirely fail to generate a session.

= 0.6.2 (June 6th, 2017) =
* Syncs session user id when a user logs in and logs out.

= 0.6.1 (May 25th, 2017) =
* Bug fix: Prevents warning session_write_close() expects exactly 0 parameters, 1 given.

= 0.6.0 (November 23rd, 2016) =
* Bug fix: Prevents PHP fatal error in `session_write_close()` by running on WordPress' `shutdown` action, before `$wpdb` destructs itself.
* Bug fix: Stores the actual user id in the sessions table, instead of `(bool) $user_id`.

= 0.5 =
* Compatibility with PHP 7.
* Adds `pantheon_session_expiration` filter to modify session expiration value.

= 0.4 = 
* Adjustment to `session_id()` behavior for wider compatibility
* Using superglobal for REQUEST_TIME as opposed to `time()`

= 0.3 = 
* Fixes issue related to WordPress plugin load order

= 0.1 =
* Initial release
