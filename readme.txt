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
In Settings > Fonten, check Enable Google Fonts to load the catalog immediately. Then choose a font and save to enable dashboard font loading and generate an admin CSS file in uploads/fonten/.
Uncheck Enable Google Fonts and save to stop Fonten's external requests on subsequent page loads and restore the default WordPress typography. You can disable it even if the Google catalog is unavailable.
When enabled, the Google Fonts catalog is fetched from fonts.google.com and cached for one day, with a last successful copy available during outages. No API key is required.
Google Fonts is disabled by default, including on existing installations with saved font settings. No default Estedad font is loaded. Font previews are not implemented yet.
Only when enabled and a font has been saved, the dashboard requests styles from fonts.googleapis.com and font files from fonts.gstatic.com.
The plugin stores the selected font and generated stylesheet filename in WordPress options and does not modify the public website.

== External services ==

= Google Fonts =

Fonten uses Google Fonts to retrieve the available font catalog and load fonts in the WordPress admin dashboard. No Google account or API key is required.

Google Fonts is optional and disabled by default. Opening settings without saved permission makes no external requests. Checking Enable Google Fonts explicitly permits an immediate catalog request, even before saving. Only saving enables persistent dashboard font loading. A previously saved font, generated stylesheet, or cached catalog alone is not consent.

The settings checkbox explains these connections and links to Google's terms and privacy policy. Requests use the following services after the corresponding explicit permission:

* https://fonts.google.com/metadata/fonts - The WordPress server retrieves the font catalog immediately when the checkbox is checked, or when settings need it after saved opt-in, if the catalog cache is missing or expired. Successful results are cached for one day. A last successful copy is retained as a fallback.
* https://fonts.googleapis.com - After a font is saved, the browser requests its stylesheet on admin pages, subject to browser caching.
* https://fonts.gstatic.com - The browser downloads the font files referenced by that stylesheet, subject to browser caching.

The catalog request exposes the WordPress server's IP address and HTTP request headers to Google. Font requests expose the admin user's IP address, requested font URLs, and browser HTTP headers to Google. Referrer information may also be included, depending on the browser and referrer policy.

When disabled, Fonten does not enqueue its font stylesheets, and WordPress typography is restored. A new catalog request requires explicitly checking the checkbox again. The previous selection, generated CSS, and catalog cache are retained but remain inactive until you explicitly enable Google Fonts again. Re-enabling may restore the saved font. If no font has been saved or the CSS file is missing, WordPress typography remains in use. Font files are served by Google and are not hosted locally by Fonten.

* [Google Fonts API Terms of Service](https://developers.google.com/fonts/terms)
* [Google Privacy Policy](https://policies.google.com/privacy)

== Deactivation and data removal ==

Deactivating the Fonten plugin deletes its saved Google Fonts consent, selected font and stylesheet filename, catalog backup, and catalog transient, including its database timeout and object-cache entry. Generated CSS files and unfinished temporary CSS files are removed from uploads/fonten/, and the directory is removed if empty. Unrelated options and files are preserved. Network deactivation cleans each site in the current network.

Reactivating starts fresh with Google Fonts disabled; opt in and select a font again to use Google Fonts. Unchecking Enable Google Fonts while the plugin stays active only stops Google requests and retains the previous settings and cache.

== Installation ==

1. Place the fonten directory in wp-content/plugins/.
2. Activate Fonten from the Plugins screen.
3. Open Settings > Fonten. Google Fonts starts disabled.
4. To opt in, check Enable Google Fonts, wait for the catalog to load, choose a font, and click Save settings.
5. To opt out, uncheck Enable Google Fonts and click Save settings.

== Changelog ==

= 0.1.0 =
* Add the plugin entry point, admin initialization, font selection, and generated CSS.
* Require explicit checkbox consent for immediate catalog requests and saved consent for dashboard font loading; allow disabling to restore WordPress typography, including on existing installations.
