<?php

declare(strict_types=1);

namespace Manzadey\LaravelOrchidStorageLogs\Components;

use Illuminate\Contracts\View\View;
use Illuminate\View\Component;
use Orchid\Screen\Repository;

class EnvironmentComponent extends Component
{
    public function __construct(
        readonly private Repository $repository
    )
    {
    }

    public function color() : string
    {
        return match ($this->environment()) {
            'production' => config('storage-logs.production_colors.production'),
            'local' => config('storage-logs.production_colors.local'),
            'test' => config('storage-logs.production_colors.test'),
            default => config('storage-logs.production_color_default')
        };
    }

    public function environment() : string
    {
        return $this->repository->get('env');
    }

    public function render() : View
    {
        return view('storage-logs::components.environment-component');
    }
}
