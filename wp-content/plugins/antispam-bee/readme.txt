# Antispam Bee #
* Contributors:      pluginkollektiv
* Tags:              comment, spam, antispam, comments, trackback, protection, prevention
* Donate link:       https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=LG5VC9KXMAYXJ
* Requires at least: 3.8
* Tested up to:      4.3
* Stable tag:        2.6.8
* License:           GPLv2 or later
* License URI:       https://www.gnu.org/licenses/gpl-2.0.html

“…another popular solution to fight spam is Antispam Bee”—Matt Mullenweg, Q&A WordCamp Europe 2014

## Description ##
Say Goodbye to comment spam on your WorddPress blog or website. *Antispam Bee* blocks spam comments and trackbacks effectively and without captchas. It is free of charge, ad-free and compliant with European data privacy standards.


### Feature/Settings Overview ###
* Trust approved commenters.
* Trust commenters with a Gravatar.
* Consider the comment time.
* Treat BBCode as spam.
* Validate the IP address of commenters.
* Use regular expressions.
* Search local spam database for commenters previously marked as spammers.
* Match against a public anti-spam database.
* Notify admins by e-mail about incoming spam.
* Delete existing spam after n days.
* Limit approval to comments/pings (will delete other comment types).
* Select spam indicators to send comments to deletion directly.
* Optionally exclude trackbacks and pingbacks from spam detection.
* Optionally spam-check comment forms on archive pages.
* Display spam statistics on the dashboard, including daily updates of spam detection rate and a total of blocked spam comments.


