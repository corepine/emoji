# Corepine Emoji

Corepine Emoji is a Blade and Alpine emoji picker for Laravel apps. It ships native Unicode emoji, a message/comment picker, and a reaction picker that works cleanly with Livewire-powered interfaces.

Learn more at [corepine.dev](https://corepine.dev).

## Installation

```bash
composer require corepine/emoji
```

## Usage

```blade
<textarea id="message"></textarea>

<x-corepine.emoji target="message" />

<x-corepine.emoji target="message">
    <button type="button">Emoji</button>
</x-corepine.emoji>

<x-corepine.emoji.reaction />
```

When you are not compiling the package CSS through your app, render the bundled styles once in your layout:

```blade
<x-corepine.emoji.styles />
```

## Package API

```php
use Corepine\Emoji\Facades\Emoji;

Emoji::theme();
Emoji::columns();
Emoji::quickReactions();
Emoji::recentStorageKey();
Emoji::reactionStorageKey();
Emoji::event()->selected();
Emoji::event()->reactionSelected();
Emoji::categories();
```

Use the facade or type-hint `Corepine\Emoji\Emoji` from another Laravel package when you need the active Emoji config values.

## License

Corepine Emoji is open-sourced software licensed under the [MIT license](LICENSE.md).
