<?php

declare(strict_types=1);

namespace Manzadey\LaravelOrchidStorageLogs\Screen\Actions;

use Orchid\Screen\Actions\Menu;

class StorageLogsMenu
{
    public static function make() : Menu
    {
        return Menu::make(__('Storage Logs'))
            ->icon('book-open');
    }
}
