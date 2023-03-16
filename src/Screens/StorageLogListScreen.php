<?php

declare(strict_types=1);

namespace Manzadey\LaravelOrchidStorageLogs\Screens;

use Closure;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Manzadey\LaravelOrchidStorageLogs\Repositories\StorageLogRepository;
use Manzadey\LaravelOrchidStorageLogs\Services\Helpers;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Actions\DropDown;
use Orchid\Screen\Actions\Link;
use Orchid\Screen\Fields\Group;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Select;
use Orchid\Screen\Repository;
use Orchid\Screen\TD;
use Orchid\Support\Color;
use Orchid\Support\Facades\Layout;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use ZipArchive;

class StorageLogListScreen extends AbstractStorageLogScreen
{
    private array $directories;

    public function __construct(
        private readonly Request $request
    )
    {
        $this->directories = Helpers::getStorageDisk()->allDirectories();
    }

    public function query() : iterable
    {
        return [
            'logs'    => collect($this->getFiles())
                ->when(
                    $this->request->filled('filter.name'),
                    fn(Collection $collection) : Collection => $this->filterName($collection),
                )
                ->when(
                    $this->request->filled('filter.content'),
                    fn(Collection $collection) : Collection => $this->filterContent($collection),
                )
                ->map($this->repositoryWrapper())
                ->when(
                    $this->request->filled('sort'),
                    fn(Collection $collection) : Collection => $this->sort($collection),
                    fn(Collection $collection) : Collection => $this->sortDefault($collection),
                ),
            'metrics' => [
                'total_size'  => Helpers::humanFilesize($this->totalSize()),
                'total_count' => count(Helpers::getStorageDisk()->allFiles()),
            ],

            'filter' => [
                'directory' => $this->request->integer('filter.directory'),
                'content'   => $this->request->input('filter.content'),
            ],
        ];
    }

    private function totalSize() : int
    {
        return collect(Helpers::getStorageDisk()->allFiles())
            ->map(fn(string $filepath) => Helpers::getStorageDisk()->size($filepath))
            ->sum();
    }

    private function repositoryWrapper() : Closure
    {
        return static fn(string $filename) => new StorageLogRepository($filename);
    }

    private function getFiles() : array
    {
        return Helpers::getStorageDisk()->files($this->getDirectoryFromRequest());
    }

    private function getDirectoryFromRequest() : ?string
    {
        return $this->directories[$this->request->input('filter.directory')] ?? null;
    }

    private function filterName(Collection $collection) : Collection
    {
        return $collection
            ->filter(fn(string $filename) : bool => str_contains($filename, $this->request->input('filter.name')));
    }

    private function sort(Collection $collection) : Collection
    {
        $sort = sprintf('%s_sort', str_replace('-', '', $this->request->input('sort')));

        return str_contains($this->request->input('sort'), '-') ?
            $collection->sortByDesc($sort) :
            $collection->sortBy($sort);
    }

    public function sortDefault(Collection $collection) : Collection
    {
        return $collection->sortByDesc('last_modified');
    }

    private function filterContent(Collection $collection) : Collection
    {
        return $collection->filter(
            fn(string $filename) => str_contains(
                haystack: Helpers::getStorageDisk()->get($filename),
                needle: $this->request->input('filter.content')
            )
        );
    }

    public function commandBar() : iterable
    {
        return [
            Button::make(__('Download all'))
                ->icon('cloud-download')
                ->type(Color::DEFAULT())
                ->method('downloadAll')
                ->rawClick()
                ->canSee(class_exists('ZipArchive')),
        ];
    }

    /**
     * Views.
     *
     * @return \Orchid\Screen\Layout[]|string[]
     */
    public function layout() : iterable
    {
        return [
            Layout::metrics([
                __('Total size')      => 'metrics.total_size',
                __('Number of files') => 'metrics.total_count',
            ]),

            Layout::rows([
                Group::make([
                    Select::make('filter.directory')
                        ->options($this->directories)
                        ->empty()
                        ->title(__('Directory'))
                        ->help(__('Select a directory'))
                        ->canSee(count($this->directories) > 0)->title(__('Directory')),
                    Input::make('filter.content')
                        ->title(__('Search'))
                        ->help(__('Search for files containing a substring')),
                    Button::make('Apply')
                        ->icon('settings')
                        ->type(Color::DEFAULT())
                        ->method('filter', $this->request->except('_token')),
                ])->alignCenter(),
            ]),

            Layout::table('logs', [
                TD::make('#')
                    ->render(static fn(Repository $repository, object $loop) : int => $loop->iteration)
                    ->alignLeft()
                    ->width('75px'),
                TD::make('name', __('Name'))
                    ->sort()
                    ->filter(),
                TD::make('size_human', __('Size'))
                    ->sort(),
                TD::make('last_modified_human', __('Last update'))
                    ->sort(),
                TD::make()->render(
                    static fn(Repository $repository) => DropDown::make()
                        ->icon('options-vertical')
                        ->list([
                            Link::make(__('More'))
                                ->icon('eye')
                                ->route(
                                    name: 'platform.storage-logs.show',
                                    parameters: Helpers::filenameEncode($repository->get('name'))
                                ),
                            Button::make(__('Download'))
                                ->icon('cloud-download')
                                ->method('download', [
                                    'file' => addslashes($repository->get('name')),
                                ])->rawClick(),
                            Button::make(__('Delete'))
                                ->icon('trash')
                                ->type(Color::DANGER())
                                ->method('delete', [
                                    'file' => addslashes($repository->get('name')),
                                ])
                                ->confirm(sprintf(__('Удалить %s?'), $repository->get('name')))
                                ->rawClick(),
                        ])
                )
                    ->alignRight()
                    ->width('50px'),
            ])->title(sprintf(__('List of files from "%s" folder'), $this->getDirectoryFromRequest() ?? 'main')),
        ];
    }

    public function filter() : RedirectResponse
    {
        return redirect()
            ->route('platform.storage-logs.list', [
                'filter' => $this->request->input('filter'),
            ]);
    }

    public function downloadAll() : BinaryFileResponse
    {
        $zip         = new ZipArchive;
        $zipFileName = tempnam(sys_get_temp_dir(), 'logs_');
        $zip->open($zipFileName, ZipArchive::CREATE | ZipArchive::OVERWRITE);

        foreach (Helpers::getStorageDisk()->allFiles() as $item) {
            $zip->addFile(Helpers::getStorageDisk()->path($item), $item);
        }

        $zip->close();

        return response()
            ->download(
                file: $zipFileName,
                name: sprintf('%s_%s_logs.zip', config('app.name'), date('d.m.Y__H:i'))
            )
            ->deleteFileAfterSend();
    }
}
