<?php

declare(strict_types=1);

return [
    'path' => storage_path('logs'),

    'disk' => 'logs',

    'error_type_color_default' => 'secondary',

    'error_type_colors' => [
        'emergency' => 'dark',
        'alert'     => 'danger',
        'critical'  => 'danger',
        'error'     => 'danger',
        'warning'   => 'warning',
        'notice'    => 'primary',
        'info'      => 'info',
        'debug'     => 'secondary',
    ],

    'production_color_default' => 'secondary',

    'production_colors' => [
        'local'      => 'primary',
        'production' => 'success',
        'test'       => 'info',
    ],
];

