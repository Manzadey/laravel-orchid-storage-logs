<?php

declare(strict_types=1);

namespace Manzadey\LaravelOrchidStorageLogs\Screens;

use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Manzadey\LaravelOrchidStorageLogs\Components\EnvironmentComponent;
use Manzadey\LaravelOrchidStorageLogs\Components\TypeComponent;
use Manzadey\LaravelOrchidStorageLogs\Repositories\StorageLogRepository;
use Manzadey\LaravelOrchidStorageLogs\Services\Helpers;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Repository;
use Orchid\Screen\Sight;
use Orchid\Screen\TD;
use Orchid\Support\Color;
use Orchid\Support\Facades\Layout;

class StorageLogShowScreen extends AbstractStorageLogScreen
{
    private StorageLogRepository $repository;

    public function __construct(
        readonly private Request $request
    )
    {
    }

    public function query(string $storageLog) : iterable
    {
        $filepath = Helpers::filenameDecode($storageLog);

        if(!Helpers::getStorageDisk()->exists($filepath)) {
            abort(404);
        }

        $content  = Helpers::getStorageDisk()->get($filepath);
        $messages = $this->getContent($content);

        $this->repository = new StorageLogRepository($filepath, [
            'data'  => $content,
            'count' => $messages->count(),
        ]);

        return [
            'log'      => $this->repository,
            'messages' => $messages
                ->map(static fn(array $error, int $i) : Repository => new Repository([
                    'date'    => $error['date'],
                    'env'     => $error['env'],
                    'type'    => $error['type'],
                    'message' => $error['message'],
                    'index'      => ++$i,
                ]))
                ->when(
                    $this->request->filled('sort'),
                    fn(Collection $collection) : Collection => $this->sort($collection),
                    fn(Collection $collection) : Collection => $this->sortDefault($collection),
                )
                ->when(
                    $this->request->filled('filter.env'),
                    fn(Collection $collection) : Collection => $this->filterEqual($collection, 'env'),
                )
                ->when(
                    $this->request->filled('filter.type'),
                    fn(Collection $collection) : Collection => $this->filterEqual($collection, 'type'),
                )
                ->when(
                    $this->request->filled('filter.message'),
                    fn(Collection $collection) : Collection => $this->filterContains($collection, 'message'),
                ),
        ];
    }

    private function sort(Collection $collection) : Collection
    {
        $sort = str_replace('-', '', $this->request->input('sort'));

        return str_contains($this->request->input('sort'), '-') ?
            $collection->sortByDesc($sort) :
            $collection->sortBy($sort);
    }

    public function filterEqual(Collection $collection, string $key) : Collection
    {
        return $collection
            ->filter(fn(Repository $repository) : bool => strtolower($repository->get($key)) === strtolower($this->request->input("filter.$key")));
    }

    public function filterContains(Collection $collection, string $key) : Collection
    {
        return $collection
            ->filter(fn(Repository $repository) : bool => str_contains(
                haystack: strtolower($repository->get($key)),
                needle: strtolower($this->request->input("filter.$key"))
            ));
    }

    public function sortDefault(Collection $collection) : Collection
    {
        return $collection->sortByDesc('index');
    }

    public function commandBar() : iterable
    {
        return [
            Button::make(__('Download'))
                ->icon('cloud-download')
                ->type(Color::DEFAULT())
                ->method('download', [
                    'file' => addslashes($this->repository->get('name')),
                ])
                ->rawClick(),
            Button::make(__('Delete'))
                ->icon('trash')
                ->type(Color::DANGER())
                ->method('delete', [
                    'file' => addslashes($this->repository->get('name')),
                ])
                ->confirm(sprintf('Delete %s?', $this->repository->get('name')))
                ->rawClick(),
        ];
    }

    public function layout() : iterable
    {
        return [
            Layout::legend('log', [
                Sight::make('name', __('Name')),
                Sight::make('path', __('Path')),
                Sight::make('size_human', __('Size')),
                Sight::make('count', __('Number of messages')),
                Sight::make('last_modified_human', __('Last update')),
            ])->title(__('More')),

            Layout::tabs([
                __('List of messages') => [
                    Layout::table('messages', [
                        TD::make('index', '#')
                            ->sort()
                            ->alignLeft(),
                        TD::make('date', __('Date'))
                            ->sort(),
                        TD::make('env', __('Environment'))
                            ->component(EnvironmentComponent::class)
                            ->sort()
                            ->filter(),
                        TD::make('type', __('Type'))
                            ->component(TypeComponent::class)
                            ->sort()
                            ->filter(),
                        TD::make('message', __('Message'))
                            ->render(static fn(Repository $repository) : string => str($repository->get('message'))->limit()->toString())
                            ->filter(),
                    ]),
                ],

                __('Content') => [
                    Layout::view('storage-logs::log', [
                        'data' => $this->repository->get('data'),
                    ]),
                ],
            ]),
        ];
    }

    public function getContent(string $content) : Collection
    {
        $pattern = "/^\[(?<date>.*)\]\s(?<env>\w+)\.(?<type>\w+):(?<message>.*)/m";

        preg_match_all($pattern, $content, $matches, PREG_SET_ORDER);

        return collect($matches);
    }
}
