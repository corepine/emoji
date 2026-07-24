<?php

namespace Corepine\Emoji\Tests;

use Corepine\Emoji\EmojiServiceProvider;
use Corepine\Support\SupportServiceProvider;
use Livewire\LivewireServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    protected function getPackageProviders($app): array
    {
        return [
            SupportServiceProvider::class,
            LivewireServiceProvider::class,
            EmojiServiceProvider::class,
        ];
    }

    protected function defineEnvironment($app): void
    {
        $app['config']->set('app.key', 'base64:2fl+Ktvkfl+Fuz4Qp/A75G2RTiWVA/ZoKZvp6fiiM10=');
    }
}
