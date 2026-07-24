<?php

namespace Corepine\Emoji\Support;

use Corepine\Support\Colors\Color as SupportColor;
use Corepine\Support\Colors\ColorManager;

final class Color
{
    /**
     * @return array{accent: string, accent_hover: string}
     */
    public static function resolve(mixed $color = null): array
    {
        $palette = self::palette($color ?: config('corepine-emoji.color', 'emerald'));

        return [
            'accent' => $palette[500],
            'accent_hover' => $palette[600] ?? $palette[500],
        ];
    }

    public static function style(mixed $color = null): string
    {
        $resolved = self::resolve($color);

        return "--corepine-emoji-accent: {$resolved['accent']}; --corepine-emoji-accent-hover: {$resolved['accent_hover']};";
    }

    /**
     * @return array<int|string, string>
     */
    private static function palette(mixed $color): array
    {
        if (is_array($color)) {
            $normalized = [];

            foreach ($color as $shade => $value) {
                if (! is_string($value) || trim($value) === '') {
                    continue;
                }

                $normalized[is_numeric($shade) ? (int) $shade : $shade] = trim($value);
            }

            if (isset($normalized[500])) {
                ksort($normalized);

                return $normalized;
            }
        }

        if (is_string($color) && trim($color) !== '') {
            $palette = app(ColorManager::class)->palette($color);

            if (is_array($palette) && isset($palette[500])) {
                return $palette;
            }
        }

        return SupportColor::Emerald;
    }
}
