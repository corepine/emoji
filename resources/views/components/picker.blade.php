@props([
    'target' => null,
    'trigger' => true,
    'placeholder' => 'Search emoji',
    'recentLabel' => 'Recent',
    'dusk' => 'emoji-picker',
    'embedded' => null,
    'columns' => null,
    'wrapperClass' => null,
])

@php
    $emojiData = app(\Corepine\Emoji\EmojiData::class);
    $categories = $emojiData->categories();
    $wireModel = $attributes->wire('model')->value();
    $recentLimit = (int) config('corepine-emoji.recent_limit', 24);
    $recentStorageKey = (string) config('corepine-emoji.storage.recent', 'corepine.emoji.recent');
    $isEmbedded = $embedded === null ? ! $trigger : filter_var($embedded, FILTER_VALIDATE_BOOLEAN);
    $columnCount = max(1, (int) ($columns ?? config('corepine-emoji.columns', 8)));
    $panelStyle = trim("--corepine-emoji-columns: {$columnCount}; ".(string) $attributes->get('style'));
    $classTokens = preg_split('/\s+/', trim((string) $attributes->get('class'))) ?: [];
    $gridColumnClasses = [];
    $panelClassTokens = [];

    foreach ($classTokens as $classToken) {
        if (preg_match('/(?:^|:)grid-cols-(?:\d+|\[[^\]]+\])$/', $classToken) === 1) {
            $gridColumnClasses[] = $classToken;

            continue;
        }

        $panelClassTokens[] = $classToken;
    }

    $panelClass = trim(implode(' ', $panelClassTokens));
    $emojiGridClass = $gridColumnClasses === []
        ? 'corepine-emoji-grid'
        : 'grid '.implode(' ', $gridColumnClasses);
@endphp

<div
    class="{{ trim('corepine-emoji relative inline-flex '.$wrapperClass) }}"
    x-data="corepineEmojiPicker({
        categories: @js($categories),
        target: @js($target),
        wireModel: @js($wireModel),
        recentLimit: @js($recentLimit),
        recentStorageKey: @js($recentStorageKey),
    })"
    x-on:keydown.escape.stop="open = false"
    data-corepine-emoji
>
    @if($trigger)
        <button
            type="button"
            class="corepine-emoji-trigger inline-flex size-9 items-center justify-center rounded-full text-zinc-600 transition hover:bg-zinc-100 hover:text-zinc-950 dark:text-zinc-300 dark:hover:bg-zinc-800 dark:hover:text-white"
            x-ref="trigger"
            x-on:click="toggle()"
            x-bind:aria-expanded="open.toString()"
            aria-haspopup="dialog"
            dusk="emoji-trigger-button"
        >
            <span class="text-xl leading-none">☺</span>
        </button>
    @endif

    <section
        x-cloak
        x-show="{{ $trigger ? 'open' : 'true' }}"
        x-transition:enter="transition ease-out duration-150"
        x-transition:enter-start="opacity-0 translate-y-2 scale-95"
        x-transition:enter-end="opacity-100 translate-y-0 scale-100"
        x-transition:leave="transition ease-in duration-100"
        x-transition:leave-start="opacity-100 translate-y-0 scale-100"
        x-transition:leave-end="opacity-0 translate-y-2 scale-95"
        x-on:click.outside="{{ $trigger ? 'open = false' : '' }}"
        @if($trigger)
            x-anchor.offset.10="$refs.trigger"
        @endif
        {{ $attributes->except(['wire:model', 'style', 'class'])->class([
            'corepine-emoji-panel flex h-[31rem] w-[min(28rem,calc(100vw-2rem))] flex-col overflow-hidden rounded-2xl border border-zinc-200 bg-white text-zinc-950 shadow-xl dark:border-zinc-700 dark:bg-zinc-900 dark:text-white',
            'absolute bottom-full left-0 z-50 mb-3' => $trigger,
            'h-full w-full shadow-none' => $isEmbedded,
            $panelClass,
        ]) }}
        style="{{ $panelStyle }}"
        role="dialog"
        aria-label="Emoji picker"
        dusk="{{ $dusk }}"
    >
        <nav class="flex shrink-0 items-center justify-between gap-1 border-b border-zinc-100 px-4 pt-3 dark:border-zinc-800" aria-label="Emoji categories">
            <template x-for="category in categories" :key="category.key">
                <button
                    type="button"
                    class="relative inline-flex h-10 min-w-10 items-center justify-center rounded-lg text-xl text-zinc-500 transition hover:bg-zinc-100 hover:text-zinc-950 dark:text-zinc-400 dark:hover:bg-zinc-800 dark:hover:text-white"
                    x-bind:class="{ 'text-zinc-950 dark:text-white': activeCategory === category.key }"
                    x-on:click="setCategory(category.key)"
                    x-bind:aria-label="category.label"
                >
                    <span x-text="category.icon"></span>
                    <span
                        class="absolute inset-x-2 bottom-0 h-1 rounded-full bg-emerald-500"
                        x-show="activeCategory === category.key"
                    ></span>
                </button>
            </template>
        </nav>

        <div class="shrink-0 p-4 pb-2">
            <label class="flex h-11 items-center gap-3 rounded-full border-2 border-emerald-500 bg-white px-4 text-zinc-500 focus-within:border-emerald-600 dark:bg-zinc-900 dark:text-zinc-400">
                <span class="text-xl">⌕</span>
                <input
                    type="search"
                    class="h-full min-w-0 flex-1 border-0 bg-transparent p-0 text-base text-zinc-900 outline-none placeholder:text-zinc-400 focus:ring-0 dark:text-white dark:placeholder:text-zinc-500"
                    x-model.debounce.120ms="search"
                    placeholder="{{ $placeholder }}"
                    autocomplete="off"
                >
            </label>
        </div>

        <div class="corepine-emoji-scroll scrollbar-thin min-h-0 flex-1 overflow-y-auto px-4 pb-4">
            <template x-if="recent.length > 0 && !search">
                <section class="mb-5">
                    <h3 class="mb-3 text-sm font-semibold text-zinc-500 dark:text-zinc-400">{{ $recentLabel }}</h3>
                    <div class="{{ $emojiGridClass }} gap-1">
                        <template x-for="emoji in recent" :key="`recent-${emoji}`">
                            <button
                                type="button"
                                class="inline-flex aspect-square items-center justify-center rounded-lg text-2xl transition hover:bg-zinc-100 focus:bg-zinc-100 focus:outline-none dark:hover:bg-zinc-800 dark:focus:bg-zinc-800"
                                x-on:click="select({ emoji, label: emoji, tags: [] })"
                                x-text="emoji"
                            ></button>
                        </template>
                    </div>
                </section>
            </template>

            <template x-for="category in visibleCategories()" :key="category.key">
                <section class="mb-5" x-show="category.emojis.length">
                    <h3 class="mb-3 text-sm font-semibold text-zinc-500 dark:text-zinc-400" x-text="category.label"></h3>
                    <div class="{{ $emojiGridClass }} gap-1">
                        <template x-for="item in category.emojis" :key="`${category.key}-${item.emoji}-${item.label}`">
                            <button
                                type="button"
                                class="inline-flex aspect-square items-center justify-center rounded-lg text-2xl transition hover:bg-zinc-100 focus:bg-zinc-100 focus:outline-none dark:hover:bg-zinc-800 dark:focus:bg-zinc-800"
                                x-bind:aria-label="item.label"
                                x-on:click="select(item)"
                                x-text="item.emoji"
                            ></button>
                        </template>
                    </div>
                </section>
            </template>
        </div>
    </section>
