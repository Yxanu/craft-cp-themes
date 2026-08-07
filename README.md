# CP Themes

Control panel themes and custom CSS for Craft CMS 6.

## Requirements

- PHP `^8.5`
- Craft CMS `^6.0.0`

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

Craft `6.0.0-alpha.16` loads plugins through Craft plugin metadata, not by declaring the plugin class in `extra.laravel.providers`. Keep the plugin class in `extra.class` only.
