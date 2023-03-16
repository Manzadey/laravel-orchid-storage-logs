<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Manzadey\LaravelOrchidStorageLogs\Screens as StorageLogScreens;
use Tabuna\Breadcrumbs\Trail;

Route::name('platform.storage-logs.')
    ->prefix('storage-logs')
    ->group(static function() {
        Route::screen('', StorageLogScreens\StorageLogListScreen::class)
            ->name('list')
            ->breadcrumbs(static fn(Trail $trail) : Trail => $trail
                ->parent('platform.index')
                ->push(__('Storage Logs'), route('platform.storage-logs.list'))
            );

        Route::screen('{storageLog}', StorageLogScreens\StorageLogShowScreen::class)
            ->name('show')
            ->breadcrumbs(static fn(Trail $trail, string $storageLog) : Trail => $trail
                ->parent('platform.storage-logs.list')
                ->push($storageLog, route('platform.storage-logs.show', $storageLog))
            );
    });
