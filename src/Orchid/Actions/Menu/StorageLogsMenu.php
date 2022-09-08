<?php

declare(strict_types=1);

namespace Manzadey\LaravelOrchidStorageLog\Orchid\Actions\Menu;

use Orchid\Screen\Actions\Menu;

class StorageLogsMenu
{
    public static function make() : Menu
    {
        return Menu::make(__('Системные логи'))
            ->icon('settings')
            ->route('platform.storage-logs');
    }
}
