=== Plugin Name ===
Plugin Name: SnapReplay
Donate: https://snapreplay.com/
Contributors: cd34
Tags: widget, widgets, social, photo, photos
Requires at least: 4.6
Tested up to: 6.7
Requires PHP: 7.4
Stable tag: 0.3

This widget allows you to specify an event or venue on SnapReplay to follow and publishes the latest picture.

== Description ==

This plugin uses [SnapReplay](https://snapreplay.com/) and displays the latest
content uploaded to an Event. Photos or Images from that Event or Stream
update on your side, as they are uploaded to SnapReplay.

== Installation ==

1. Activate Plugin
2. Settings > SnapReplay Widget, enter your Event or Venue ID
3. Appearance > Widgets, drag the SnapReplay Widget to the desired Sidebar

== Frequently Asked Questions ==

== Screenshots ==

1. SnapReplay Widget Setup Screen

2. Snapshot of the Live Stream on a website

== Changelog ==

= 0.3 =
* Modernized widget using WP_Widget class
* Fixed XSS vulnerabilities with proper output escaping
* Fixed CSRF protection via WordPress settings API nonces
* Fixed bug where stream ID was reset to 1 on every admin page load
* Switched all URLs to HTTPS
* Used wp_enqueue_script for proper script loading
* Used textContent/createElement instead of innerHTML for security
* Only load scripts when widget is active

= 0.2 =
* Removed JQuery requirement

= 0.1 =
* Initial release

== Upgrade Notice ==

= 0.3 =
Security and compatibility update. Fixes XSS vulnerabilities and updates to modern WordPress widget API.
