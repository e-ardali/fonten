=== Fonten ===
Tags: admin, typography, fonts, google fonts
Requires at least: 6.0
Requires PHP: 7.4
Stable tag: 0.1.0
License: GPL-2.0-or-later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Customize WordPress admin typography using Google Fonts only.

== Description ==

Fonten is a plugin foundation for changing the font inside the WordPress admin dashboard.
The planned font source is Google Fonts exclusively; custom font uploads and other font providers are outside the plugin's scope.

This initial version registers a settings page at Settings > Fonten.
Font selection, preview, and font loading are not implemented yet.
It currently makes no requests to Google or other external services, stores no settings, and does not modify the public website.

== Installation ==

1. Place the fonten directory in wp-content/plugins/.
2. Activate Fonten from the Plugins screen.
3. Open Settings > Fonten.

== Changelog ==

= 0.1.0 =
* Add the plugin entry point, admin initialization, and initial settings screen.
