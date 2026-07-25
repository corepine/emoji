<?php

namespace Corepine\Emoji\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static mixed config(?string $key = null, mixed $default = null)
 * @method static string locale()
 * @method static string theme()
 * @method static mixed color()
 * @method static array colors(mixed $color = null)
 * @method static string colorStyle(mixed $color = null)
 * @method static int columns()
 * @method static int recentLimit()
 * @method static array quickReactions()
 * @method static string recentStorageKey()
 * @method static string reactionStorageKey()
 * @method static \Corepine\Emoji\Support\EmojiEvents event()
 * @method static string selectedEvent()
 * @method static string reactionSelectedEvent()
 * @method static string dispatchEvent(string $key)
 * @method static string storageKey(string $key, string $default)
 * @method static array categories(?string $locale = null)
 * @method static array emojis(?string $locale = null)
 *
 * @see \Corepine\Emoji\Emoji
 */
class Emoji extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \Corepine\Emoji\Emoji::class;
    }
}
