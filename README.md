# Fonten

A WordPress plugin for customizing admin typography with Google Fonts.

**Status: early development · Version 0.1.0**

Fonten provides optional Google Fonts for the WordPress admin dashboard. Google Fonts is **disabled by default**, including for existing installations with a previously saved font. A saved font is not consent to contact Google.

## Current functionality

- Registers **Settings → Fonten**, restricted to users with `manage_options`.
- Makes no external requests until an administrator explicitly checks **Enable Google Fonts** or has previously enabled and saved it. No default Estedad font is loaded.
- Checking **Enable Google Fonts** immediately loads the catalog into the select through an authenticated AJAX request, using the cache when available. Choose a font and save once to persist permission, generate a CSS file in `uploads/fonten/`, and apply the font to the admin interface. Checking alone does not enable fonts across the dashboard.
- Caches the catalog for one day and keeps a last successful copy for outages. No Google account or API key is required.
- Lets you disable Google Fonts and save at any time, including when the catalog is unavailable. This stops Fonten's external requests on subsequent page loads and restores the default WordPress typography.
- Retains the previous font selection, generated CSS, and catalog cache when disabled, but does not apply the font or treat them as consent. The select follows the checkbox immediately and may show a previously cached catalog without contacting Google. Unchecking also disables the font stylesheet on the current settings page; save to disable Google Fonts across the dashboard. Explicitly enabling again may restore a previously saved font.
- Does not modify the public website. Font previews are not implemented yet.

## External services and consent

The checkbox explains the connections and links to the [Google Fonts Terms of Service](https://developers.google.com/fonts/terms) and [Google Privacy Policy](https://policies.google.com/privacy). Checking it grants permission for the immediate catalog request, before saving. Persistent dashboard font loading still requires saving. Opening the settings page with no saved permission makes no external requests.

After explicit checkbox consent (or saved permission):

- The WordPress server requests the catalog from `https://fonts.google.com/metadata/fonts` when needed and not cached, exposing its IP address and HTTP request headers.
- After a font has been saved, the browser requests its stylesheet from `fonts.googleapis.com` and font files from `fonts.gstatic.com`, subject to browser caching. These requests expose the admin user's IP address, requested URLs, and browser headers; referrer information depends on browser policy.
- Font files remain hosted by Google. The generated local CSS references Google's stylesheet.

Existing font settings and cached files do not enable Google Fonts automatically. If no font is selected or its generated CSS is missing, WordPress's default typography remains in use; there is no automatic Estedad fallback.

## Deactivation and data removal

Deactivating the **Fonten plugin** deletes its saved consent, font selection, stylesheet filename, catalog backup, and catalog transient (including its database timeout and object-cache entry). It also removes Fonten's generated CSS files and unfinished temporary CSS files from `uploads/fonten/`, then removes that directory if empty. Unrelated options and files are preserved. Network deactivation cleans each site in the current network.

Reactivating starts fresh with Google Fonts disabled and requires opting in and choosing a font again. This differs from unchecking **Enable Google Fonts** while keeping the plugin active: that stops Google requests but retains the previous selection and cache.

## Requirements

- WordPress 6.0 or later.
- PHP 7.4 or later.

## Installation

### From GitHub

1. Download the repository using **Code → Download ZIP** and extract it.
2. Rename the extracted directory to `fonten`.
3. Copy it into your WordPress installation's `wp-content/plugins/` directory, so the main file is at `wp-content/plugins/fonten/fonten.php`.
4. Activate **Fonten** from the WordPress **Plugins** screen.
5. Open **Settings → Fonten**.

Google Fonts starts disabled. To use it, check **Enable Google Fonts**, wait for the catalog, choose a font, and save. To stop using it, uncheck the option and save. Activating or upgrading the plugin alone does not enable Google requests.

### For development

From your local WordPress installation's `wp-content/plugins/` directory:

```bash
git clone https://github.com/e-ardali/fonten.git fonten
```

Activate the plugin and open **Settings → Fonten**. The current version does not require a Composer install or a frontend build step.

## Contributing

Contributions are welcome, including bug reports, documentation improvements, testing, and code changes.

1. Check the [existing issues](https://github.com/e-ardali/fonten/issues) before starting. For a new feature or a substantial change, open an issue first to discuss the approach.
2. Fork this repository and create a branch for your change.
3. Make a focused change and follow the existing code style and WordPress coding conventions.
4. Test the change in a local WordPress installation. At minimum, check plugin activation and the **Settings → Fonten** page.
5. Open a [pull request](https://github.com/e-ardali/fonten/pulls) targeting `main`. Explain what changed, why, and how you tested it. Include screenshots for visible interface changes.

When reporting a bug, include your WordPress and PHP versions, steps to reproduce it, and the expected and actual behavior. Remove credentials and other sensitive information from logs and screenshots.

Run the standalone consent and font-generation regression checks with `php tests/google-fonts-consent.php`. They use WordPress stubs and make no network requests. Run `node --test tests/settings.test.cjs` for the immediate-fetch, cancellation, and retry controls; also verify the settings flow in a local WordPress installation.

## Changelog

### 0.1.0

- Added the plugin entry point and bootstrap.
- Added the admin settings page, Google Fonts selection, and generated CSS.
- Require explicit checkbox consent for catalog requests and saved opt-in for dashboard font loading, including on existing installations.

## License

Fonten is licensed under the **GNU General Public License v2.0 or later** (`GPL-2.0-or-later`). See [LICENSE](LICENSE) for the license text.
