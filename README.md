# Laravel Orchid Storage Logs Screen

[![Latest Version on Packagist](https://img.shields.io/packagist/v/manzadey/laravel-orchid-storage-logs.svg?style=flat-square)](https://packagist.org/packages/manzadey/laravel-orchid-storage-logs)
[![Total Downloads](https://img.shields.io/packagist/dt/manzadey/laravel-orchid-storage-logs.svg?style=flat-square)](https://packagist.org/packages/manzadey/laravel-orchid-storage-logs)

This package allows you to view logs directly in the Laravel Orchid admin panel.

### List of logs
![Screenshot List Logs](https://user-images.githubusercontent.com/34869211/225702577-92d0589f-6d01-48b5-8916-16d69918a331.png)

### Show Log
![Screenshot Show Log](https://user-images.githubusercontent.com/34869211/225709127-47744ad5-9767-4338-be19-e9f400c250f2.png)
## Features:
 - Download logs
 - Views logs
 - Delete logs
 - Sorting logs by name, date, size
 - Filtering logs by name
 - Sorting log messages by date, environment, type
 - Filtering log messages by environment, type, message

## Installation

You can install the package via composer:

```bash
composer require manzadey/laravel-orchid-storage-logs
```

## Usage
Optional publish config:
```bash
php artisan vendor:publish --tag=storage-logs-config
```

Publish routes:
```bash
php artisan vendor:publish --tag=storage-logs-routes
```
Or add yourself to the file platform.php
```php
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
```

Add a menu item to the method `registerMainMenu()` in the `PlatformProvider` to access logs:
```php
use Manzadey\LaravelOrchidStorageLogs\Screen\Actions\StorageLogsMenu;

class PlatformProvider extends OrchidServiceProvider
{
    /**
     * @return Menu[]
     */
    public function registerMainMenu(): array
    {
        return [
            // Menu items
            
            StorageLogsMenu::make()
                ->route('platform.storage-logs.list'),
]       ;
    }
}
```

### Testing

```bash
composer test
```

### Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

### Security

If you discover any security related issues, please email andrey.manzadey@gmail.com instead of using the issue tracker.

## Credits

-   [Andrey Manzadey](https://github.com/manzadey)
-   [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
