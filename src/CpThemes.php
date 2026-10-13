<?php

namespace Local\CpThemes;

use CraftCms\Cms\Form\Controls\Choice;
use CraftCms\Cms\Form\Controls\Lightswitch;
use CraftCms\Cms\Form\Controls\Text;
use CraftCms\Cms\Form\Controls\Textarea;
use CraftCms\Cms\Form\Form;
use CraftCms\Cms\Form\FormContext;
use CraftCms\Cms\Form\Nodes\Field;
use CraftCms\Cms\Plugin\Plugin;
use CraftCms\Cms\Support\Env;
use CraftCms\Cms\Support\Facades\HtmlStack;
use CraftCms\Cms\Support\Url;
use CraftCms\Cms\Twig\Events\PageStarting;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;

class CpThemes extends Plugin
{
    public string $schemaVersion = '1.0.0';

    public bool $hasCpSettings = true;

    public function boot(): void
    {
        Event::listen(PageStarting::class, function (PageStarting $event): void {
            if (! request()->isCpRequest()) {
                return;
            }

            /** @var Settings $settings */
            $settings = $this->getSettings();

            $this->registerThemeCss($settings);
            $this->registerCssFiles($settings);
            $this->registerAdditionalCss($settings);
        });
    }

    protected static function createSettings(): Settings
    {
        return new Settings;
    }

    public function settingsForm(FormContext $context = new FormContext): ?Form
    {
        return Form::make([
            Field::make('Theme', Choice::make('theme')->options(Settings::themeOptions())->withoutPlaceholder()),
            Field::make('CSS File(s)', Text::make('cssFile')),
            Field::make('Additional CSS', Textarea::make('additionalCss')->rows(12)->monospace()),
            Field::make('Cache busting', Lightswitch::make('cacheBusting')),
        ]);
    }

    private function registerThemeCss(Settings $settings): void
    {
        $path = $this->themePath($settings->theme);

        if ($path === null) {
            return;
        }

        $css = @file_get_contents($path);

        if ($css === false) {
            Log::warning("Unable to load CP theme CSS from {$path}.", [__METHOD__]);

            return;
        }

        HtmlStack::css($css, [], "cp-themes:theme:{$settings->theme}");
    }

    private function registerCssFiles(Settings $settings): void
    {
        $files = array_filter(array_map('trim', explode(',', $settings->cssFile)));

        foreach ($files as $file) {
            $resolved = $this->resolveCssFile($file);

            if ($resolved === null) {
                continue;
            }

            [$url, $hashSource] = $resolved;

            if ($settings->cacheBusting) {
                $url = $this->addCacheBustHash($url, $hashSource);
            }

            HtmlStack::cssFile($url, [], "cp-themes:file:{$url}");
        }
    }

    private function registerAdditionalCss(Settings $settings): void
    {
        $css = trim($settings->additionalCss);

        if ($css === '') {
            return;
        }

        HtmlStack::css($css, [], 'cp-themes:additional');
    }

    private function themePath(string $theme): ?string
    {
        if (! array_key_exists($theme, Settings::themes()) || $theme === Settings::THEME_NONE) {
            return null;
        }

        return dirname(__DIR__)."/resources/themes/{$theme}.css";
    }

    private function resolveCssFile(string $file): ?array
    {
        $parsed = Env::parse($file);

        if (! is_string($parsed) || trim($parsed) === '') {
            return null;
        }

        $parsed = trim($parsed);

        if (Url::isAbsoluteUrl($parsed) || Url::isProtocolRelativeUrl($parsed)) {
            return [$parsed, $parsed];
        }

        if (Url::isRootRelativeUrl($parsed)) {
            return [$parsed, public_path(ltrim(parse_url($parsed, PHP_URL_PATH) ?: $parsed, '/'))];
        }

        $publicPath = $this->normalizePath(public_path()).'/';
        $parsedPath = $this->normalizePath($parsed);

        if (str_starts_with($parsedPath, $publicPath)) {
            $relativePath = ltrim(substr($parsedPath, strlen($publicPath)), '/');

            return [asset($relativePath), $parsedPath];
        }

        $relativePath = ltrim($parsed, '/');

        return [asset($relativePath), public_path($relativePath)];
    }

    private function addCacheBustHash(string $url, string $source): string
    {
        $contents = @file_get_contents($source);

        if ($contents === false) {
            Log::warning("Can't bust cache for CP Themes CSS, unable to load contents of {$source}.", [__METHOD__]);

            return $url;
        }

        return Url::urlWithParams($url, ['e' => sha1($contents)]);
    }

    private function normalizePath(string $path): string
    {
        return rtrim(str_replace('\\', '/', $path), '/');
    }
}
