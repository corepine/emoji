<?php

use Illuminate\Foundation\Application;

$APP_BASE_PATH = dirname(__DIR__);

return Application::configure(basePath: $APP_BASE_PATH ?? default_skeleton_path())
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
    )
    ->create();
