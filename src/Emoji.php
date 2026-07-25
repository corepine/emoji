<?php

namespace Corepine\Emoji;

use Corepine\Emoji\Support\Color;
use Corepine\Emoji\Support\EmojiEvents;

class Emoji
{
    protected ?EmojiEvents $events = null;

    public function __construct(
        protected EmojiData $data,
    ) {
    }

    public function config(?string $key = null, mixed $default = null): mixed
    {
        $base = 'corepine-emoji';

        return config($key === null ? $base : "{$base}.{$key}", $default);
    }

    public function locale(): string
    {
        return (string) $this->config('locale', 'en');
    }

    public function theme(): string
    {
        return (string) $this->config('theme', 'system');
    }

    public function color(): mixed
    {
        return $this->config('color', 'emerald');
    }

    /**
     * @return array{accent: string, accent_hover: string}
     */
    public function colors(mixed $color = null): array
    {
        return Color::resolve($color ?? $this->color());
    }

    public function colorStyle(mixed $color = null): string
    {
        return Color::style($color ?? $this->color());
    }

    public function columns(): int
    {
        return max(1, (int) $this->config('columns', 8));
    }

    public function recentLimit(): int
    {
        return max(0, (int) $this->config('recent_limit', 24));
    }

    /**
     * @return array<int, string>
     */
    public function quickReactions(): array
    {
        return collect($this->config('quick_reactions', []))
            ->filter(fn (mixed $emoji): bool => is_string($emoji) && trim($emoji) !== '')
            ->values()
            ->all();
    }

    public function recentStorageKey(): string
    {
        return $this->storageKey('recent', 'corepine.emoji.recent');
    }

    public function reactionStorageKey(): string
    {
        return $this->storageKey('reactions', 'corepine.emoji.reactions');
    }

    public function event(): EmojiEvents
    {
        return $this->events ??= new EmojiEvents($this);
    }

    public function selectedEvent(): string
    {
        return $this->event()->selected();
    }

    public function reactionSelectedEvent(): string
    {
        return $this->event()->reactionSelected();
    }

    public function dispatchEvent(string $key): string
    {
        $configured = $this->config("events.dispatch.{$key}");

        if (is_string($configured) && trim($configured) !== '') {
            return trim($configured);
        }

        return [
            'selected' => 'emoji.selected',
            'reaction_selected' => 'emoji.reaction-selected',
        ][$key] ?? $key;
    }

    public function storageKey(string $key, string $default): string
    {
        return (string) $this->config("storage.{$key}", $default);
    }

    /**
     * @return array<int, array{key: string, label: string, icon: string, emojis: array<int, array{emoji: string, label: string, tags: array<int, string>}>}>
     */
    public function categories(?string $locale = null): array
    {
        return $this->data->categories($locale);
    }

    /**
     * @return array<int, array{emoji: string, label: string, tags: array<int, string>}>
     */
    public function emojis(?string $locale = null): array
    {
        return collect($this->categories($locale))
            ->flatMap(fn (array $category): array => $category['emojis'])
            ->values()
            ->all();
    }
}
