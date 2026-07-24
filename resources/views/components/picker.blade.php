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
    $attributeArray = $attributes->getAttributes();
    $ignoredModelBindingKeys = [];

    foreach ($attributeArray as $attributeName => $attributeValue) {
        if (preg_match('/^wire:model(?:\.|$)/', (string) $attributeName) === 1) {
            $ignoredModelBindingKeys[] = (string) $attributeName;
        }
    }

    $emojiData = app(\Corepine\Emoji\EmojiData::class);
    $categories = $emojiData->categories();
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
    $panelAttributes = new \Illuminate\View\ComponentAttributeBag(
        collect($attributeArray)->except(array_merge($ignoredModelBindingKeys, ['style', 'class']))->all()
    );
@endphp

<div
    class="{{ trim('corepine-emoji relative inline-flex '.$wrapperClass) }}"
    x-data="corepineEmojiPicker({
        categories: @js($categories),
        target: @js($target),
        recentLimit: @js($recentLimit),
        recentStorageKey: @js($recentStorageKey),
    })"
    x-on:keydown.escape.stop="open = false"
    data-corepine-emoji
>
    @if($trigger)
        @if($slot->isEmpty())
            <button
                type="button"
                class="corepine-emoji-trigger inline-flex size-9 items-center justify-center rounded-full text-zinc-600 transition hover:bg-zinc-100 hover:text-zinc-950 dark:text-zinc-300 dark:hover:bg-zinc-800 dark:hover:text-white"
                x-ref="trigger"
                x-on:click="toggle()"
                x-bind:aria-expanded="open.toString()"
                aria-haspopup="dialog"
                dusk="emoji-trigger-button"
            >
                <span class="text-xl leading-none">
                    <svg
                        x-bind:style="open ? { color: 'var(--corepine-emoji-accent, var(--wc-brand-primary, #16a34a))' } : null"
                        viewBox="0 0 24 24"
                        height="24"
                        width="24"
                        preserveAspectRatio="xMidYMid meet"
                        class="h-6 w-6 text-gray-600 dark:text-gray-300"
                        version="1.1"
                        x="0px"
                        y="0px"
                        enable-background="new 0 0 24 24"
                    >
                        <title>smiley</title>
                        <path
                            fill="currentColor"
                            d="M9.153,11.603c0.795,0,1.439-0.879,1.439-1.962S9.948,7.679,9.153,7.679 S7.714,8.558,7.714,9.641S8.358,11.603,9.153,11.603z M5.949,12.965c-0.026-0.307-0.131,5.218,6.063,5.551 c6.066-0.25,6.066-5.551,6.066-5.551C12,14.381,5.949,12.965,5.949,12.965z M17.312,14.073c0,0-0.669,1.959-5.051,1.959 c-3.505,0-5.388-1.164-5.607-1.959C6.654,14.073,12.566,15.128,17.312,14.073z M11.804,1.011c-6.195,0-10.826,5.022-10.826,11.217 s4.826,10.761,11.021,10.761S23.02,18.423,23.02,12.228C23.021,6.033,17.999,1.011,11.804,1.011z M12,21.354 c-5.273,0-9.381-3.886-9.381-9.159s3.942-9.548,9.215-9.548s9.548,4.275,9.548,9.548C21.381,17.467,17.273,21.354,12,21.354z  M15.108,11.603c0.795,0,1.439-0.879,1.439-1.962s-0.644-1.962-1.439-1.962s-1.439,0.879-1.439,1.962S14.313,11.603,15.108,11.603z"
                        ></path>
                    </svg>
                </span>
            </button>
        @else
            <span
                class="corepine-emoji-trigger inline-flex"
                x-ref="trigger"
                x-on:click="toggle()"
                x-bind:aria-expanded="open.toString()"
                aria-haspopup="dialog"
                dusk="emoji-trigger-button"
            >
                {{ $slot }}
            </span>
        @endif
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
            x-anchor.fixed.offset.10="$refs.trigger"
        @endif
        {{ $panelAttributes->class([
            'corepine-emoji-panel flex h-[31rem] w-[min(28rem,calc(100vw-2rem))] flex-col overflow-hidden rounded-2xl border border-zinc-200 bg-white text-zinc-950 shadow-xl dark:border-zinc-700 dark:bg-zinc-900 dark:text-white',
            'z-50' => $trigger,
            'h-full w-full shadow-none' => $isEmbedded,
            $panelClass,
        ]) }}
        style="{{ $panelStyle }}"
        role="dialog"
        aria-label="Emoji picker"
        dusk="{{ $dusk }}"
    >
        <nav class="flex shrink-0 items-center justify-between gap-1 border-b border-zinc-100 px-4 pt-3 dark:border-zinc-800" aria-label="Emoji categories">
            <template x-for="item in navigationItems()" :key="item.key">
                <button
                    type="button"
                    class="relative inline-flex h-10 min-w-10 items-center justify-center rounded-lg text-zinc-500 transition hover:bg-zinc-100 hover:text-zinc-950 dark:text-zinc-400 dark:hover:bg-zinc-800 dark:hover:text-white"
                    x-bind:class="{ 'text-zinc-950 dark:text-white': activeCategory === item.key }"
                    x-on:click="scrollToSection(item.key)"
                    x-bind:aria-label="item.label"
                >
                    <span class="inline-flex size-7 items-center justify-center" x-html="categoryIconSvg(item.key)"></span>
                    <span
                        class="absolute inset-x-2 bottom-0 h-1 rounded-full bg-emerald-500"
                        x-show="activeCategory === item.key"
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

        <div
            class="corepine-emoji-scroll scrollbar-thin min-h-0 flex-1 overflow-y-auto px-4 pb-4"
            x-ref="scroll"
            x-on:scroll.throttle.100ms="syncActiveCategory()"
        >
            <template x-if="recent.length > 0 && !search">
                <section class="mb-5" data-corepine-emoji-section="recent">
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
                <section class="mb-5" x-show="category.emojis.length" x-bind:data-corepine-emoji-section="category.key">
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
                recentLimit: options.recentLimit ?? 24,
                recentStorageKey: options.recentStorageKey ?? 'corepine.emoji.recent',
                recent: [],
                activeCategory: null,
                categoryIcons: {
                    'recent': '<svg viewBox="0 0 24 24" class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><title>recent</title><circle cx="12" cy="12" r="8.25"></circle><path d="M12 7.5V12l3 2"></path></svg>',
                    'smileys-people': '<svg viewBox="0 0 24 24" class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><title>smiley</title><circle cx="12" cy="12" r="8.25"></circle><path d="M9 10.25h.01"></path><path d="M15 10.25h.01"></path><path d="M8.75 14.25c.85 1 1.95 1.5 3.25 1.5s2.4-.5 3.25-1.5"></path></svg>',
                    'animals-nature': '<svg viewBox="0 0 24 24" class="h-6.5 w-6.5" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M7.5 10.5 5 7.5C4.4 6.8 4.8 5.8 5.7 5.7l3.9-.5"/><path d="m16.5 10.5 2.5-3c.6-.7.2-1.7-.7-1.8l-3.9-.5"/><path d="M6.5 13.5c0-3.3 2.4-5.8 5.5-5.8s5.5 2.5 5.5 5.8c0 3-2.3 5.2-5.5 5.2s-5.5-2.2-5.5-5.2Z"/><path d="M9.5 13h.01"/><path d="M14.5 13h.01"/><path d="M11 15.5h2"/></svg>',
                    'food-drink': '<svg viewBox="0 0 24 24" class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M6 3v8"/><path d="M10 3v8"/><path d="M8 3v18"/><path d="M15 3v18"/><path d="M15 3c2.2 1.2 3.5 3.2 3.5 5.8 0 2.2-1.2 3.7-3.5 4.2"/></svg>',
                    'travel-places': '<svg viewBox="0 0 24 24" class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M3.5 12h17"/><path d="M12 3.5c2.5 2.3 3.8 5.1 3.8 8.5S14.5 18.2 12 20.5"/><path d="M12 3.5C9.5 5.8 8.2 8.6 8.2 12s1.3 6.2 3.8 8.5"/><circle cx="12" cy="12" r="8.5"/></svg>',
                    'activities': '<svg viewBox="0 0 24 24" class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="8.5"/><path d="M5.2 8.3c3.5.8 6.3 3.6 7.1 7.1"/><path d="M18.8 15.7c-3.5-.8-6.3-3.6-7.1-7.1"/><path d="M8.3 18.8c.8-3.5 3.6-6.3 7.1-7.1"/><path d="M15.7 5.2c-.8 3.5-3.6 6.3-7.1 7.1"/></svg>',
                    'objects': '<svg viewBox="0 0 24 24" class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M9 18h6"/><path d="M10 22h4"/><path d="M8.5 14.5c-1.4-1.1-2.3-2.8-2.3-4.7a5.8 5.8 0 1 1 9.3 4.7c-.8.6-1.1 1.4-1.1 2.5H9.6c0-1.1-.3-1.9-1.1-2.5Z"/></svg>',
                    'symbols': '<svg viewBox="0 0 24 24" class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M7 4v16"/><path d="M17 4v16"/><path d="M4 9h16"/><path d="M3 15h16"/></svg>',
                    'flags': '<svg viewBox="0 0 24 24" class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M6 21V4"/><path d="M6 4h10.5l-1.4 3 1.4 3H6"/></svg>',
                },

                init() {
                    this.recent = this.readRecent();
                    this.activeCategory = this.recent.length > 0 ? 'recent' : this.categories[0]?.key ?? null;
                    this.$nextTick(() => this.syncActiveCategory());
                },

                toggle() {
                    this.open = !this.open;
                },

                scrollToSection(key) {
                    this.activeCategory = key;
                    this.search = '';
                    this.$nextTick(() => {
                        const section = this.$root.querySelector(`[data-corepine-emoji-section="${key}"]`);

                        if (!section || !this.$refs.scroll) {
                            return;
                        }

                        this.$refs.scroll.scrollTop = section.offsetTop - this.$refs.scroll.offsetTop;
                        this.syncActiveCategory();
                    });
                },

                categoryIconSvg(key) {
                    return this.categoryIcons[key] ?? this.categoryIcons['smileys-people'];
                },

                navigationItems() {
                    const items = this.search ? [] : this.categories.map((category) => ({
                        key: category.key,
                        label: category.label,
                    }));

                    if (!this.search && this.recent.length > 0) {
                        return [{ key: 'recent', label: @js($recentLabel) }, ...items];
                    }

                    return items;
                },

                syncActiveCategory() {
                    if (this.search || !this.$refs.scroll) {
                        return;
                    }

                    const containerTop = this.$refs.scroll.getBoundingClientRect().top;
                    const sections = Array.from(this.$refs.scroll.querySelectorAll('[data-corepine-emoji-section]'));
                    let active = this.recent.length > 0 ? 'recent' : this.categories[0]?.key ?? null;

                    for (const section of sections) {
                        if (section.getBoundingClientRect().top <= containerTop + 20) {
                            active = section.dataset.corepineEmojiSection;
                        }
                    }

                    this.activeCategory = active;
                },

                visibleCategories() {
                    const query = this.search.trim().toLowerCase();
                    const source = this.categories;

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
                    const input = this.resolveInput();

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
                },

                resolveInput() {
                    if (!this.target) {
                        return null;
                    }

                    return document.getElementById(this.target) || this.findScopedInput();
                },

                findScopedInput() {
                    const scopes = [
                        this.$root.closest('form'),
                        this.$root.parentElement,
                    ].filter(Boolean);
                    const uniqueScopes = [...new Set(scopes)];

                    for (const scope of uniqueScopes) {
                        const candidates = Array.from(scope.querySelectorAll('textarea, input'))
                            .filter((element) => this.isEditableInput(element) && !this.$root.contains(element));

                        const modelCandidate = candidates.find((element) => this.hasMatchingModelBinding(element));

                        if (modelCandidate) {
                            return modelCandidate;
                        }
                    }

                    return null;
                },

                isEditableInput(element) {
                    if (!(element instanceof HTMLTextAreaElement) && !(element instanceof HTMLInputElement)) {
                        return false;
                    }

                    const ignoredInputTypes = ['button', 'checkbox', 'color', 'file', 'hidden', 'image', 'radio', 'range', 'reset', 'search', 'submit'];

                    return !element.disabled && !element.readOnly && !ignoredInputTypes.includes((element.type || '').toLowerCase());
                },

                hasMatchingModelBinding(element) {
                    return element.getAttributeNames().some((name) => {
                        if (!name.startsWith('wire:model') && !name.startsWith('x-model')) {
                            return false;
                        }

                        return element.getAttribute(name) === this.target;
                    });
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
