# Corepine Emoji

Corepine Emoji is a Blade and Alpine emoji picker for Laravel apps. It ships native Unicode emoji, a message/comment picker, and a reaction picker that works cleanly with Livewire-powered interfaces.

Learn more at [corepine.dev](https://corepine.dev).

## Installation

```bash
composer require corepine/emoji
```

## Usage

```blade
<x-corepine.emoji.assets />

<textarea id="message"></textarea>

<x-corepine.emoji target="message" />

<x-corepine.emoji target="message">
    <button type="button">Emoji</button>
</x-corepine.emoji>

<x-corepine.emoji.reaction />
```

## License

Corepine Emoji is open-sourced software licensed under the [MIT license](LICENSE.md).
