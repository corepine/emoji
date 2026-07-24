<?php

use Corepine\Emoji\EmojiData;
use Illuminate\Support\Facades\Blade;

it('registers package config and emoji data service', function () {
    expect(config('corepine-emoji.locale'))->toBe('en')
        ->and(config('corepine-emoji.columns'))->toBe(8)
        ->and(app(EmojiData::class))->toBeInstanceOf(EmojiData::class);
});

it('renders the main emoji component', function () {
    $html = Blade::render('<x-corepine.emoji target="body" />');

    expect($html)
        ->toContain('data-corepine-emoji')
        ->toContain('x-anchor.offset.10')
        ->toContain('scrollbar-thin')
        ->toContain('--corepine-emoji-columns: 8')
        ->toContain('target: \'body\'')
        ->toContain('resolveInput()')
        ->toContain('dusk="emoji-picker"');
});

it('renders the main emoji component without livewire coupling', function () {
    $html = Blade::render('<x-corepine.emoji target="message" />');

    expect($html)
        ->toContain('target: \'message\'')
        ->not->toContain('wireModel')
        ->not->toContain('$wire.set');
});

it('renders next to a livewire model field without an input id', function () {
    $html = Blade::render('<textarea wire:model.live="body"></textarea><x-corepine.emoji target="body" />');

    expect($html)
        ->toContain('wire:model.live="body"')
        ->toContain('target: \'body\'')
        ->not->toContain('$wire.set');
});

it('renders a custom trigger from the component slot', function () {
    $html = Blade::render('<x-corepine.emoji target="message"><button type="button" class="custom-trigger">Pick emoji</button></x-corepine.emoji>');

    expect($html)
        ->toContain('dusk="emoji-trigger-button"')
        ->toContain('custom-trigger')
        ->toContain('Pick emoji')
        ->not->toContain('<span class="text-xl leading-none">☺</span>');
});

it('renders bundled package assets', function () {
    $html = Blade::render('<x-corepine.emoji.assets />');

    expect($html)
        ->toContain('data-corepine-emoji-assets')
        ->toContain('corepine-emoji-panel')
        ->toContain('scrollbar-thin')
        ->toContain('corepine-emoji-grid');
});

it('renders scroll-linked category navigation', function () {
    $html = Blade::render('<x-corepine.emoji target="message" />');

    expect($html)
        ->toContain('navigationItems()')
        ->toContain('scrollToSection(item.key)')
        ->toContain('syncActiveCategory()')
        ->toContain('data-corepine-emoji-section="recent"')
        ->toContain('categoryIcons')
        ->toContain('flags');
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

it('moves grid column classes from the panel class to the emoji grids', function () {
    $panelWidth = 'w-[500'.'px]';
    $baseGrid = 'grid-cols-'.'4';
    $responsiveGrid = 'sm:grid-cols-'.'9';
    $html = Blade::render('<x-corepine.emoji :trigger="false" class="'.$panelWidth.' rounded-xl '.$baseGrid.' '.$responsiveGrid.'" />');

    expect($html)
        ->toContain('corepine-emoji-panel')
        ->toContain($panelWidth.' rounded-xl')
        ->toContain('grid '.$baseGrid.' '.$responsiveGrid.' gap-1')
        ->and(substr_count($html, $baseGrid))->toBe(2)
        ->and(substr_count($html, $responsiveGrid))->toBe(2);
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
