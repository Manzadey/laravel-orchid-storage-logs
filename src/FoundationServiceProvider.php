<?php

declare(strict_types=1);

namespace Manzadey\LaravelOrchidStorageLogs;

use Illuminate\Foundation\Console\AboutCommand;
use Illuminate\Support\ServiceProvider;

class FoundationServiceProvider extends ServiceProvider
{
    private const VENDOR = 'laravel-orchid-storage-logs';

    private const VERSION = '1.0.0';

    public function register() : void
    {
        $this->registerConfig();
    }

    public function boot() : void
    {
        $this->registerViews();
        $this->registerTranslations();
        $this->registerPublishes();

        AboutCommand::add('Laravel Orchid Storage Log Package', static fn() => ['Version' => self::VERSION]);
    }

    public function registerConfig() : void
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/config.php', 'storage-logs');

        app()->config["filesystems.disks.logs"] = [
            'driver' => 'local',
            'root'   => storage_path('logs'),
        ];
    }

    public function registerViews() : void
    {
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'storage-logs');
    }

    /**
     * @return void
     */
    public function registerTranslations() : void
    {
        $this->loadJsonTranslationsFrom(__DIR__ . '/../resources/lang');
    }

    private function registerPublishes() : void
    {
        if($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/config.php' => config_path('storage-logs.php'),
            ], 'storage-logs-config');

            $this->publishes([
                __DIR__ . '/../stubs/routes' => base_path('routes/platform/storage-logs.php'),
            ], 'storage-logs-routes');

            $this->publishes([
                __DIR__ . '/../resources/lang' => $this->app->langPath('vendor/' . self::VENDOR),
            ], 'storage-logs-lang');

            $this->publishes([
                __DIR__ . '/../resources/views' => resource_path('views/vendor/' . self::VENDOR),
            ], 'storage-logs-views');
        }
    }
}

