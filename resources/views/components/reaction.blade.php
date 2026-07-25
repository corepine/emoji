@props([
    'quick' => null,
    'target' => null,
    'placeholder' => 'Search reaction',
    'color' => null,
])

@php
    $emoji = app(\Corepine\Emoji\Emoji::class);
    $quickReactions = is_array($quick) ? $quick : $emoji->quickReactions();
    $reactionStorageKey = $emoji->reactionStorageKey();
    $selectedEvent = $emoji->event()->selected();
    $reactionSelectedEvent = $emoji->event()->reactionSelected();
    $reactionStyle = trim($emoji->colorStyle($color).' '.(string) $attributes->get('style'));
@endphp

<div
    {{ $attributes->except('style')->merge(['class' => 'corepine-emoji-reaction relative inline-flex']) }}
    x-data="{
        open: false,
        pickerOpen: false,
        quick: @js($quickReactions),
        storageKey: @js($reactionStorageKey),
        recentLimit: @js($emoji->recentLimit()),
        selectedEvent: @js($selectedEvent),
        reactionSelectedEvent: @js($reactionSelectedEvent),
        init() {
            this.$el.addEventListener(this.selectedEvent, (event) => {
                this.choose(event.detail.emoji);
            });
        },
        toggle() {
            this.open = !this.open;

            if (! this.open) {
                this.pickerOpen = false;
            }
        },
        togglePicker() {
            this.open = true;
            this.pickerOpen = !this.pickerOpen;
        },
        choose(emoji) {
            this.store(emoji);
            this.$dispatch(this.reactionSelectedEvent, { emoji });
            this.open = false;
            this.pickerOpen = false;
        },
        store(emoji) {
            try {
                const current = JSON.parse(localStorage.getItem(this.storageKey) || '[]');
                const next = [emoji, ...current.filter((item) => item !== emoji)].slice(0, this.recentLimit);
                localStorage.setItem(this.storageKey, JSON.stringify(next));
            } catch (error) {}
        },
    }"
    x-on:keydown.escape.stop="open = false; pickerOpen = false"
    style="{{ $reactionStyle }}"
    data-corepine-emoji-reaction
>
    <button
        type="button"
        class="corepine-emoji-trigger inline-flex size-9 items-center justify-center rounded-full text-zinc-600 transition hover:bg-zinc-100 hover:text-zinc-950 dark:text-zinc-300 dark:hover:bg-zinc-800 dark:hover:text-white"
        x-ref="reactionButton"
        x-on:click="toggle()"
        x-bind:aria-expanded="open.toString()"
        aria-haspopup="dialog"
        aria-label="React"
        dusk="emoji-reaction-trigger"
    >
        <span class="text-xl leading-none">
            <svg
                x-bind:style="open ? { color: 'var(--corepine-emoji-accent)' } : null"
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

    <section
        x-cloak
        x-show="open"
        x-transition:enter="transition ease-out duration-150"
        x-transition:enter-start="opacity-0 translate-y-2 scale-95"
        x-transition:enter-end="opacity-100 translate-y-0 scale-100"
        x-transition:leave="transition ease-in duration-100"
        x-transition:leave-start="opacity-100 translate-y-0 scale-100"
        x-transition:leave-end="opacity-0 translate-y-2 scale-95"
        x-on:click.outside="open = false; pickerOpen = false"
        x-anchor.fixed.top.offset.10="$refs.reactionButton"
        class="z-50"
        dusk="emoji-reaction-strip"
    >
        <div
            x-ref="reactionStrip"
            class="corepine-emoji-panel relative flex items-center gap-2 rounded-full border border-zinc-200 px-3 py-2 shadow-lg dark:border-zinc-700"
        >
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
                x-on:click="togglePicker()"
                x-bind:style="pickerOpen ? { color: 'var(--corepine-emoji-accent)' } : null"
                aria-label="More reactions"
            >
                +
            </button>
        </div>

        <div
            x-cloak
            x-show="pickerOpen"
            x-transition:enter="transition ease-out duration-150"
            x-transition:enter-start="opacity-0 translate-y-2 scale-95"
            x-transition:enter-end="opacity-100 translate-y-0 scale-100"
            x-transition:leave="transition ease-in duration-100"
            x-transition:leave-start="opacity-100 translate-y-0 scale-100"
            x-transition:leave-end="opacity-0 translate-y-2 scale-95"
            x-anchor.fixed.top.offset.10="$refs.reactionStrip"
            class="z-50"
            dusk="emoji-reaction-picker"
        >
            <x-corepine.emoji :target="$target" :trigger="false" :embedded="false" :placeholder="$placeholder" :recent-storage-key="$reactionStorageKey" :color="$color" recent-label="Recent reactions" />
        </div>
    </section>
</div>
