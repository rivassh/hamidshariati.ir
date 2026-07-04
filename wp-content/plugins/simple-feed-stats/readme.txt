=== Simple Statistics for Feeds ===

Plugin Name: Simple Statistics for Feeds
Plugin URI: https://perishablepress.com/simple-feed-stats/
Description: Tracks your feeds, adds custom content, and displays your feed statistics on your site.
Tags: rss, feed, stats, statistics, subscribers
Author: Jeff Starr
Author URI: https://plugin-planet.com/
Donate link: https://monzillamedia.com/donate.html
Contributors: specialk
Requires at least: 4.6
Tested up to: 6.6
Stable tag: 20240625
Version:    20240625
Requires PHP: 5.6.20
Text Domain: simple-feed-stats
Domain Path: /languages
License: GPL v2 or later

Tracks your feeds and displays your feed count via shortcode.



== Description ==

[Simple Statistics for Feeds](https://perishablepress.com/simple-feed-stats/) (SFS) tracks your feeds automatically using a variety of methods, and provides a wealth of tools and options for further configuration and management. Also displays your subscriber count via template tag or shortcode. Fully configurable. Visit the "Simple Statistics for Feeds" settings page for stats, tools, and more info.

> Check out a feed-count demo in the sidebar @ [WP-Mix.com](https://wp-mix.com/)

**Fast & Free**

* Stop paying 3rd-party fees, track your own stats for free!
* Display a "feed count" badge like the once-awesome Feedburner
* Built with the WP API for optimal performance & security
* Fully compatible with WordPress Multisite
* Regularly updated and "future proof"

**Core Features**

* __Daily & Total__ - collect daily stats and total stats for each feed
* __Beautiful Stats__ - view all your feed stats via the plugin settings
* __Dashboard Widget__ - get a quick overview of your feed statistics
* __Custom Feed Count__ - display any number for your feed count
* __Custom Content__ - embellish your feed with graphics and markup
* __Custom CSS__ - use your own styles to customize your feed stats

**More Features**

* Shortcodes & Template Tags to display your feed count anywhere
* Display your daily feed counts for post feeds and comments feeds
* Options to clear cache, reset stats, and restore default settings
* Automatically track custom feeds generated via the WordPress API
* Enable "Strict Mode" reporting for more accurate feed counts
* Track feeds using custom key/value tracking parameters
* Works with or without Gutenberg Block Editor

**Tracking Methods**

Simple Statistics for Feeds provides four ways to track your feeds:

* __Default Tracking__ - tracks feeds directly via URI request
* __Custom Tracking__ - tracks feeds via embedded post image
* __Alternate Tracking__ - tracks feeds via embedded feed image
* __Open Tracking__ - tracks feeds via your own custom image

**Collected Data**

Simple Statistics for Feeds tracks the following data for each feed request:

* Feed type
* Tracking type
* Requested URL
* User-agent
* IP address
* Referrer
* Date
* ID

You can view these data at any time by visiting the plugin settings. All feed data are displayed via beautiful, easy-to-use interface. [View screenshots&nbsp;&raquo;](https://wordpress.org/plugins/simple-feed-stats/screenshots/)

**Privacy**

This plugin records the user agent, referrer, and IP address for each feed request. IP collection may be disabled via the plugin settings. All collected data may be deleted at any time via the plugin settings. This plugin does not set any cookies, and it does not connect to any third-party locations.

Simple Statistics for Feeds (formerly Simple Feed Stats) is developed and maintained by [Jeff Starr](https://twitter.com/perishable), 15-year [WordPress developer](https://plugin-planet.com/) and [book author](https://books.perishablepress.com/).


**Support development**

I develop and maintain this free plugin with love for the WordPress community. To show support, you can [make a donation](https://monzillamedia.com/donate.html) or purchase one of my books: 

* [The Tao of WordPress](https://wp-tao.com/)
* [Digging into WordPress](https://digwp.com/)
* [.htaccess made easy](https://htaccessbook.com/)
* [WordPress Themes In Depth](https://wp-tao.com/wordpress-themes-book/)
* [Wizard's SQL Recipes for WordPress](https://books.perishablepress.com/downloads/wizards-collection-sql-recipes-wordpress/)

And/or purchase one of my premium WordPress plugins:

* [BBQ Pro](https://plugin-planet.com/bbq-pro/) - Super fast WordPress firewall
* [Blackhole Pro](https://plugin-planet.com/blackhole-pro/) - Automatically block bad bots
* [Banhammer Pro](https://plugin-planet.com/banhammer-pro/) - Monitor traffic and ban the bad guys
* [GA Google Analytics Pro](https://plugin-planet.com/ga-google-analytics-pro/) - Connect WordPress to Google Analytics
* [Simple Ajax Chat Pro](https://plugin-planet.com/simple-ajax-chat-pro/) - Unlimited chat rooms
* [USP Pro](https://plugin-planet.com/usp-pro/) - Unlimited front-end forms

Links, tweets and likes also appreciated. Thank you! :)



== Installation ==

**Installation**

1. Upload the plugin to your blog and activate
2. Visit the settings page to configure your options

[More info on installing WP plugins](https://wordpress.org/support/article/managing-plugins/#installing-plugins)


**Usage**

Everything is configured to work out of the box. Once the plugin is activated on your site, it will begin tracking how many times your feed is accessed each day. You can visit the SFS settings to customize tracking method, custom count, shortcodes and more.

To display your daily feed count or total feed count, you can use the various shortcodes and template tags. More information about the shortcodes and template tags can be found in the SFS settings and also further down on this page.


**Important**

This plugin collects feed data and stores it in the WordPress database. It is recommended to clear/remove the feed data periodically to keep database size to a minimum. The plugin makes it easy to clear/remove feed data, follow these steps:

1. Visit the SFS plugin settings
2. Scroll down to the setting, "Reset feed stats"
3. Click the link to reset your stats

After resetting the stats, you can reset the cache by clicking the "Clear cache" link, located just before "Reset feed stats".

Note: after clearing feed stats, the feed count will show as zero, 0, until new feed data is collected. While waiting for the feed count to normalize, you can display a custom count, using whatever number you prefer. Here are the steps:

1. Visit the plugin setting, "Custom count"
2. Enter the feed count number you would like to display
3. Check the box next to the setting, "Enable custom count"
4. Click the "Save Settings" button to save changes

That will display your custom feed count in shortcodes, on the dashboard, etc. To disable the custom count and restore normal feed count, simply uncheck the box for the "Enable custom count" setting. And don't forget to save changes.


**Testing**

To verify that the plugin is working properly, do the following:

1. Visit the "Your Feed Information" panel in the plugin's settings
2. Click on each of "Your feed URLs" a few times to collect some data
3. In the "Tools and Options" panel, click "clear cache"

After performing these steps, your "Current Feed Stats" and "Total Feed Stats" will display some numbers, based on the feed links that you clicked in step 2. This shows that the plugin is working normally using its default settings. Similar testing may be done for other feed-tracking methods and options.

Note that not all tracking methods (or browsers/devices) work for all types of feeds; for example, the "Alt Tracking" method is required to record hits for RDF feeds. If in doubt, roll with the default options &mdash; they are tuned for robust, everyday feed tracking.


**Shortcodes**

Display daily count for all feeds in plain-text:

	[sfs_subscriber_count]

Display daily count for all feeds with a FeedBurner-style badge:

	[sfs_count_badge]

Display daily count for all feeds with a simple (linked) badge:

	[sfs_count_simple]
	[sfs_count_simple link="https://example.com/feed/"]

Display daily count for RSS2 feeds in plain-text:

	[sfs_rss2_count]

Display daily count for comment feeds in plain-text:

	[sfs_comments_count]

See the plugin settings page for more options and infos.


**Template Tags**

Display daily count for all feeds in plain-text:

	<?php if (function_exists('sfs_display_subscriber_count')) sfs_display_subscriber_count(); ?>

Display daily count for all feeds with a FeedBurner-style badge:

	<?php if (function_exists('sfs_display_count_badge')) sfs_display_count_badge(); ?>

Display daily count for all feeds with a simple (linked) badge:

	<?php if (function_exists('sfs_display_count_simple')) sfs_display_count_simple(); ?>
	<?php if (function_exists('sfs_display_count_simple')) sfs_display_count_simple(array('link' => 'https://example.com/feed/')); ?>

Display total count for all feeds as plain-text:

	<?php if (function_exists('sfs_display_total_count')) sfs_display_total_count(); ?>

See the plugin settings page for more options and infos.


**Notes**

To update your feed stats at any time (without waiting for the automatic 12-hour interval), click the "clear cache" link in the "Tools and Options" settings panel.

Also, this plugin uses WP Cron functionality to store feed data. Unfortunately, not all hosts/servers support WP Cron (e.g., Media Temple dv servers). If this is the case with your server, the total number of subscribers will not change from day to day. Fortunately there are a few possible solutions:

* Click the "Clear cache" button (located in the plugin settings) once or twice per day
* Add `define('ALTERNATE_WP_CRON', true);` to your WordPress configuration file, `wp-config.php`
* Use a [free cron service](https://www.setcronjob.com/) to request manually `https://example.com/wp-cron.php?setcronjob` once or twice per day (change the domain portion of the URL to match your own).

Any of these methods are suitable workarounds if WP Cron does not run automatically.


**Like the plugin?**

If you like Simple Statistics for Feeds, please take a moment to [give a 5-star rating](https://wordpress.org/support/plugin/simple-feed-stats/reviews/?rate=5#new-post). It helps to keep development and support going strong. Thank you!


**Upgrades**

To upgrade SFS, remove the old version and replace with the new version. Or just click "Update" from the Plugins screen and let WordPress do it for you automatically.

__Note:__ uninstalling the plugin from the WP Plugins screen results in the _removal of all settings and feed stats_ from the WP database. 


**Restore Default Options**

To restore default plugin options, either uninstall/reinstall the plugin, or visit the plugin settings &gt; "Restore default settings".


**Uninstalling**

Simple Statistics for Feeds cleans up after itself. All plugin options, transients, feed stats, and cron jobs will be removed from your database when the plugin is uninstalled via the Plugins screen. To delete the feed-stats table without uninstalling the plugin, visit the plugin settings &gt; "Delete database table". Likewise, to reset your feed stats without deleting the table, visit the plugin settings &gt; "Reset feed stats".



== Upgrade Notice ==

To upgrade SFS, remove the old version and replace with the new version. Or just click "Update" from the Plugins screen and let WordPress do it for you automatically.

__Note:__ uninstalling the plugin from the WP Plugins screen results in the _removal of all settings and feed stats_ from the WP database. 



== Screenshots ==

1. Simple Statistics for Feeds: Plugin options
2. Simple Statistics for Feeds: Feed Count Table
3. Simple Statistics for Feeds: Feed Stats Table
4. Simple Statistics for Feeds: FeedBurner-style count badge
5. Simple Statistics for Feeds: Dashboard Widget
6. Simple Statistics for Feeds: Full plugin settings page (panels toggle open/closed)

More screenshots and info available at the [SFS Homepage](https://perishablepress.com/simple-feed-stats/).



== Frequently Asked Questions ==

**Why did you change the name of the plugin?**

Before January 2022, the name of this plugin was "Simple Feed Stats". That name was used since the plugin was first born back in 2012. Went almost 10 years with that name. Then in 2021, I was asked to change the name because it conflicted with another product (not a WP plugin) that has been using a similar name since before 2012. So it was agreed to change the name of the plugin to "Simple Statistics for Feeds" to avoid any confusion. Note that the abbreviation for the name of this plugin will remain "SFS" going forward.


**How can link the Feedburner badge to my RSS feed?**

By default, the Feedburner count badge is not linked to any URL. So to wrap the badge in a link, you can do this for the shortcode: 

`<a href="https://example.com/feed/">[sfs_count_badge]</a>`

Or do this for the template tag: 

`<a href="https://example.com/feed/"><?php sfs_display_total_count(); ?></a>` 

You can change the URL to match the URL of your feed, or anything else. Also check out the other count badge shortcodes and template tags.


**How can I monitor a custom feed?**

If you use WordPress API for the [custom feed template](https://digwp.com/2011/08/custom-feeds/), and include the usual template tags for feeds, the SFS plugin will automatically track the custom feed. 


**Hits are recorded, but feed counts are not updating?**

Could be several things involved here..

First, as explained in the documentation, feed statistics are updated every 12 hours. So you may see new hits recorded without any change to the displayed feed count. At any time you can click the "Clear cache" link to clear the plugin cache and display the current counts.

Another thing to check, is the plugin setting "Enable custom count" enabled? If so, only the specified number will be displayed. So disable/uncheck the option to display current stats.

Also check if any cache plugins are in use on your site. If your feeds are cached it may affect the stats results.

Lastly, verify that your site's Cron functionality is working properly. SFS relies on WP Cron functionality to handle the updating of feed counts. Unfortunately, WP Cron does not always work on all servers/hosting environments. Please check out the "Notes" section on the [plugin's Installation page](https://wordpress.org/plugins/simple-feed-stats/installation/) for a list of solutions and more information.


**The plugin settings page is getting slow to load?**

Simple Statistics for Feeds logs requests in the database. So after awhile, the number of logged items can add up. To keep database size down, and resolve slow loading settings page, you can click the "Reset stats" link anytime. Doing so will remove all logged items from the database.

Tip: after clearing the database, your feed stats will show zero "0" until the plugin logs more feed requests. So instead of displaying "0" or other low numbers, you can enable the following settings:

* Custom count
* Enable custom count

Just remember to disable the Custom count setting after a day or so, after the plugin has logged more feed requests.


**What's up with "strict mode" reporting?**

It has to do with how SFS reports your feed stats. For example, in normal reporting mode (strict mode = off), each feed request is reported as unique. With strict mode enabled, feed requests are filtered by IP address, so that if "Pat" requests your comments feed five times per day, it's counted as "1" subscriber rather than "5". 

So strict mode is more accurate, but feed counts are usually lower with strict mode enabled. Note also that SFS still records all requests, so if you're reporting in strict mode the individual request data is still recorded and available in the Feed Stats table. In other words, strict mode determines how recorded data is reported, not collected.


**How can I use the the custom key/value parameters?**

If you don't already know, you probably don't need it. Basically it's a requested feature that enables the inclusion of a custom URL parameter (key/value) in either "custom" or "alt" tracking methods. You know, for stuff like Google Analytics.


**The stats are showing zero?**

During the first 12 hours, data is collected. Then the cache is refreshed to show the latest stats for the previous 12 hours. If your stats are showing zero or you would just like to update the count, visit the "Tools and Options" panel and click the "Clear cache" link.

Also: to test that the plugin is recording hits, visit the "Your Feed Info" panel. There you will find a list of links for your site's feeds. Click on a few of those links as a test. If the plugin is working correctly, you will see your own feed requests recorded in the "Subscriber Info" panel.


**Got a question?**

Send any questions or feedback via my [contact form](https://plugin-planet.com/support/#contact). Thanks! :)



== Changelog ==

If you like Simple Statistics for Feeds, please take a moment to [give a 5-star rating](https://wordpress.org/support/plugin/simple-feed-stats/reviews/?rate=5#new-post). It helps to keep development and support going strong. Thank you!


**20240625**

* Tests on WordPress 6.6


Full changelog @ [https://plugin-planet.com/wp/changelog/simple-feed-stats.txt](https://plugin-planet.com/wp/changelog/simple-feed-stats.txt)
