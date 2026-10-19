=== Fonten ===
Tags: admin, typography, fonts, google fonts
Requires at least: 6.0
Requires PHP: 7.4
Stable tag: 0.1.0
Contributors: erak127
Author: Ehsan
Tested up to: 7.1
License: GPL-2.0-or-later
License URI: https://www.gnu.org/licenses/gpl-2.0.html


Customize WordPress admin typography using Google Fonts only.

== Description ==

Fonten is a plugin foundation for changing the font inside the WordPress admin dashboard.
The font source is Google Fonts exclusively; custom font uploads and other font providers are outside the plugin's scope.

This initial version registers a settings page at Settings > Fonten.
Choose a font in Settings > Fonten and save to generate an admin CSS file in uploads/fonten/.
The Google Fonts catalog is fetched from fonts.google.com and cached for one day, with a last successful copy available during outages. No API key is required.
Estedad remains the default until the first successful save. Font previews are not implemented yet.
The dashboard requests styles from fonts.googleapis.com and font files from fonts.gstatic.com.
The plugin stores the selected font and generated stylesheet filename in WordPress options and does not modify the public website.

== Installation ==

1. Place the fonten directory in wp-content/plugins/.
2. Activate Fonten from the Plugins screen.
3. Open Settings > Fonten, choose a Google Font, and click Save font.

== Changelog ==

= 0.1.0 =
* Add the plugin entry point, admin initialization, and initial settings screen.
