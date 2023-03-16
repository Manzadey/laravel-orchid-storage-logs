<?php

declare(strict_types=1);

namespace Manzadey\LaravelOrchidStorageLogs\Screens;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Manzadey\LaravelOrchidStorageLogs\Services\Helpers;
use Orchid\Screen\Screen;
use Orchid\Support\Facades\Alert;
use Orchid\Support\Facades\Toast;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Tabuna\Breadcrumbs\Breadcrumbs;

abstract class AbstractStorageLogScreen extends Screen
{
    public const SEPARATOR_FOR_FILES = '___';

    public function name() : ?string
    {
        return Breadcrumbs::current()->last()->title();
    }

    public function download(Request $request) : BinaryFileResponse|false
    {
        $file = $request->input('file');

        if(Helpers::getStorageDisk()->exists($file)) {
            return response()
                ->download(
                    file: Helpers::getStorageDisk()
                        ->path(str_replace(DIRECTORY_SEPARATOR, '\\', $file)),
                    name: config('app.name') . '_' . str_replace('/', '__', $file),
                );
        }

        Toast::error(__('Error when uploading a file'));

        return false;
    }

    public function delete(Request $request) : RedirectResponse
    {
        $file = $request->input('file');

        if(Helpers::getStorageDisk()->exists($file)) {
            $deleted = Helpers::getStorageDisk()->delete($file);

            if($deleted) {
                Alert::success(sprintf(__('The %s file was successfully deleted'), $file));

                return redirect()->route('platform.storage-logs.list');
            }
        }

        Toast::error(sprintf(__('The %s file has not been deleted'), $file));

        return back();
    }
}
