# Fonten

A WordPress plugin for customizing admin typography with Google Fonts.

**Status: early development · Version 0.1.0**

Fonten currently provides the plugin foundation and a settings page. Font selection, preview, and font loading are planned and are **not available yet**.

## Current functionality

- Registers a settings page under **Settings → Fonten**.
- Restricts access to users with the `manage_options` capability.
- Makes no requests to Google or other external services.
- Stores no settings and makes no changes to the public website.

## Planned functionality

- Choose a Google Font for the WordPress admin dashboard.
- Preview the selected font before applying it.
- Load and apply the selected font to the admin interface.

These are planned features, not a release commitment. The initial scope focuses on admin typography using Google Fonts.

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

The settings page currently displays a placeholder for the upcoming font controls. Activating this version does not change any fonts.

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

There is currently no automated test suite in this repository.

## Changelog

### 0.1.0

- Added the plugin entry point and bootstrap.
- Added the initial admin settings page.

## License

Fonten is licensed under the **GNU General Public License v2.0 or later** (`GPL-2.0-or-later`). See [LICENSE](LICENSE) for the license text.
