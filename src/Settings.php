<?php

namespace Local\CpThemes;

use CraftCms\Cms\Plugin\PluginSettings;
use Illuminate\Validation\Rule;

class Settings extends PluginSettings
{
    public const string THEME_NONE = 'none';

    public string $theme = self::THEME_NONE;

    public string $cssFile = '';

    public string $additionalCss = '';

    public bool $cacheBusting = true;

    public static function themes(): array
    {
        return [
            self::THEME_NONE => 'None',
            'graphite' => 'Graphite',
            'midnight' => 'Midnight',
            'forest' => 'Forest',
            'high-contrast' => 'High Contrast',
        ];
    }

    public static function themeOptions(): array
    {
        return collect(self::themes())
            ->map(fn (string $label, string $value): array => [
                'label' => $label,
                'value' => $value,
            ])
            ->values()
            ->all();
    }

    public function getRules(): array
    {
        return [
            'theme' => ['string', Rule::in(array_keys(self::themes()))],
            'cssFile' => ['string'],
            'additionalCss' => ['string'],
            'cacheBusting' => ['boolean'],
        ];
    }
}
