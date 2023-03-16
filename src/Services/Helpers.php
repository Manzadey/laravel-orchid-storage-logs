<?php

declare(strict_types=1);

namespace Manzadey\LaravelOrchidStorageLogs\Services;

use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Support\Facades\Storage;
use Manzadey\LaravelOrchidStorageLogs\Screens\AbstractStorageLogScreen;

class Helpers
{
    public static function humanFilesize(int $size, $precision = 2) : string
    {
        $units = ['B', 'kB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'];
        $step  = 1024;
        $i     = 0;
        while (($size / $step) > 0.9) {
            $size /= $step;
            $i++;
        }

        return round($size, $precision) . $units[$i];
    }

    public static function getStorageDisk() : Filesystem
    {
        return Storage::disk(config('storage-logs.disk'));
    }

    public static function filenameEncode(string $name) : string
    {
        return str_replace(['.log', '/'], ['', AbstractStorageLogScreen::SEPARATOR_FOR_FILES], $name);
    }

    public static function filenameDecode(string $filename) : string
    {
        return str_replace(AbstractStorageLogScreen::SEPARATOR_FOR_FILES, DIRECTORY_SEPARATOR, "$filename.log");
    }
}
