=== WP SES ===
Contributors: deliciousbrains, bradt, SylvainDeaure
Tags: email,ses,amazon,webservice,deliverability,newsletter,autoresponder,mail,wp_mail,smtp,service
Requires at least: 3.0.0
Tested up to: 4.9.1
Requires PHP: 5.5+
Stable tag: trunk

WP SES sends all outgoing WordPress emails through Amazon Simple Email Service (SES) for maximum email deliverability

== Description ==

WP SES sends all outgoing WordPress emails through Amazon Simple Email Service (SES) instead of local wp_mail function.
This ensures high email deliverability, email traffic statistics and a powerful managed infrastructure.

Multisite features are so far experimental.

Current features are:

* Ability to adjust WordPress default sender email and name
* Validation of Amazon API credentials
* Request confirmation for sender emails
* Test message within Amazon Sandbox mode
* Full integration as seamless replacement for wp_mail internal function
* Dashboard panel with quota and statistics
* Ability to customize return path for delivery failure notifications
* Custom Reply-To and From headers
* Default config values for centralized multisite setups
* SES region selection
* Emails with attachments are supported (Compatible with Contact Form 7)
* File logging feature (may be verbose and insecure, do not use as is in production for a long period of time)
* English, French, Spanish, Serbo-Croatian translations (fell free to send your mo/po files to support more languages)

**PRO UPGRADE - Coming Soon**

