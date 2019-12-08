=== Plugin Name ===
Contributors: cteitzel, tynor
Tags: encrypt, secrets management, secrets, encryption, security, API, key, password, security, secure, locker
Requires at least: 2.7
Tested up to: 5.2
Requires PHP: 5.5
Stable tag: trunk
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Lockr is the first API & Encryption key management service for WordPress, providing an affordable solution to secure secrets used by plugins.

== Description ==

= API & ENCRYPTION KEY MANAGEMENT FOR WORDPRESS =

Lockr is the first hosted secrets management solution for WordPress, providing an affordable solution for all sites to properly manage site secrets such as API and encryption keys used by their plugins. Lockr's offsite key management solution protects against critical vulnerabilities, delivers best-practice security to help sites comply with many industry regulations, and provides a Defense in Depth approach to securing your data. Lockr also provides AES-256 encryption to your custom plugins in a seamless manner to protect data at rest in your site. And best of all, even though it delivers enterprise-grade key management, you can try it for 2 weeks free! Learn more at http://www.lockr.io.

= Lockr Features: =
* Easy to configure and setup in WordPress
* Simple UI to override any option stored by plugins
* Safe and Secure offsite key storage
* Works with any API and encryption key
* Built-in AES-256 Encryption functions to secure data in your site
* 99.9% uptime guarantee (SLA Available for Enterprise Customers)
* Regular Backups
* Multiple Region Redundancy
* Backed by Townsend Security's FIPS 140-2 compliant key manager, your keys are secured to industry standards.

= Lockr is the first key management service for WordPress. =
More and more plugins are leveraging 3rd party APIs. To securely access these APIs, a token, secret key, or password is necessary. Until now, these highly sensitive secrets were stored right in your database. We’ve seen a major need to secure sensitive data and communications by removing these API keys from your database, encrypting them, and storing safely in an offsite key vault. This limits the damage that could be done if your site is compromised or a developer has a local copy of your database. Lockr makes key management easy. Just install the plugin for WordPress, configure your account and begin securely storing your keys. Lockr provides patches for the major plugins used by hundreds of thousands of sites and with WP-CLI a single command will make sure your plugins use Lockr.

= Who is Lockr for? =
Lockr is available for WordPress sites of all sizes. Easy to use for the novice site owner and advanced enough for the expert developer, Lockr secures web transactions and data at rest by protecting API and encryption keys.
For Site Builders: fill out a single registration form and you’re set. To use with other plugins, look for those that have Lockr available or use our patch library to update your favorite plugin to use Lockr.
For Developers: Lockr provides an easy to use framework to “get and set” keys from your custom plugin. Additionally, Lockr provides a simple to use yet strong AES-256 encryption function, ensuring your data is encrypted according to industry best-practices and securely stored. Using Lockr helps keep the developer safe, by removing the sensitive passwords and key secrets from the code and database, following security best practices should a site be compromised.

= Is Lockr Safe? =
Lockr can secure any API key, secret key, and other types of credentials. Once enabled in WordPress, keys entered are encrypted, then sent over to the Lockr system and removed from the code repository and database. This encryption teamed with hosting provider based authentication prevents your key from being used outside your website. Lockr also manages keys on a “per environment" basis which helps eliminate the potential of keys being shared from production to development environments. No longer will you have to worry about sending a notification from development to your production users, or having production data decrypted in development environments.

Leveraging proven enterprise-grade key management technology from Townsend Security, Lockr's offsite key management delivers best-practice security to protect against critical vulnerabilities and help sites meet PCI DSS, HIPAA and other security requirements and regulations.

This plugin is designed, written and maintained by experts in security, to the end user it is easy to use and understand. Let Lockr handle the difficult part of securing your site, so you can focus on delivering the best experience possible to your users.

== Installation ==

Installation of Lockr is simple, and if you are on a supported hosting partner, it is done seamlessly and within seconds.

1. Upload the Lockr directory to the `/wp-content/plugins/plugin-name` directory, or install the plugin through the WordPress plugins screen directly.
2. Activate Lockr through the 'Plugins' screen in your WordPress
3. Visit Settings > Lockr
4. Follow the prompts to connect your site to a KeyRing. This will open up a popup window where current users can login, or new users can register for an account.
5. You can create a new KeyRing or connect your site to an existing KeyRing to share secrets with another application on your account.
6. Once the dashboard shows you as having a certificate and registration you're done!
7. When ready to deploy to production, follow the prompts provided which will remove the development certificate and place a production one in its place. With production you're in our guaranteed uptime environment.

You're set! Start entering your keys through the Lockr config or creating option overrides to integrate with other plugins! To get the values out of Lockr all you will need is the function lockr_get_key([machine_name_of_key]) where you put in the machine name of the key you have set.

