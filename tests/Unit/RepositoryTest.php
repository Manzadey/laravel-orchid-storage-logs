<?php

declare(strict_types=1);

namespace Manzadey\LaravelOrchidStorageLogs\Tests\Unit;

use Manzadey\LaravelOrchidStorageLogs\Repositories\StorageLogRepository;
use Manzadey\LaravelOrchidStorageLogs\Tests\TestCase;

class RepositoryTest extends TestCase
{
    public function test_common() : void
    {
        $filepath = 'test.log';

        $repository = new StorageLogRepository($filepath);

        $this->assertEquals(config('filesystems.disks.logs.root') . DIRECTORY_SEPARATOR . $filepath, $repository->get('path'));
        $this->assertEquals($filepath, $repository->get('name'));

        $this->assertArrayHasKey('path', $repository->all());
        $this->assertArrayHasKey('name', $repository->all());
        $this->assertArrayHasKey('size', $repository->all());
        $this->assertArrayHasKey('size_human', $repository->all());
        $this->assertArrayHasKey('last_modified', $repository->all());
        $this->assertArrayHasKey('last_modified_human', $repository->all());
        $this->assertArrayHasKey('last_modified_human_sort', $repository->all());
        $this->assertArrayHasKey('size_human_sort', $repository->all());
        $this->assertArrayHasKey('name_sort', $repository->all());
    }
}
