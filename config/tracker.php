<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Source
    |--------------------------------------------------------------------------
    |
    | ...
    |
    */
    'source' => env('TRACKER_SOURCE', 'MASTER'),

    /*
    |--------------------------------------------------------------------------
    | Environment
    |--------------------------------------------------------------------------
    |
    | ...
    |
    */
    'env' => env('TRACKER_ENV', config('app.env', 'DEFAULT')),

    /*
    |--------------------------------------------------------------------------
    | Tracker Master Switch
    |--------------------------------------------------------------------------
    |
    | This option may be used to disable all Tracker watchers regardless
    | of their individual configuration, which simply provides a single
    | and convenient way to enable or disable Tracker data storage.
    |
    */
    'enabled' => env('TRACKER_ENABLED', false),

    /*
    |--------------------------------------------------------------------------
    | Allowed / Ignored Paths & Commands
    |--------------------------------------------------------------------------
    |
    | The following array lists the URI paths and Artisan commands that will
    | not be watched by Tracker. In addition to this list, some Laravel
    | commands, like migrations and queue commands, are always ignored.
    |
    */

    'only_paths' => [
        // 'api/*'
    ],

    'ignore_paths' => [
        'livewire*',
        'nova-api*',
        'pulse*',
    ],

    'ignore_commands' => [
        //
    ],

    /*
    |--------------------------------------------------------------------------
    | Tracker Storage Driver
    |--------------------------------------------------------------------------
    |
    | This configuration options determines the storage driver that will
    | be used to store Tracker's data. In addition, you may set any
    | custom options as needed by the particular driver you choose.
    |
    */

    'driver' => env('TRACKER_DRIVER', 'database'),
    'storage' => [
        'database' => [
            'connection' => env('TRACKER_DB_CONNECTION', 'tracker'),
        ],
    ],

    /**
     * queue configuration
     */
    'queue' => [
        'enabled' => false,
        'connection' => ''
    ],

    // todo - morph maps to be added
    // env/source etc case sensitive or not

    /**
     * Watchers and their behaviors can be controller
     * from here
     */
    'watchers' => [
        Delta4op\Laravel\Tracker\Watchers\AppRequestWatcher::class => [
            'enabled' => env('TRACKER_APP_REQUEST_WATCHER', false),
            'size_limit' => env('TRACKER_RESPONSE_SIZE_LIMIT', 64),
            'ignore_http_methods' => [],
            'ignore_status_codes' => [],
        ],

        Delta4op\Laravel\Tracker\Watchers\DbQueryWatcher::class => [
            'enabled' => env('TRACKER_DB_QUERY_WATCHER', false),
            'ignore_packages' => true,
            'ignore_paths' => [],
            'slow' => 100,
        ],

        Delta4op\Laravel\Tracker\Watchers\ConsoleCommandWatcher::class => [
            'enabled' => env('TRACKER_CONSOLE_COMMAND_WATCHER', false),
            'ignore' => [],
        ],

        Delta4op\Laravel\Tracker\Watchers\AppErrorWatcher::class => [
            'enabled' => env('TRACKER_APP_ERROR_WATCHER', false),
        ],

        Delta4op\Laravel\Tracker\Watchers\CacheWatcher::class => [
            'enabled' => env('TRACKER_CACHE_WATCHER', false),
            'hidden' => [],
        ],

        Delta4op\Laravel\Tracker\Watchers\ConsoleScheduleWatcher::class => [
            'enabled' => env('TRACKER_SCHEDULE_COMMAND_WATCHER', false),
        ],

        Delta4op\Laravel\Tracker\Watchers\MailWatcher::class => [
            'enabled' => env('TRACKER_MAIL_WATCHER', false),
        ],

        Delta4op\Laravel\Tracker\Watchers\EventWatcher::class => [
            'enabled' => env('TRACKER_EVENT_WATCHER', false),
            'ignore' => [],
        ],

        Delta4op\Laravel\Tracker\Watchers\LogWatcher::class => [
            'enabled' => env('TRACKER_LOG_WATCHER', false),
            'ignore' => [],
        ],

        Delta4op\Laravel\Tracker\Watchers\AppDumpWatcher::class => [
            'enabled' => env('TRACKER_APP_DUMP_WATCHER', false),
            'always' => env('TRACKER_APP_DUMP_WATCHER_ALWAYS', false),
        ],
    ],
];
