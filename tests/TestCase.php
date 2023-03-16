<?php

declare(strict_types=1);

namespace Manzadey\LaravelOrchidStorageLogs\Tests;

use Manzadey\LaravelOrchidStorageLogs\FoundationServiceProvider;

class TestCase extends \Orchestra\Testbench\TestCase
{
    protected function getPackageProviders($app) : array
    {
        return [
            FoundationServiceProvider::class,
        ];
    }

    public function getEnvironmentSetUp($app) : void
    {
        config()->set('filesystems.disks.logs', [
            'driver' => 'local',
            'root'   => __DIR__ . DIRECTORY_SEPARATOR . 'logs',
        ]);
    }
}
