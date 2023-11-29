=== WordPress Native PHP Sessions ===
Contributors: getpantheon, outlandish josh, mpvanwinkle77, danielbachhuber, andrew.taylor, jazzs3quence, stovak, jspellman, rwagner00
Tags: comments, sessions
Requires at least: 4.7
Tested up to: 6.3
Stable tag: 1.4.3
Requires PHP: 5.4
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

== Configuration ==

By default the session lifetime is set to 0, which is until the browser is closed.

To override this use the `pantheon_session_expiration` filter before the WordPress Native PHP Sessions plugin is loaded. For example a small Must-use plugin (a.k.a. mu-plugin) could contain:

    <?php
    function my_session_expiration_override() {
        return 60*60*4; // 4 hours
    }
    add_filter( 'pantheon_session_expiration', 'my_session_expiration_override' );

== CLI Commands ==

**wp pantheon session add-index**

Added in 1.4.0. This command should be run if your installation of the plugin occurred before the addition of the primary ID key to the session table in version 1.2.2. You will be automatically notified when you visit any admin page if this is the case. If there's no message, your version is good to go. Note that this command is non-destructive, a new table will be created and the existing one preserved in a backup state until you have verified that the upgrade is functioning as expected.

**wp pantheon session primary-key-finalize**

Added in 1.4.0. If you have run the `add-index` command and have verified that the new table is functioning correctly, running the `primary-key-finalize` command will perform a database cleanup and remove the backup table.

**wp pantheon session primary-key-revert**

Added in 1.4.0. If you have run the `add-index` command and something unexpected has occurred, just run the `primary-key-revert` command and the backup table will immediately be returned to being the active table.

= WordPress Multisite =
As of 1.4.2 the `add-index`, `primary-key-add` and `primary-key-revert` commands are fully multisite compatible.

== Contributing ==

