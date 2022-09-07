<?php

declare(strict_types=1);

use App\Orchid\Screens\StorageLogScreen;
use Illuminate\Support\Facades\Route;
use Tabuna\Breadcrumbs\Trail;

Route::screen('storage-logs', StorageLogScreen::class)
    ->name('storage-logs')
    ->breadcrumbs(static fn(Trail $trail) : Trail => $trail
        ->parent('platform.index')
        ->push(__('Логи'), route('platform.storage-logs'))
    );
