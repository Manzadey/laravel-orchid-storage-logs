<?php

declare(strict_types=1);

namespace Manzadey\LaravelOrchidStorageLog\Console\Commands;

use Illuminate\Console\Command;
use Manzadey\LaravelOrchidStorageLog\Providers\FoundationServiceProvider;

class InstallCommand extends Command
{
    protected $signature = 'orchid-storage-logs:install';

    protected $description = 'Install package.';

    public function handle() : int
    {
        $this->call('vendor:publish', [
            '--provider' => FoundationServiceProvider::class,
            '--tag'      => [
                'orchid-storage-log-screens',
            ],
        ]);

        $this->components->info('Package installed!');

        return 1;
    }
}
