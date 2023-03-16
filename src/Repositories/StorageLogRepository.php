<?php

declare(strict_types=1);

namespace Manzadey\LaravelOrchidStorageLogs\Repositories;

use Illuminate\Support\Facades\Date;
use Manzadey\LaravelOrchidStorageLogs\Services\Helpers;
use Orchid\Screen\Repository;

class StorageLogRepository extends Repository
{
    public function __construct(string $storageLog, iterable $items = [])
    {
        $disk = Helpers::getStorageDisk();

        $items = array_merge([
            'path'                     => $disk->path($storageLog),
            'name'                     => $storageLog,
            'size'                     => $size = $disk->size($storageLog),
            'size_human'               => Helpers::humanFilesize($size),
            'last_modified'            => $lastModified = $disk->lastModified($storageLog),
            'last_modified_human'      => Date::parse($lastModified)
                ->timezone(config('app.timezone'))
                ->isoFormat('LLLL'),
            'last_modified_human_sort' => $lastModified,
            'size_human_sort'          => $size,
            'name_sort'                => $storageLog,
        ], $items);

        parent::__construct($items);
    }
}
