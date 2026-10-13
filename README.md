# CP Themes

Control panel themes and custom CSS for Craft CMS 6.

## Requirements

- PHP `^8.5`
- Craft CMS `^6.0.0-alpha.20`

## Installation in the Local Craft Harness

The parent Craft project loads this plugin through a Composer path repository:

```bash
ddev composer require yxanu/craft-cp-themes:@dev
ddev artisan craft:plugin:install cp-themes
```

## Features

- Built-in CP themes: None, Graphite, Midnight, Forest, High Contrast
- Comma-separated CSS file paths or URLs
- Inline additional CSS
- Cache-busting hash for readable local CSS files
- Control-panel-only CSS injection

CSS is registered in this order:

1. Built-in theme CSS
2. CSS file URLs
3. Additional CSS

## Craft 6 Alpha Notes

Tested with Craft `6.0.0-alpha.20`. Craft loads the plugin through `extra.class`, not `extra.laravel.providers`. Settings use the native `createSettings()` factory and `settingsForm()` API introduced during the Craft 6 alpha cycle.

Craft 6 is still in development, and later alpha releases may require compatibility updates.
