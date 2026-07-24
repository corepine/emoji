<?php

use Corepine\Emoji\EmojiData;
use Illuminate\Support\Facades\Blade;

it('registers package config and emoji data service', function () {
    expect(config('corepine-emoji.locale'))->toBe('en')
        ->and(config('corepine-emoji.columns'))->toBe(8)
        ->and(app(EmojiData::class))->toBeInstanceOf(EmojiData::class);
});

it('renders the main emoji component with a wire model', function () {
    $html = Blade::render('<x-corepine.emoji wire:model="body" />');

    expect($html)
        ->toContain('data-corepine-emoji')
        ->toContain('x-anchor.offset.10')
        ->toContain('scrollbar-thin')
        ->toContain('--corepine-emoji-columns: 8')
        ->toContain('wireModel: \'body\'')
        ->toContain('dusk="emoji-picker"');
});

it('renders bundled package assets', function () {
    $html = Blade::render('<x-corepine.emoji.assets />');

    expect($html)
        ->toContain('data-corepine-emoji-assets')
        ->toContain('corepine-emoji-panel')
        ->toContain('scrollbar-thin')
        ->toContain('corepine-emoji-grid');
});

it('renders custom columns and merges classes on the picker panel', function () {
    $html = Blade::render('<x-corepine.emoji :trigger="false" :columns="4" class="emoji-panel-custom-size" wrapper-class="w-full" />');

    expect($html)
        ->toContain('corepine-emoji relative inline-flex w-full')
        ->toContain('--corepine-emoji-columns: 4')
        ->toContain('corepine-emoji-panel')
        ->toContain('emoji-panel-custom-size')
        ->toContain('corepine-emoji-grid');
});

it('renders the reaction component', function () {
    $html = Blade::render('<x-corepine.emoji.reaction />');

    expect($html)
        ->toContain('data-corepine-emoji-reaction')
        ->toContain('x-anchor.offset.10')
        ->toContain('h-[31rem]')
        ->not->toContain('h-full w-full shadow-none')
        ->toContain('dusk="emoji-reaction-picker"')
        ->toContain('quick:')
        ->toContain('More reactions');
});

it('renders embedded picker panels when triggerless by default', function () {
    $html = Blade::render('<x-corepine.emoji :trigger="false" />');

    expect($html)
        ->toContain('h-full w-full shadow-none')
        ->toContain('dusk="emoji-picker"');
});

it('loads generated emoji categories', function () {
    $categories = app(EmojiData::class)->categories();

    expect($categories)->not->toBeEmpty()
        ->and($categories[0])->toHaveKeys(['key', 'label', 'icon', 'emojis'])
        ->and($categories[0]['emojis'][0])->toHaveKeys(['emoji', 'label', 'tags']);
});