Be sure to check out our [docs](https://docs.lockr.io) for more details.


== Frequently Asked Questions ==

= How is my key stored? =

Before transmitting your key to Lockr, it is encrypted and verified with a HMAC. It is then sent via a secure connection to the Lockr server where it is held in a FIPS 140-2 compliant key manager. By encrypting it before it leaves the site, Lockr has no way of knowing or accessing your key, and with the HMAC you can be sure no one has possibly interfered with the key in transit. Only your site can unlock the key for it to be used.

= Will this slow down my site? =

Not to any noticeable effect. The connection to the Lockr server depends on the speed of your servers connection but on average we see round trips of under 200ms. This is about the same time that some database queries take.

= What is the uptime guarantee of Lockr =

We know your keys are critical to your site. To ensure you have your keys whenever you need it our cloud is built to scale, and we back that with a 99.9% uptime guarantee. A dedicated SLA is available for enterprise clients.

== Screenshots ==


== Changelog ==

= 3.0 =
**Welcome to Lockr v2!**

Welcome to the new Lockr 2.0! We’ve completely re-architected the service from the ground up and as such the modules got an overhaul at the same time.

**Re Architected from the ground up**
The latest version of Lockr does not sit on top of the previous version, but rather incorporates all that we have learned since the first release. It takes full advantage of the latest technical improvements to speed, performance and security.

**FASTER**
We’ve always been committed to creating a fast lookup time for any secret in Lockr. With 2.0, secret retrievals are blazing fast, now in the sub-100 millisecond range. Go ahead and utilize Lockr with the peace of mind knowing we won’t be slowing you down.

**Lockr KeyRings**
Secrets are no longer organized by site, but rather by a new logical grouping we call KeyRings. These KeyRings are easier to create (now done in a convenient popup) and it’s even easier now to create clients (connections) on multiple environments which all connect to the same KeyRing. This means you can deploy Lockr to your development, staging, and local environments with ease and without the risk of creating multiple subscriptions.

**Cloud Independent**
Lockr infrastructure has now freed itself of cloud host-proprietary capabilities. This allows Lockr to be deployed across multiple various cloud providers to increase performance by offering more points of presence. Be on the lookout for more of these as they come online and if you have a location you’d like to see Lockr located, just drop us a line.

**Cache Mesh Network**
Lockr has improved the speed of key retrieval through a patent-pending mesh network of cache Hardware Security Modules (HSMs). These caches will automatically distribute and hold the values you store closer to where your site is located. The result is a significant improvement in performance.

Want more info? Check out our [blog post](https://www.lockr.io/blog/any-key-anywhere-2/) where we go into more details.


= 2.4 =
**Big update!**

With this update we've added automatic registration! This means that when you enable the plugin it will automatically set you up with the certificates for a development account. We didn't stop there, we've setup a few of the largest WordPress hosts with a customized experience for you if you're on one of the hosts.

The hosts we now support with automatic registration are:

* Pantheon
* Kinsta
* GoDaddy
* WP Engine
* Flywheel
* Siteground

But wait... there's more! We've added 2 ways to keep your data in WordPress even more secure.

With our roots in encryption we wanted to make sure WordPress users every opportunity to stay secure. We've also added the ability to encrypt posts. Simply check the box in the Lockr configuration and any post you password protect will also be encrypted in the database. Now you can keep those private posts encrypted and not able to be read from the database. With this reliance on password protection, we also wanted to make the password handling as secure as possible as well. So we added the ability to hash passwords stored in the database, keeping your passwords as secure as they can be.

Of course we wouldn't launch this without full Gutenberg compatibility! All the encryption works great, no matter what editor you use!

= 2.3 =
With our new pricing, and free trial period, we've added a row to the status table to show when the trial period ends. We've also fixed a bug that caused some intermittent issues on Pantheon.

= 2.2 =
* Any Secret Anywhere! - This is BIG. No more custom integrations, no more custom code necessary to use Lockr with the plugins already in your site. Lockr now seamlessly integrates with any plugin on your site like WooCommerce, Give, Mailchimp, Stripe etc. to keep your keys and passwords secure in Lockr’s FIPS 140-2 compliant key managers. Using Lockr’s new options override UI, you can now simply select any secret from any plugin that utilizes the option table (almost everyone does), even if that secret is buried in a serialized array. Within a few clicks you're on your way and your secrets are removed the options table, encrypted and stored in Lockr. With a completely seamless integration, keys remain available for settings forms but that secret (key, password, etc) is no longer in your database. Feel free to update the settings via the forms and our update hooks will ensure Lockr keeps up to date on the latest values. Decide you want to delete the plugin? Upon deleting the option from the options table, Lockr will also delete it so you're only storing the keys you need. As always, if you want a strong encryption key to override the one your plugin was storing in the database, just check the box on the form and we'll create a new, random 256-bit key to keep your data secure.

= 2.1 =
* Any site anywhere - Lockr now automates all of the setup and authentication, no matter where your site is located. Simply fill out the form when you first activate the plugin and we will create secure authentication credentials for you and place them into a secure folder on your server. Now setup for Lockr can be done in just a few clicks!
* Easy Encryption - When Lockr first starts up, it will create for you a highly secure 256-bit encryption key. With this, lockr_encrypt() and lockr_decrypt() work out of the box without any additional setup.
* HMAC signed keys - Not only are keys encrypted before being sent to Lockr, they now are signed with an HMAC so the integrity of the key is ensured during transit and storage. TL/DR - We made your keys even safer when they're stored in Lockr.
* Status dashboard - Now with a quick glance you can see the status of all the major components of the Lockr setup and registration process. Green means go and red means take a deeper look.
* EU Support - Out of beta, this release also allows for keys to be stored in our isolated and dedicated EU environment.
* Support for GiveWP - Last but not least, we now support all major plugins for GiveWP! With this your donations are secured even more with the api keys for all the additional services (from payment gateways to email marketing) are now automatically stored in Lockr. Simply go to the settings page and save the settings form again. Afterwards, you'll see the keys show up in your key list and Give will keep working as expected.

= 2.0.1 =
* Mailchimp For WordPress Support!
With over 600,000 active installs, Mailchimp is the first plugin Lockr supports out of the box. Upon installing Lockr (or this update), it will automatically secure the API key in Mailchimp for WordPress plugin. No extra work necessary! Stay tuned for more supported plugins!

= 2.0 =
* Hello WordPress! Lockr is happy to be a part of the community and officially in the plugin directory.
* To celebrate our release we have provided a function to encrypt/decrypt data based on a key stored in Lockr. Simply use lockr_encrypt() and lockr_decrypt() to secure your data. More features around encryption are planned for future releases
