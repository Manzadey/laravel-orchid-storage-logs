<?php

namespace Manzadey\LaravelOrchidStorageLogs\Components;

use Illuminate\Contracts\View\View;
use Illuminate\View\Component;
use Orchid\Screen\Repository;

class TypeComponent extends Component
{
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct(
        readonly private Repository $repository
    )
    {
        //
    }

    public function render() : View
    {
        return view('storage-logs::components.type-component');
    }

    public function type() : string
    {
        return $this->repository->get('type');
    }

    public function color() : string
    {
        return match (strtolower($this->repository->get('type'))) {
            'emergency' => config('storage-logs.error_type_colors.emergency', 'dark'),
            'alert' => config('storage-logs.error_type_colors.alert', 'danger'),
            'critical' => config('storage-logs.error_type_colors.critical', 'danger'),
            'error' => config('storage-logs.error_type_colors.error', 'danger'),
            'warning' => config('storage-logs.error_type_colors.warning', 'warning'),
            'notice' => config('storage-logs.error_type_colors.notice', 'primary'),
            'info' => config('storage-logs.error_type_colors.info', 'info'),
            'debug' => config('storage-logs.error_type_colors.debug', 'secondary'),
            default => config('storage-logs.error_type_colors.error_type_color_default', 'secondary'),
        };
    }
}