</div>

@once
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('corepineEmojiPicker', (options = {}) => ({
                open: false,
                search: '',
                categories: options.categories ?? [],
                target: options.target ?? null,
                wireModel: options.wireModel ?? null,
                recentLimit: options.recentLimit ?? 24,
                recentStorageKey: options.recentStorageKey ?? 'corepine.emoji.recent',
                recent: [],
                activeCategory: null,

                init() {
                    this.activeCategory = this.categories[0]?.key ?? null;
                    this.recent = this.readRecent();
                },

                toggle() {
                    this.open = !this.open;
                },

                setCategory(key) {
                    this.activeCategory = key;
                    this.search = '';
                },

                visibleCategories() {
                    const query = this.search.trim().toLowerCase();
                    const source = query ? this.categories : this.categories.filter((category) => category.key === this.activeCategory);

                    if (!query) {
                        return source;
                    }

                    return source
                        .map((category) => ({
                            ...category,
                            emojis: category.emojis.filter((item) => {
                                const label = item.label?.toLowerCase() ?? '';
                                const tags = (item.tags ?? []).join(' ').toLowerCase();

                                return label.includes(query) || tags.includes(query) || item.emoji === query;
                            }),
                        }))
                        .filter((category) => category.emojis.length > 0);
                },

                select(item) {
                    this.storeRecent(item.emoji);
                    this.insertEmoji(item.emoji);
                    this.$dispatch('corepine-emoji:selected', {
                        emoji: item.emoji,
                        label: item.label,
                        tags: item.tags ?? [],
                    });
                    this.open = false;
                },

                insertEmoji(emoji) {
                    const input = this.target ? document.getElementById(this.target) : null;

                    if (input && typeof input.selectionStart === 'number') {
                        const start = input.selectionStart;
                        const end = input.selectionEnd;
                        const value = input.value ?? '';
                        const next = value.substring(0, start) + emoji + value.substring(end);

                        input.value = next;
                        input.dispatchEvent(new Event('input', { bubbles: true }));
                        input.focus({ preventScroll: true });
                        input.setSelectionRange(start + emoji.length, start + emoji.length);

                        if (input._x_model) {
                            input._x_model.set(next);
                        }
                    }

                    if (this.wireModel && this.$wire) {
                        const value = input ? input.value : `${this.$wire.get(this.wireModel) ?? ''}${emoji}`;
                        this.$wire.set(this.wireModel, value);
                    }
                },

                readRecent() {
                    try {
                        const value = JSON.parse(localStorage.getItem(this.recentStorageKey) || '[]');

                        return Array.isArray(value) ? value.slice(0, this.recentLimit) : [];
                    } catch (error) {
                        return [];
                    }
                },

                storeRecent(emoji) {
                    this.recent = [emoji, ...this.recent.filter((item) => item !== emoji)].slice(0, this.recentLimit);
                    localStorage.setItem(this.recentStorageKey, JSON.stringify(this.recent));
                },
            }));
        });
    </script>
@endonce
