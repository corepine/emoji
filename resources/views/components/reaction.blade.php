@props([
    'quick' => null,
    'target' => null,
    'placeholder' => 'Search reaction',
])

@php
    $emojiData = app(\Corepine\Emoji\EmojiData::class);
    $quickReactions = is_array($quick) ? $quick : $emojiData->quickReactions();
    $reactionStorageKey = (string) config('corepine-emoji.storage.reactions', 'corepine.emoji.reactions');
@endphp

<div
    {{ $attributes->merge(['class' => 'corepine-emoji-reaction relative inline-flex']) }}
    x-data="{
        open: false,
        quick: @js($quickReactions),
        storageKey: @js($reactionStorageKey),
        choose(emoji) {
            this.store(emoji);
            this.$dispatch('corepine-emoji:reaction-selected', { emoji });
            this.open = false;
        },
        store(emoji) {
            try {
                const current = JSON.parse(localStorage.getItem(this.storageKey) || '[]');
                const next = [emoji, ...current.filter((item) => item !== emoji)].slice(0, 24);
                localStorage.setItem(this.storageKey, JSON.stringify(next));
            } catch (error) {}
        },
    }"
    x-on:corepine-emoji:selected="choose($event.detail.emoji)"
    x-on:keydown.escape.stop="open = false"
    data-corepine-emoji-reaction
>
    <div class="corepine-emoji-panel flex items-center gap-2 rounded-full border border-zinc-200 bg-white px-3 py-2 shadow-lg dark:border-zinc-700 dark:bg-zinc-900">
        <template x-for="emoji in quick" :key="emoji">
            <button
                type="button"
                class="inline-flex size-9 items-center justify-center rounded-full text-2xl transition hover:bg-zinc-100 dark:hover:bg-zinc-800"
                x-on:click="choose(emoji)"
                x-text="emoji"
            ></button>
        </template>

        <button
            type="button"
            class="inline-flex size-9 items-center justify-center rounded-full text-2xl font-semibold text-zinc-800 transition hover:bg-zinc-100 dark:text-zinc-100 dark:hover:bg-zinc-800"
            x-ref="moreButton"
            x-on:click="open = !open"
            aria-label="More reactions"
        >
            +
        </button>
    </div>

    <section
        x-cloak
        x-show="open"
        x-transition:enter="transition ease-out duration-150"
        x-transition:enter-start="opacity-0 translate-y-2 scale-95"
        x-transition:enter-end="opacity-100 translate-y-0 scale-100"
        x-transition:leave="transition ease-in duration-100"
        x-transition:leave-start="opacity-100 translate-y-0 scale-100"
        x-transition:leave-end="opacity-0 translate-y-2 scale-95"
        x-on:click.outside="open = false"
        x-anchor.offset.10="$refs.moreButton"
        class="absolute bottom-full left-0 z-50 mb-3"
        dusk="emoji-reaction-picker"
    >
        <x-corepine.emoji :target="$target" :trigger="false" :embedded="false" :placeholder="$placeholder" recent-label="Recent reactions" />
    </section>
</div>
