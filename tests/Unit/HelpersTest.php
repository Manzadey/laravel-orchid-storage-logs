<?php

declare(strict_types=1);

namespace Manzadey\LaravelOrchidStorageLogs\Tests\Unit;

use Manzadey\LaravelOrchidStorageLogs\Services\Helpers;
use PHPUnit\Framework\TestCase;

class HelpersTest extends TestCase
{
    public function test_human_filesize() : void
    {
        $size = 1024;
        $this->assertEquals('1kB', Helpers::humanFilesize($size));

        $size *= 1024;
        $this->assertEquals('1MB', Helpers::humanFilesize($size));

        $size *= 1024;
        $this->assertEquals('1GB', Helpers::humanFilesize($size));
    }

    public function test_filename_encode_decode() : void
    {
        $filename = 'laravel.log';
        $this->assertEquals('laravel', Helpers::filenameEncode($filename));
        $this->assertEquals($filename, Helpers::filenameDecode('laravel'));

        $filename = 'path' . DIRECTORY_SEPARATOR . 'to' . DIRECTORY_SEPARATOR . 'laravel.log';
        $this->assertEquals('path___to___laravel', Helpers::filenameEncode($filename));
        $this->assertEquals($filename, Helpers::filenameDecode('path___to___laravel'));
    }
}
