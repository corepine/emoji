<?php

namespace Corepine\Emoji;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;

class EmojiData
{
    /**
     * @var array<string, array<int, array{key: string, label: string, icon: string, emojis: array<int, array{emoji: string, label: string, tags: array<int, string>}>}>>
     */
    protected array $categories = [];

    /**
     * @return array<int, array{key: string, label: string, icon: string, emojis: array<int, array{emoji: string, label: string, tags: array<int, string>}>}>
     */
    public function categories(?string $locale = null): array
    {
        $locale = $locale ?: (string) config('corepine-emoji.locale', 'en');

        if (array_key_exists($locale, $this->categories)) {
            return $this->categories[$locale];
        }

        $path = __DIR__."/../resources/data/{$locale}/emoji.json";

        if (! File::exists($path)) {
            $path = __DIR__.'/../resources/data/en/emoji.json';

            if (! File::exists($path)) {
                return $this->categories[$locale] = [];
            }
        }

        $data = json_decode(File::get($path), true);

        return $this->categories[$locale] = is_array($data) ? $data : [];
    }

    /**
     * @return array<int, string>
     */
    public function quickReactions(): array
    {
        return Collection::make(config('corepine-emoji.quick_reactions', []))
            ->filter(static fn (mixed $emoji): bool => is_string($emoji) && trim($emoji) !== '')
            ->values()
            ->all();
    }
}
