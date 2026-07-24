# Corepine Emoji

Corepine Emoji provides Blade and Alpine emoji picker components for Laravel, with optional Livewire support.

```bash
composer require corepine/emoji
```

```blade
<x-corepine.emoji.assets />

<textarea id="message"></textarea>

<x-corepine.emoji target="message" />

<textarea wire:model.live="body"></textarea>

<x-corepine.emoji target="body" />

<x-corepine.emoji target="message">
    <button type="button">Emoji</button>
</x-corepine.emoji>

<x-corepine.emoji.reaction />
```

The package renders native Unicode emoji and ships compact emoji metadata generated from Emojibase.
