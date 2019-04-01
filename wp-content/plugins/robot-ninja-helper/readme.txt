=== Robot Ninja Helper ===

Contributors: prospress
Tags: woocommerce, robot ninja, tests
Requires at least: 4.4.0
Tested up to: 5.0
Requires PHP: 5.6
License: GPLv3
License URI: http://www.gnu.org/licenses/gpl-3.0.html
WC requires at least: 3.0
WC tested up to: 3.5.0
Stable tag: 1.8.0

Never lose time or sleep worrying about whether your WooCommerce store is working again with Robot Ninja automated checkout testing.

== Description ==

**Never lose time or sleep worrying about whether your online store is working again with Robot Ninja, the only automated checkout testing solution for WooCommerce.**

*Note: The Robot Ninja Helper plugin connects your online store to [Robot Ninja](https://robotninja.com/?utm_source=wporg&utm_medium=readme&utm_campaign=helperplugin). This plugin only works if you have a [Robot Ninja account](https://robotninja.com/?utm_source=wporg&utm_medium=readme&utm_campaign=helperplugin).*

Robot Ninja takes the tedious work out of manually testing your WooCommerce checkout. Simply connect your online store to Robot Ninja and it will run through a comprehensive series of tests using real products and real payments. No coding skills required.

In the time it takes to make a cup of coffee, Robot Ninja will thoroughly test your store and let you know whether your store is running smoothly or if you need to make fixes to get your checkout back in working order.

[youtube https://youtu.be/l9e4FlFGzJ4]

= Is Your WooCommerce Checkout Working? =

It’s a question that keeps a lot of store owners up at night. We know because they told us.

We interviewed dozens of store owners and developers before building Robot Ninja. They told us:

* All too often, store owners discover their WooCommerce checkout hasn’t been working for days, even weeks. Store owners usually find out when a loyal customer points it out.
* Checkout anxiety is common amongst store owners and developers. They worry about whether they’ve set up their online store correctly or if it’s actually working properly and accepting payments.
* Developers spend too much time testing and maintaining stores, using complicated and inventive ways to test if checkouts are working.

The solution? Automated testing for your WooCommerce store that you can set and forget with Robot Ninja.

= Robot Ninja’s Features =

Checking testing is one of those important things we all put off. We get it – it’s boring and time-consuming.

The alternative is a broken checkout and no one wants that. That’s why we built Robot Ninja to make testing your online store easier.

With Robot Ninja you can:

* Queue up checkout tests right now and watch as they’re processed in real-time.
* Schedule automated tests to run when it suits you – every day, week, you set what works for your maintenance schedule.
* View the results from your last Robot Ninja test.
* Get email notifications when a test fails, including information about the error, your store configuration, and a screenshot of what a user would see at the time of failure to help diagnose the issue.
* Test your online store using real products and real payments.

Robot Ninja tests your WooCommerce store in a fraction of the time it would take you to do it manually. Plus, it tests every aspect of your store – core pages (including shop and cart pages), whether existing customers can log in, guests can successfully checkout, existing customers can return to make more purchases, and more.

= No Coding Skills Required =

You don’t need to know how to code to use Robot Ninja. You also don’t need to use tools like Git or GitHub or install any complicated packages via terminal.

Simply connect your store to Robot Ninja to get started running your first test.

Whether you’re a store owner or a developer, Robot Ninja will take care of your testing for you so you can get on with the day-to-day running of your store.

= A Time-Saving Tool for Agencies =

Do you maintain multiple WooCommerce stores for clients? With Robot Ninja, you can connect and test multiple checkouts and receive alerts when a client’s store goes down.

* Manage multiple stores from one account.
* Receive notifications if a client’s store stops working.
* Streamline and improve your testing workflow after WooCommerce and WordPress updates.
* Maintain and view a history of test results for your stores.

Robot Ninja is an ideal complement to regular uptime monitoring since it does more than just check for a HTML response.

= Sign Up For Free =

Get started using Robot Ninja for free. With our Free plan, you can start testing your WooCommerce store today and upgrade later to test unlimited stores and checkout testing using real payments.

= About Us =

Robot Ninja is brought to you by [Prospress](https://prospress.com/?utm_source=wporg&utm_medium=readme&utm_campaign=rnhelperplugin). Our mission is simple – to help people prosper with WordPress.

We’ve been working with WooCommerce for over 6 years and are responsible for popular extensions like [WooCommerce Subscriptions](https://woocommerce.com/products/woocommerce-subscriptions/).

We’ve talked and listened to many store managers and developers – both freelance and agencies – over the years and found the same concerns around checkout testing kept cropping up in conversations.

We wanted to build something to help people save time and money and relieve some of the anxiety around managing an online store. This is where the idea for Robot Ninja was born.

Want to learn more about Robot Ninja and end-to-end checkout testing? Check out [our blog](https://robotninja.com/blog/?utm_source=wporg&utm_medium=readme&utm_campaign=helperplugin) for all the latest updates.

== Installation ==

1. Upload the `robot-ninja-helper` plugin to your `/wp-content/plugins/` directory.
1. Activate the plugin through the 'Plugins' menu in WordPress.
1. Done! There isn't anything else to configure. Head back to [Robot Ninja](https://robotninja.com/?utm_source=wporg&utm_medium=readme&utm_campaign=helperplugin) to continue adding your store.

== Frequently Asked Questions ==

**Do I need a Robot Ninja account to use this plugin?**

Yes. You can sign up for a [free account here](https://robotninja.com/?utm_source=wporg&utm_medium=readme&utm_campaign=helperplugin). The pugin will still work but it won’t provide you any benefit without the service.

**What does the helper plugin do?**

* Making sure the Robot Ninja customer used for tests has an empty cart when logging in to provide a clean slate for testing.
* Register additional fields to the WooCommerce /system_status endpoint to help Robot Ninja identify:
  * Whether _Guest Checkout_ is enabled
  * The location of key WooCommerce pages (shop, cart, checkout, myaccount)
  * The cheapest product in your store (used as the default test product)

== Changelog ==

= 1.8.0 =
* Fixes issue with password protected and private simple/variation products being chosen for testing
* Adds new options for disabling order/subscription emails being sent for Robot Ninja tests

= 1.7.1 =
* Fixes error on stores running early versions of PHP (pre PHP5.5)
* Fixes error on WordPress 5.0 where custom api response fields are not being included in responses if they don't have a schema
* Update the readme.txt versions for required PHP version and WP version tested up to

= 1.7.0 =
* Adds the Avada theme checkout layout setting to system status to help with Robot Ninja tests
* Adds the add the cart behaviour WooCommerce setting to system status to help with Robot Ninja tests

= 1.6.1 =
* Update the WC tested up to version

= 1.6.0 =
* Adds additional guest checkout setting to the system status for Robot Ninja tests
* Adds option for new constant (`RN_REDUCE_STOCK`) to be set to false to turn off reducing stock during tests

= 1.5.1 =
* Fixes issue with connecting your store to Robot Ninja if the REST API discovery URL has been removed

= 1.5.0 =
* Adds checkout settings to System Status report for Robot Ninja
* Update the WC tested up to version

= 1.4.2 =
* Fixes issue with variation product IDs returned in System Status not being useable in the 'include' param of the WC Products API

= 1.4.1 =
* Tweaks the page URLs sent to Robot Ninja to be permalinks instead of ?p=ID

= 1.4.0 =
* Adds better support for sites that don't automatically set Basic HTTP Auth server variables.
* Fixes the product query to return variation products that are in-stock, regardless of the whether the parent product is setup to have 0 stock
* Update the WC tested up to version to 3.3.5

= 1.3.2 =
* Adds Intuit QBMS gateway settings to System Status report for Robot Ninja

= 1.3.1 =
* Fix compatibility with WooCommerce 3.3+

= 1.3.0 =
* Adds some gateway settings data to the System Status report for the new gateways supported in Robot Ninja (adds Stripe, Authorize.net CIM, Braintree Credit Card & Moneris)

= 1.2.0 =
* Fix deploy script
* Tweak plugin description and assets files for WordPress.org
* Add new ssl check to the /rn/helper/status endpoint.
* Tweak product query to return the cheapest product in the System Status report (instead of the two most popular)
* Test compatibility with WordPress v4.9.1 and WooCommerce v3.2.5 and update the "Tested up to" versions.

= 1.1.1 =
* Fix to make sure the cart is always empty before Robot Ninja runs tests for stores using WC 3.0+

= 1.1.0 =
* Add additional php_auth_user, php_auth_pw, http_authorization and redirect_http_authorization properties to the /rn/helper/status route that return booleans. Used to help determine if api authentication will work or not.

= 1.0.2 =
* Fix the required WooCommerce version mentioned in admin notice.
* Uses WC_VERSION instead of `woocommerce_db_version` to check the version of WooCommerce installed.

= 1.0.1 =
* Fix the query which retrieves two products to test. The helper will now properly add two products that are in stock and also visible to the catalog to the system status payload.

= 1.0.0 =
* Initial beta release.
