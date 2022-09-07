<?php

declare(strict_types=1);

namespace Manzadey\LaravelOrchidStorageLog\Providers;

use Illuminate\Support\ServiceProvider;
use Manzadey\LaravelOrchidStorageLog\Console\Commands\InstallCommand;

class FoundationServiceProvider extends ServiceProvider
{
    public function boot() : void
    {
        $this->commands([
            InstallCommand::class,
        ]);

        $this->registerScreens();
    }

    private function registerScreens() : void
    {
        $this->publishes([
            __DIR__ . '/../../stubs/app/Orchid/Screens' => app_path('Orchid/Screens'),
            __DIR__ . '/../../stubs/routes'             => base_path('routes/platform'),
        ], 'orchid-storage-log-screens');
    }
}
