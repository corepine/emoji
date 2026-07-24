<?php

namespace Corepine\Emoji;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class EmojiServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/corepine-emoji.php', 'corepine-emoji');

        $this->app->singleton(EmojiData::class, static fn (): EmojiData => new EmojiData);
    }

    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'corepine-emoji');

        Blade::component('corepine-emoji::components.styles', 'corepine.emoji.styles');
        Blade::component('corepine-emoji::components.styles', 'corepine.emoji.assets');
        Blade::component('corepine-emoji::components.picker', 'corepine.emoji');
        Blade::component('corepine-emoji::components.reaction', 'corepine.emoji.reaction');

        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/corepine-emoji.php' => config_path('corepine-emoji.php'),
            ], 'corepine-emoji-config');

            $this->publishes([
                __DIR__.'/../resources/views' => resource_path('views/vendor/corepine-emoji'),
            ], 'corepine-emoji-views');

            $this->publishes([
                __DIR__.'/../resources/css/app.css' => resource_path('css/vendor/corepine-emoji.css'),
            ], 'corepine-emoji-styles');

            $this->publishes([
                __DIR__.'/../resources/css/app.css' => resource_path('css/vendor/corepine-emoji.css'),
            ], 'corepine-emoji-assets');
        }
    }
}
