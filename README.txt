=== Plugin Name ===
Contributors: melvr, erdem61, autovisie
Donate link: http://autovisie.nl/devblog/autovisie-ab-title-testing-for-wordpress/
Tags: ab testing, a/b, ab titles, titles
Requires at least: 3.0.1
Tested up to: 4.6.1
Stable tag: 4.6.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

This plugin makes it possible to test different titles (A/B) for different posts.

== Description ==

Want to have a tool for AB testing titles? This free WordPress plugin makes this possible.

= Why we built it =

Our editors at Autovisie really like to check which title attracts more readers.

For example: Will someone choose for the clickbait or more serious, straightforward heading?

With the Autovisie AB Title Tester, everyone – not only the editor staff, but also marketing and sales – can check out which test will win.

Based on the WordPress Boilterplate that can be found on http://wppb.io/.

* Plugin website: http://autovisie.nl/devblog/autovisie-ab-title-testing-for-wordpress/
* Want to know more about our tools, check out our website at: http://autovisie.nl/devblog/


== Installation ==

Activate the plugin and set the correct settings (settings -> AB Testing settings).
Then you will see an extra input field when editing posts. You will also see the amount of views on the right.

1. Upload ‘av-ab-testing; to the ;/wp-content/plugins/’ directory
2. Activate the plugin through the ‘Plugins’ menu in WordPress
3. Set the correct settings (settings -> AB Testing settings)

**========**
**Settings**
**========**

= Activated =
When this is not set, the plugin will not show any A/B title settings when you are editing the posts and will not register any views.

= Use JS for the titles? =
This setting controles the way the titles are replaced. This feature is mainly for sites that use caching.

= Amount of views =
This setting, in combination with the “Percentage” setting, controles the amount of views that will be registered. Note: Views of registered users (editors and above) are excluded!

= Percentage =
This setting, in combination with the “Amount of views” setting, controles when the plugin must make a choice between both titles.

= Posts using AB testing =
Here you will find a list with the posts currently using A/B testing.

== Frequently Asked Questions ==

= Does this plugin work with cache plugins like W3 Total Cache? =
*Yes it does! You can check the box  “Use JS for titles” to inject the titles using jQuery and so preventing caching the titles.*

= What is the “Hide titles until JS is done” setting? =
*This setting will hide the titles until the titles are fetched with jQuery. When this is done, the titles will be shown again. This prevents a ‘flickering’ when replacing the titles with JQuery. Note: This setting only works when “Use JS for titles” is checked.*

= Is it possible to reset the title count? =
*You can do this by going to the post you want to reset the counter of. Then check the box “Reset all counters” and save the post. The counters will be reset to zero.*

= How does the plugin determine which title to choose? =
*The plugin uses the values you setup in the settings at “Amount of views” and “Percentage”. Based on these settings, the plugin calculates which titles has the most views and when it reaches the “Percentage” setting, it will choose that title.*

= Does this plugin work with ajax pagination and ajax search? =
*Currently this is not working, we will be working on this for a future release*

== Screenshots ==

1. Menu
2. Settings
3. Post editing

== Changelog ==

= 1.0.3 =
* Posts using AB testing overview is changed on AB Testing Settings.

= 1.0.2 =
* Changed the way we are showing the overview of AB Titles in the settings.

= 1.0.1 =
* When using ajax pagination and ajax search, the titles where hidden by default. Changed this to prevent hidden titles with ajax pagination and ajax search.

= 1.0.0 =
* Our first release!