The WP SES plugin has been acquired by [Delicious Brains Inc](https://deliciousbrains.com/?utm_campaign=WP%2BOffload%2BSES&utm_source=wordpress.org&utm_medium=free%2Bplugin%2Blisting). Big improvements are on the way, but for now we've just tidied up the UI a bit.

We're working on a pro upgrade that will have the following features:

* Track email opens and link clicks
* Track email addresses that have bounced or received complaints
* Log sent emails and failures
* Queue email to handle rate limits and retry failures
* Customizing email templates
* Step-by-step setup wizard encouraging best practices
* Email support

We've learned a ton iterating on [our Amazon S3 plugin](https://deliciousbrains.com/wp-offload-s3/?utm_campaign=WP%2BOffload%2BSES&utm_source=wordpress.org&utm_medium=free%2Bplugin%2Blisting) over the last couple of years and will be applying all that knowlege and experience in building this plugin.

Get notified when the pro upgrade launches by [subscribing to the email list](https://deliciousbrains.com/email-subscribe/7/?utm_campaign=WP%2BOffload%2BSES&utm_source=wordpress.org&utm_medium=free%2Bplugin%2Blisting).

== Installation ==

= Basic Setup =

1. Install and activate the plugin
1. Go to Settings > WP SES in the WordPress dashboard menu
1. Fill the email address and name to use as the sender for all emails

= Amazon API Keys and Permissions =

1. Sign up for AWS if you haven't already
1. Visit the [IAM console](https://console.aws.amazon.com/iam/)
1. Create a new IAM user or grant an existing user the following permissions: ListIdentities, SendEmail, SendRawEmail, VerifyEmailIdentity, DeleteIdentity, GetSendQuota, GetSendStatistics
1. In the plugin settings, fill in the Amazon API Keys

IAM User Inline Policy:

	{
	    "Version": "2012-10-17",
	    "Statement": [
	        {
	            "Sid": "Stmt1461433902000",
	            "Effect": "Allow",
	            "Action": [
	                "ses:DeleteIdentity",
	                "ses:GetSendQuota",
	                "ses:GetSendStatistics",
	                "ses:ListIdentities",
	                "ses:SendEmail",
	                "ses:SendRawEmail",
	                "ses:VerifyEmailIdentity"
	            ],
	            "Resource": [
	                "*"
	            ]
	        }
	    ]
	}

= Verify Sender Email Address =

Amazon SES requires that the sender of emails sent through its service confirm that they are the owners of the sender email address.

Email verification is per region, so **if you change regions, you will need to confirm your sender email address again.**

1. Visit the [Amazon SES console](https://console.aws.amazon.com/ses/)
1. Verify the sender email address
1. Confirm by visiting the link you get by email from Amazon SES

= Sending a Test Email =

If this is the first time you're using Amazon SES, your account will be in sandbox mode, which means you can only send emails to the verified sender email address.

1. In the plugin settings, make sure to save changes (important!)
1. Scroll down and click the Send Test Email button

= Turning It On =

Once you get the test email working, you can send AWS a request to increase the sending limit and put your account in production mode, allowing you to send emails to anyone.

1. Visit the [Sending Statistics screen](https://console.aws.amazon.com/ses/#dashboard:) in the Amazon SES console
1. Click the *Request a Sending Limit Increase* button, fill out the form and submit
1. Once AWS grants your request, click the *Turn On* button in the plugin settings

== Frequently Asked Questions ==

= Where can I get support for the plugin? =

Please use [the plugin's support forum](https://wordpress.org/support/plugin/wp-ses).

= What are the minimum requirements? =

* WordPress 3.0+
* PHP 5 and cURL PHP extension
* MySQL 5.0+
* Apache 2+ or Nginx 1.4+
* Amazon Web Services account

= Can you help me about... (an Amazon concern) =

We are not otherwise linked to Amazon or Amazon Web Services.
Please direct your specific Amazon questions to Amazon support.

= How can I setup default values for a multisite install? =

Please test using the UI first, then if all works as expected, try using the following constants.

Edit the wp-config.php file and add any of the following constants:

	// AWS Access Key ID
	define('WP_SES_ACCESS_KEY','blablablakey');

	// AWS Secret Access Key
	define('WP_SES_SECRET_KEY','blablablasecret');

	// From mail (optional) must be an Amazon SES validated email
	// hard coded email, leave empty or comment out to allow custom setting via panel
	define('WP_SES_FROM','me@....');

	// Return path for bounced emails (optional)
	// hard coded email, leave empty or comment out to allow custom setting via panel
	define('WP_SES_RETURNPATH','return@....');

	// ReplyTo (optional) - This will get the replies from the recipients.
	// hard coded email, or 'headers' for using the 'replyto' from the headers.
	// Leave empty or comment out to allow custom setting via panel
	define('WP_SES_REPLYTO','headers');

	// Hide list of verified emails (optional)
	define('WP_SES_HIDE_VERIFIED',true);

	// Hide SES Stats panel (optional)
	define('WP_SES_HIDE_STATS',true);

	// Auto activate the plugin for all sites (optional)
	define('WP_SES_AUTOACTIVATE',true);

	When using defines to hardcode your setting, don't forget to define the SES endpoints, too :

	define('WP_SES_ENDPOINT', 'email.us-east-1.amazonaws.com');
	OR
	define('WP_SES_ENDPOINT', 'email.us-west-2.amazonaws.com');
	OR
	define('WP_SES_ENDPOINT', 'email.eu-west-1.amazonaws.com');

= How to do other actions on mail sent? =

I was asked to add a hook once mail is sent.
Could be used to log emails, or post email info to an API or database.

wpses_mailsent hook is available for that use.

In your code, define a callback function :

function myMailSentHook($to, $subject, $message, $headers, $attachments ) { ... }
// params are the same as the wp_mail() function.

// Then add your action :
add_action('wpses_mailsent','myMailSentHook',10,5);

= Can I create a translation for your plugin? =

Yes, please do! It's easy.

1. Visit [translate.wordpress.org](https://translate.wordpress.org)
1. Choose your locale
1. Click on *Plugins*
1. Search for *wp ses*
1. Click on WP SES
1. Translate!

== Screenshots ==

1. Settings screen

== Changelog ==

= 0.8.1 - 2018-06-06 =
* Added dismissable admin notice that WP SES will soon require PHP 5.5+

= 0.8 - 2017-12-28 =
* WP SES has been acquired by [Delicious Brains Inc](https://deliciousbrains.com/?utm_campaign=WP%2BOffload%2BSES&utm_source=wordpress.org&utm_medium=free%2Bplugin%2Blisting) (big improvements on the way including a pro upgrade)
* Refreshed UI to be consistent with the latest WordPress styles
* Updated in-plugin instructions to be much clearer
* Updated set up documentation
* Now works with translate.wordpress.org

= 0.7.2.1 =
* Fix for stats report, thanks to @Ange1Rob0t

= 0.7.2 =
* Fix for use as "must use plugin" in a wpmu setup, thanks to @positonic

= 0.7.1 =
* fix deprecated get_currentuserinfo()

= 0.7.0 =
* PHP 7.0 Compatibility

= 0.4.8 =
* Experimental support for cc: and Bcc: in custom header
* Domain verification is ok

= 0.4.0 =
* Serbo-Croatian Translation by https://webhostinggeeks.com/
* Fixed Reply-to: extraction Regexp
* fixes from hbradleyiii https://wordpress.org/support/topic/bug-with-force-plugin-activation-option
* better handling of custom headers
* removed ListVerifiedEmailAddresses deprecated api call, now using ListIdentities.
* added wpses_mailsent hook
* several minor fixes.

= 0.3.58 =
* Tries to always auto-activate in answer to https://wordpress.org/support/topic/the-plugin-get-inactive-after-a-few-minutes
* small fixes

= 0.3.56 =
* fixed sender name format
* fixed regexp for some header recognition
* now supports comma separated emails in to: header

= 0.3.54 =
* bad ses lib include fixed
* Added "force plugin activation" for some use case with IAM credentials

= 0.3.52 =
* Warning if Curl not installed
* Attachments support for use with Contact Form (finally !)
* Notice fixed

= 0.3.50 =
* Notice fixed, setup documentation slightly tweaked

= 0.3.48 =
* Experimental "WP Better Email" Plugin compatibility

= 0.3.46 =
* Maintenance release - fixes some notices and old code.

= 0.3.45 =
* Maintenance release - fixes some notices.

= 0.3.44 =
* Added Amazon SES Endpoint selection. EU users can now select EU region.

= 0.3.42 =
* Added Spanish translation, thanks to Andrew of webhostinghub.com

= 0.3.4 =
* Auto activation via WP_SES_AUTOACTIVATE define, see FAQ.

= 0.3.2 =
* Tweaked header parsing thanks to bhansson

= 0.3.1 =
* Added Reply-To
* Added global WPMU setup (To be fully tested)

= 0.2.9 =
* Updated SES access class
* WP 3.5.1 compatibility
* Stats sorting
* Allow Removal of verified e-mail address
* Added wp_mail filter
* "Forgotten password" link is now ok.
* Various bugfixes

= 0.2.2 =
Reference Language is now English.
WP SES est fourni avec les textes en Francais.

= 0.2.1 =
Added some functions

* SES Quota display
* SES Statistics
* Can set email return_path
* Full email test form
* Can partially de-activate plugin for intensive testing.

= 0.1.2 =
First public Beta release

* Functionnal version
* Internationnal Version
* fr_FR and en_US locales

= 0.1 =
* Proof of concept

== Upgrade Notice ==

= 0.4.8 =
Domain verification is ok

= 0.4.2 =
Experimental support for cc: and Bcc: in custom header

= 0.4.0 =
Removed deprecated SES call, several bugfixes, added sr_RS translation.

= 0.2.9 =
Pre-release, mainly bugfixes, before another update.

= 0.2.2 =
All default strings are now in english.

= 0.2.1 =
Quota and statistics Integration

= 0.1.2 =
First public Beta release


