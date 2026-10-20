# Fonten

Customize WordPress admin typography with Google Fonts. The public website is unaffected.

**Early development · Version 0.1.0 · WordPress 6.0+ · PHP 7.4+**

## Install and use

1. Upload the release ZIP through **Plugins → Add New Plugin → Upload Plugin** and activate **Fonten**.
2. Open **Settings → Fonten**.
3. Check **Enable Google Fonts**, choose a font, and save.

Google Fonts is disabled by default. Checking the option loads Google's font catalog;
saving enables fonts across the dashboard. No API key is needed.
To restore the default typography, uncheck the option and save.

When enabled, the server contacts `fonts.google.com` for the catalog, and the browser
loads fonts from `fonts.googleapis.com` and `fonts.gstatic.com`. Google receives IP
addresses and request headers. See [Google's terms](https://developers.google.com/fonts/terms)
and [privacy policy](https://policies.google.com/privacy).

Disabling keeps saved settings. Deactivating deletes the plugin's settings, cache,
and generated CSS. See [readme.txt](readme.txt) for details.

## Development

Clone into your WordPress `wp-content/plugins/` directory:

```bash
git clone https://github.com/e-ardali/fonten.git fonten
```

Activate **FontenDev** and open **Settings → FontenDev**. No Composer install or frontend build is needed.

Run the checks:

```bash
php tests/google-fonts-consent.php
node --test tests/settings.test.cjs
```

Bug reports and pull requests are welcome on [GitHub](https://github.com/e-ardali/fonten).

## Build a release

Requires Python 3. From the project root, run:

```bash
python3 build-release.py
```

This creates `fonten-<version>.zip` in the project root, using the version in
`fonten.php`. The ZIP uses **Fonten**; the source stays **FontenDev**.

Only runtime files, `readme.txt`, and `LICENSE` are packaged inside `fonten/`.
Tests, development files, and old ZIPs are excluded. Tests stay in Git.
Add new runtime files to `RELEASE_FILES` in `build-release.py`.
Release ZIPs are ignored by Git.

## License

[GPL-2.0-or-later](LICENSE).