See [CONTRIBUTING.md](https://github.com/pantheon-systems/wp-native-php-sessions/blob/main/CONTRIBUTING.md) for information on contributing.

== Frequently Asked Questions ==

= Why not use another session plugin? =

This implements the built-in PHP session handling functions, rather than introducing anything custom. That way you can use built-in language functions like the `$_SESSION` superglobal and `session_start()` in your code. Everything else will "just work".

= Why store them in the database? =

PHP's fallback default functionality is to allow sessions to be stored in a temporary file. This is what most code that invokes sessions uses by default, and in simple use-cases it works, which is why so many plugins do it.

However, if you intend to scale your application, local tempfiles are a dangerous choice. They are not shared between different instances of the application, producing erratic behavior that can be impossible to debug. By storing them in the database the state of the sessions is shared across all application instances.

= Where do I report security bugs found in this plugin? =

Please report security bugs found in the source code of the WP Native PHP Sessions plugin through the [Patchstack Vulnerability Disclosure Program](https://patchstack.com/database/vdp/wp-native-php-sessions). The Patchstack team will assist you with verification, CVE assignment, and notify the developers of this plugin.

== Troubleshooting ==

If you see an error like "Fatal error: session_start(): Failed to initialize storage module:" or "Warning: ini_set(): A session is active.", then you likely have a plugin that is starting a session before WP Native PHP Sessions is loading.

To fix, create a new file at `wp-content/mu-plugins/000-loader.php` and include the following:

    <?php
    require_once WP_PLUGIN_DIR . '/wp-native-php-sessions/pantheon-sessions.php';

This mu-plugin will load WP Native PHP Sessions before all other plugins, while letting you still use the WordPress plugin updater to keep the plugin up-to-date.

== Upgrade Notice ==

= 1.4.0 (October 17, 2023) =
Adds a WP-CLI command to add an index to the sessions table if one does not exist already. If you installed this plugin before version 1.2.2, you likely need to run this command. However, regardless of version at installation a notice will appear in your admin dashboard if your database table is missing the index. If no notice appears, no action is necessary.

== Changelog ==

= 1.4.3 (November 13, 2023) =
* Fixed a PHP warning when running the `pantheon session add-index` command on a single site installation. [[#285](https://github.com/pantheon-systems/wp-native-php-sessions/pull/285)]

= 1.4.2 (November 8, 2023) =
* Fixed an issue with the `pantheon session add-index` PHP warning. [[#276](https://github.com/pantheon-systems/wp-native-php-sessions/pull/276/files)]
* Fixed a syntax issue with the suggested WP CLI commands [[#278](https://github.com/pantheon-systems/wp-native-php-sessions/pull/278)]
* Made `wp pantheon session add-index`, `wp pantheon session primary-key-finalize`, and `wp pantheon session primary-key-revert` fully multisite compatible. [[#275](https://github.com/pantheon-systems/wp-native-php-sessions/pull/275)]

= 1.4.1 (October 23, 2023) =
* Fixed an issue with the `pantheon session add-index` command not working properly on WP multisite [[#270](https://github.com/pantheon-systems/wp-native-php-sessions/pull/270)]
* Made the notice added in 1.4.0 dismissable (stores in user meta) & hides for multisite (an update is coming to iterate through all sites on a network) [[#271](https://github.com/pantheon-systems/wp-native-php-sessions/pull/271)]


= 1.4.0 (October 17, 2023) =
* Adds new CLI command to add a Primary Column (id) to the `pantheon_sessions` table for users who do not have one. [[#265](https://github.com/pantheon-systems/wp-native-php-sessions/pull/265)]
* Adds alert to dashboard for users who need to run the command.
* 8.3 compatibility and code quality updates
* Updates Pantheon WP Coding Standards to 2.0 [[#264](https://github.com/pantheon-systems/wp-native-php-sessions/pull/264)]

= 1.3.6 (June 1, 2023) =
* Fixes PHP 8.2 deprecated dynamic property error [[#251](https://github.com/pantheon-systems/wp-native-php-sessions/pull/251)] (props @miguelaxcar)
* Update CONTRIBUTING.md [[#252](https://github.com/pantheon-systems/wp-native-php-sessions/pull/252)].
* Update informational Error message for the case of headers already sent [[#249](https://github.com/pantheon-systems/wp-native-php-sessions/pull/249)].
* Add pantheon-wp-coding-standards [[#247](https://github.com/pantheon-systems/wp-native-php-sessions/pull/247)].

= 1.3.5 (April 7, 2023) =
* Bump yoast/phpunit-polyfills from 1.0.4 to 1.0.5 [[#245](https://github.com/pantheon-systems/wp-native-php-sessions/pull/245)].
* Bump tested up to version

= 1.3.4 (February 7, 2023) =
* Add fallback for $session->get_data() [[#237(https://github.com/pantheon-systems/wp-native-php-sessions/pull/237)]] (reported [on WordPress.org](https://wordpress.org/support/topic/php-warning-session_start-failed-to-read-session-data-user/))
* Update CODEOWNERS file [[#239](https://github.com/pantheon-systems/wp-native-php-sessions/pull/239)]
* Fix GPL license in `composer.json` file [[#236](https://github.com/pantheon-systems/wp-native-php-sessions/pull/236)]
* Bump grunt from 1.5.3 to 1.6.1 [[#235](https://github.com/pantheon-systems/wp-native-php-sessions/pull/235)]

= 1.3.3 (January 25, 2023) =
* Bump version in pantheon-sessions.php [[#234](https://github.com/pantheon-systems/wp-native-php-sessions/pull/234)].

= 1.3.2 (January 25, 2023) =
* PHP 8.2 compatibility [[#232](https://github.com/pantheon-systems/wp-native-php-sessions/pull/232)].
* Bump dealerdirect/phpcodesniffer-composer-installer from 0.7.2 to 1.0.0 [[#229](https://github.com/pantheon-systems/wp-native-php-sessions/pull/229)].
* Update images for lint and test-behat jobs [[#228](https://github.com/pantheon-systems/wp-native-php-sessions/pull/228)].

= 1.3.1 (December 5, 2022) =
* Document session lifetime handling [[#224](https://github.com/pantheon-systems/wp-native-php-sessions/pull/224)].
* Make dependabot target develop branch [[#226](https://github.com/pantheon-systems/wp-native-php-sessions/pull/226)].
* Ignore `.wordpress-org` directory [[#223](https://github.com/pantheon-systems/wp-native-php-sessions/pull/223)].

= 1.3.0 (November 28th, 2022) =
* Added CONTRIBUTING.MD and GitHub action to automate deployments to wp.org. [[#219](https://github.com/pantheon-systems/wp-native-php-sessions/pull/219)]

= 1.2.5 (October 28, 2022) =
* Added `#[ReturnTypeWillChange]` where required to silence deprecation warnings in PHP 8.1. [[#216](https://github.com/pantheon-systems/wp-native-php-sessions/pull/216)]

= 1.2.4 (September 14th, 2021) =
* Increases data blob size from 64k to 16M for new session tables; existing tables will need to manually modify the column if they want to apply this change [[#193](https://github.com/pantheon-systems/wp-native-php-sessions/pull/193)].

= 1.2.3 (April 9th, 2021) =
* Assigns the table name to a variable before using in query [[#188](https://github.com/pantheon-systems/wp-native-php-sessions/pull/188)].

= 1.2.2 (March 29th, 2021) =
* Includes an auto-incrementing `id` column for replication support [[#187](https://github.com/pantheon-systems/wp-native-php-sessions/pull/187)].

= 1.2.1 (September 17th, 2020) =
* Plugin textdomain needs to be the same as the WordPress.org slug [[#169](https://github.com/pantheon-systems/wp-native-php-sessions/pull/169)].

= 1.2.0 (May 18th, 2020) =
* Avoids using cookies for sessions when WP-CLI is executing [[#154](https://github.com/pantheon-systems/wp-native-php-sessions/pull/154)].

= 1.1.0 (April 23rd, 2020) =
* Avoids initializing PHP sessions when doing cron [[#149](https://github.com/pantheon-systems/wp-native-php-sessions/pull/149)].

= 1.0.0 (March 2nd, 2020) =
* Plugin is stable.

= 0.9.0 (October 14th, 2019) =
* Refactors session callback logic into `Session_Handler` abstraction, fixing PHP notice in PHP 7.3 [[#135](https://github.com/pantheon-systems/wp-native-php-sessions/pull/135)].

= 0.8.1 (August 19th, 2019) =
* Fixes handling of 'X-Forwarded-For' header in `get_client_ip_server()` [[#126](https://github.com/pantheon-systems/wp-native-php-sessions/pull/126)].

= 0.8.0 (August 13th, 2019) =
* Respects various `HTTP_*` sources for client IP address [[#122](https://github.com/pantheon-systems/wp-native-php-sessions/pull/122)].

= 0.7.0 (April 3rd, 2019) =
* Adds a safety check that restores `$wpdb` when it's missing.

= 0.6.9 (May 15th, 2018) =
* Ensures `_pantheon_session_destroy()` uses a return value.

= 0.6.8 (May 4th, 2018) =
* Switches to `E_USER_WARNING` instead of `E_WARNING` when triggering errors.

= 0.6.7 (April 26th, 2018) =
* Disables plugin load when `WP_INSTALLING`, because session table creation breaks installation process.

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
