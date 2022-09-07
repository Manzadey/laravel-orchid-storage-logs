<?php

declare(strict_types=1);

namespace App\Orchid\Screens;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Fields\Group;
use Orchid\Screen\Fields\Select;
use Orchid\Screen\Fields\TextArea;
use Orchid\Screen\Screen;
use Orchid\Support\Color;
use Orchid\Support\Facades\Layout;

class StorageLogScreen extends Screen
{
    private array $logs = [];

    /**
     * Query data.
     *
     * @return array
     */
    public function query(Request $request) : iterable
    {
        $this->logs = collect(Storage::disk('logs')->allFiles())
            ->filter(static fn(string $log) => str_contains($log, '.log'))
            ->values()
            ->toArray();

        $data = '';
        if($request->filled('log') && isset($this->logs[$request->input('log')])) {
            $file = $this->logs[$request->input('log')];

            $data = Storage::disk('logs')->get($file);
        }

        return compact('data');
    }

    /**
     * Views.
     *
     * @return \Orchid\Screen\Layout[]|string[]
     */
    public function layout() : iterable
    {
        return [
            Layout::rows([
                Group::make([
                    Select::make('log')
                        ->title(__('Выберите лог'))
                        ->options($this->logs)
                        ->empty()
                        ->value((int) request('log'))
                        ->required(),
                    Button::make(__('Показать содержимое'))
                        ->type(Color::DEFAULT())
                        ->method('getLog'),
                ])->alignEnd(),
            ]),

            Layout::rows([
                TextArea::make('data')
                    ->rows(50)
                    ->class('form-control no-resize')
                    ->style('max-width:100%'),
            ]),
        ];
    }

    public function getLog(Request $request) : RedirectResponse
    {
        return to_route('platform.storage-logs', [
            'log' => $request->input('log'),
        ]);
    }
}