> #### Auf Deutsch? ####
> Für eine ausführliche [Dokumentation](https://github.com/pluginkollektiv/antispam-bee/wiki/Dokumentation) besuche bitte das [Antispam-Bee-Wiki](https://github.com/pluginkollektiv/antispam-bee/wiki). Dort findest du u.a. Antworten auf [häufig gestellte Fragen](https://github.com/pluginkollektiv/antispam-bee/wiki/H%C3%A4ufige-Fragen), sowie Hinweise zu den [Einstellungen der Antispam-Regeln](https://github.com/pluginkollektiv/antispam-bee/wiki/Dokumentation#antispam-regeln).
>
> **Community-Support auf Deutsch** erhältst du in einem der [deutschsprachigen Foren](https://de.forums.wordpress.org/forum/plugins); im [Plugin-Forum für Antispam Bee](https://wordpress.org/support/plugin/antispam-bee) wird, wie in allen Plugin-Foren auf wordpress.org, ausschließlich **Englisch** gesprochen.


### Credits ###
* Author: [Sergej Müller](https://sergejmueller.github.io/)
* Maintainers: [pluginkollektiv](http://pluginkollektiv.org)


## Installation ##
* If you don’t know how to install a plugin for WordPress, [here’s how](http://codex.wordpress.org/Managing_Plugins#Installing_Plugins).


### Requirements ###
* PHP 5.2.4 or greater
* WordPress 3.8 or greater


### Settings ###
After you have activated *Antispam Bee* the plugin will block spam comments out of the box. However, you may want to visit *Settings → Antispam Bee* to configure your custom set of anti-spam options that works best for your site.


### Privacy Notice ###
On sites operating from within the EU the option *Use a public antispam database* should not be activated for privacy reasons. When that option has been activated, *Antispam Bee* will match full IP addresses from comments against a public spam database. Technically it is not possible to encrypt those IPs, because spam databases only store and operate with complete, unencrypted IP addresses.


## Frequently Asked Questions ##
### Does Antispam Bee work with Jetpack, Disqus Comments and other comment plugins? ###
Antispam Bee works best with default WordPress comments. It is not compatible with Jetpack or Disqus Comments as those plugins load the comment form within an iframe. Thus Antispam Bee can not access the comment form directly.
It also won’t work with any AJAX-powered comment forms.

### On how many web sites or blogs can I use Antispam Bee? ###
On as many as you wish. There is no limitation to the number of sites you use the plugin on.

### Do I have to register for any sort of paid service if my site gets a lot of comment spam? ###
No, Antispam Bee is free forever, for both private and commercial projects.

### Does Antispam Bee store any private user data, IP addresses or the like? ###
Nope. Antispam Bee is developed in Germany and Switzerland. You might have heard we can be a bit nitpicky over here when it comes to privacy.

### Will I have to edit any theme templates to get Antispam Bee to work? ###
No, the plugin works as is. You may want to configure your favorite settings, though.


## Changelog ##
### 2.6.8 ###
* added a POT file
* updated German translation, added formal version
* updated plugin text domain to include a dash instead of an underscore
* updated, translated + formatted README.md
* updated expired link URLs in plugin and languages files
* updated [plugin authors](https://gist.github.com/glueckpress/f058c0ab973d45a72720)

### 2.6.7 ###
* **English**
    * Removal of functions *Block comments from specific countries* and *Allow comments only in certain language* for financial reasons
* **Deutsch**
    * Entfernung der Funktionen *Kommentare nur in einer Sprache zulassen* und *Bestimmte Länder blockieren bzw. erlauben* aus finanziellen Gründen
    * [Weitere Informationen zum Hintergrund](https://plus.google.com/u/0/+SergejMüller/posts/ZyquhoYjUyF)

### 2.6.6 ###
* **English**
    * Switch to the official Google Translation API
    * *Release time investment (Development & QA): 2.5 h*
* **Deutsch**
    * (Testweise) Umstellung auf die offizielle Google Translation API
    * [Weitere Informationen zum Hintergrund](https://plus.google.com/u/0/+SergejMüller/posts/ZyquhoYjUyF)
    * *Release-Zeitaufwand (Development & QA): 2,5 Stunden*

### 2.6.5 ###
* **English**
    * Fix: Return parameters on `dashboard_glance_items` callback / thx [@toscho](https://twitter.com/toscho)
    * New function: Trust commenters with a Gravatar / thx [@glueckpress](https://twitter.com/glueckpress)
    * Additional plausibility checks and filters
    * *Release time investment (Development & QA): 12 h*
* **Deutsch**
    * Fix: Parameter-Rückgabe bei `dashboard_glance_items` / thx [@toscho](https://twitter.com/toscho)
    * Neue Funktion: [Kommentatoren mit Gravatar vertrauen](https://github.com/pluginkollektiv/antispam-bee/wiki/Dokumentation) / thx [@glueckpress](https://twitter.com/glueckpress)
    * Zusätzliche Plausibilitätsprüfungen und Filter
    * *Release-Zeitaufwand (Development & QA): 12 Stunden*

### 2.6.4 ###
* **English**
    * Consideration of the comment time (Spam if a comment was written in less than 5 seconds)
    * *Release time investment (Development & QA): 6.25 h*
* **Deutsch**
    * Berücksichtigung der Kommentarzeit (Spam, wenn ein Kommentar in unter 5 Sekunden verfasst)
    * [Mehr Informationen auf Google+](https://plus.google.com/+SergejMüller/posts/73EbP6F1BgC)
    * *Release-Zeitaufwand (Development & QA): 6,25 Stunden*

### 2.6.3 ###
* **English**
    * Sorting for the Antispam Bee column in the spam comments overview
    * Code refactoring around the use of REQUEST_URI
    * *Release time investment (Development & QA): 2.75 h*
* **Deutsch**
    * Sortierung für die Antispam Bee Spalte in der Spam-Übersicht
    * Code-Refactoring rund um die Nutzung von REQUEST_URI
    * *Release-Zeitaufwand (Development & QA): 2,.75 Stunden*

### 2.6.2 ###
* **English**
    * Improving detection of fake IPs
    * *Release time investment (Development & QA): 11 h*
* **Deutsch**
    * Überarbeitung der Erkennung von gefälschten IPs
    * *Release-Zeitaufwand (Development & QA): 11 Stunden*

### 2.6.1 ###
* **English**
    * Code refactoring of options management
    * Support for `HTTP_FORWARDED_FOR` header
    * *Release time investment (Development & QA): 8.5 h*
* **Deutsch**
    * Überarbeitung der Optionen-Verwaltung
    * Berücksichtigung der Header `HTTP_FORWARDED_FOR`
    * *Release-Zeitaufwand (Development & QA): 8,5 Stunden*

### 2.6.0 ###
* DE: Optimierungen für WordPress 3.8
* DE: Zusatzprüfung auf Nicht-UTF-8-Zeichen in Kommentardaten
* DE: Spamgrund als Spalte in der Übersicht mit Spamkommentaren
* EN: Optimizations for WordPress 3.8
* EN: Clear invalid UTF-8 characters in comment fields
* EN: Spam reason as a column in the table with spam comments

### 2.5.9 ###
* DE: Anpassung des Dashboard-Skriptes für die Zusammenarbeit mit [Statify](http://statify.de)
* EN: Dashboard widget changes to work with [Statify](http://statify.de)

### 2.5.8 ###
* DE: Umstellung von TornevallDNSBL zu [Stop Forum Spam](http://www.stopforumspam.com)
* DE: Neue JS-Bibliothek für das Dashboard-Widget
* DE: [Mehr Informationen auf Google+](https://plus.google.com/110569673423509816572/posts/VCFr3fDAYDs)
* EN: Switch from TornevallDNSBL to [Stop Forum Spam](http://www.stopforumspam.com)
* EN: New JS library for the Antispam Bee dashboard chart

### 2.5.7 ###
* DE: Optionale Spam-Logdatei z.B. für [Fail2Ban](https://github.com/sergejmueller/sergejmueller.github.io/wiki/Fail2Ban:-IP-Blacklist)
* DE: Filter `antispam_bee_notification_subject` für eigenen Betreff in Benachrichtigungen
* DE: Detaillierte Informationen zum Update auf [Google+](https://plus.google.com/110569673423509816572/posts/iCfip2ggYt9)
* EN: Optional logfile with spam entries e.g. for [Fail2Ban](https://gist.github.com/sergejmueller/5622883)
* EN: Filter `antispam_bee_notification_subject` for a custom subject in notifications

### 2.5.6 ###
* DE: Neue Erkennungsmuster für Spam hinzugefügt / [Google+](https://plus.google.com/110569673423509816572/posts/9BSURheN3as)
* EN: Added new detection/patterns for spam comments

### 2.5.5 ###
* Deutsch: Erkennung und Ausfilterung von Spam-Kommentaren, die versuchen, [Sicherheitslücken von W3 Total Cache und WP Super Cache](http://blog.sucuri.net/2013/05/w3-total-cache-and-wp-super-cache-vulnerability-being-targeted-in-the-wild.html) auszunutzen. [Ausführlicher auf Google+](https://plus.google.com/110569673423509816572/posts/afWWQbUh4at).
* English: Detection and filtering of spam comments that try to exploit the latest [W3 Total Cache and WP Super Cache Vulnerability](http://blog.sucuri.net/2013/05/w3-total-cache-and-wp-super-cache-vulnerability-being-targeted-in-the-wild.html).

### 2.5.4 ###
* Jubiläumsausgabe: [Details zum Update](https://plus.google.com/110569673423509816572/posts/3dq9Re5vTY5)
* Neues Maskottchen für Antispam Bee
* Erweiterte Prüfung eingehender Kommentare in lokaler Blog-Spamdatenbank auf IP, URL und E-Mail-Adresse

### 2.5.3 ###
* Optimierung des Regulären Ausdrucks

### 2.5.2 ###
* Neu: [Reguläre Ausdrücke anwenden](https://github.com/pluginkollektiv/antispam-bee/wiki/Dokumentation) mit vordefinierten und eigenen Erkennungsmustern
* Änderung der Filter-Reihenfolge
* Verbesserungen an der Sprachdatei
* [Hintergrundinformationen zum Update](https://plus.google.com/110569673423509816572/posts/CwtbSoMkGrT)

### 2.5.1 ###
* [BBCode im Kommentar als Spamgrund](https://github.com/pluginkollektiv/antispam-bee/wiki/Dokumentation)
* IP-Anonymisierung bei der Länderprüfung
* [Mehr Transparenz](https://plus.google.com/110569673423509816572/posts/ZMU6RfyRK29) durch hinzugefügte Datenschutzhinweise
* PHP 5.2.4 als Voraussetzung (ist zugleich die Voraussetzung für WP 3.4)

### 2.5.0 ###
* [Edition 2012](https://plus.google.com/110569673423509816572/posts/6JUC6PHXd6A)

### 2.4.6 ###
* Russische Übersetzung
* Veränderung der Secret-Zeichenfolge

### 2.4.5 ###
* Überarbeitetes Layout der Einstellungen
* Streichung von Project Honey Pot
* TornevallNET als neuer DNSBL-Dienst
* WordPress 3.4 als Mindestvoraussetzung
* WordPress 3.5 Unterstützung
* Online-Handbuch in Neufassung

### 2.4.4 ###
* Technical and visual support for WordPress 3.5
* Modification of the file structure: from `xyz.dev.css` to `xyz.min.css`
* Retina screenshot

### 2.4.3 ###
* Check for basic requirements
* Remove the sidebar plugin icon
* Set the Google API calls to SSL
* Compatibility with WordPress 3.4
* Add retina plugin icon on options
* Depending on WordPress settings: anonymous comments allowed

### 2.4.2 ###
* New geo ip location service (without the api key)
* Code cleanup: Replacement of `@` characters by a function
* JS-Fallback for missing jQuery UI

### 2.4.1 ###
* Add russian translation
* Fix for the textarea replace
* Detect and hide admin notices

### 2.4 ###
* Support for IPv6
* Source code revision
* Delete spam by reason
* Changing the user interface
* Requirements: PHP 5.1.2 and WordPress 3.3

### 2.3 ###
* Xmas Edition

### 2.2 ###
* Interactive Dashboard Stats

### 2.1 ###
* Remove Google Translate API support

### 2.0 ###
* Allow comments only in certain language (English/German)
* Consider comments which are already marked as spam
* Dashboard Stats: Change from canvas to image format
* System requirements: WordPress 2.8
* Removal of the migration script
* Increase plugin security

### 1.9 ###
* Dashboard History Stats (HTML5 Canvas)

### 1.8 ###
* Support for the new IPInfoDB API (including API Key)

### 1.7 ###
* Black and whitelisting for specific countries
* "Project Honey Pot" as a optional spammer source
* Spam reason in the notification email
* Visual refresh of the notification email
* Advanced GUI changes + Fold-out options

### 1.6 ###
* Support for WordPress 3.0
* System requirements: WordPress 2.7
* Code optimization

### 1.5 ###
* Compatibility with WPtouch
* Add support for do_action
* Translation to Portuguese of Brazil

### 1.4 ###
* Enable stricter inspection for incomming comments
* Do not check if the author has already commented and approved

### 1.3 ###
* New code structure
* Email notifications about new spam comments
* Novel Algorithm: Advanced spam checking

### 1.2 ###
* Antispam Bee spam counter on dashboard

### 1.1 ###
* Adds support for WordPress new changelog readme.txt standard
* Various changes for more speed, usability and security

### 1.0 ###
* Adds WordPress 2.8 support

### 0.9 ###
* Mark as spam only comments or only pings

### 0.8 ###
* Optical adjustments of the settings page
* Translation for Simplified Chinese, Spanish and Catalan

### 0.7 ###
* Spam folder cleanup after X days
* Optional hide the &quot;MARKED AS SPAM&quot; note
* Language support for Italian and Turkish

### 0.6 ###
* Language support for English, German, Russian

### 0.5 ###
* Workaround for empty comments

### 0.4 ###
* Option for trackback and pingback protection

### 0.3 ###
* Trackback and Pingback spam protection


## Screenshots ##
1. Antispam Bee Optionen
