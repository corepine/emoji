@php
    $distPath = dirname((new \ReflectionClass(\Corepine\Emoji\EmojiServiceProvider::class))->getFileName()).'/../dist/corepine-emoji.css';
@endphp

@once
    @if(\Illuminate\Support\Facades\File::exists($distPath))
        <style data-corepine-emoji-styles>
            {!! \Illuminate\Support\Facades\File::get($distPath) !!}
        </style>
    @endif
@endonce
