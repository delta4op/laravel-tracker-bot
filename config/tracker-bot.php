<?php

return [

    /**
     * The source is an optional configuration that gets added
     * to the appEntries collection in the database
     *
     * The purpose is to segregate code from multiple servers or multiple projects using this key.
     */
    'source' => 'MASTER',

    /**
     * If this flag is true
     * then the package will do its magic
     */
    'enabled' => env('TRACKER_BOT_ENABLED', true),

    /**
     * storage configuration
     */
    'storage' => [
        'driver' => 'database',
        'connection' => env('TRACKER_BOT_DB_CONNECTION', 'tracker-bot'),
    ],

    /**
     * Listeners and their behaviors can be controller
     * from here
     */
    'listeners' => [
        Delta4op\Laravel\TrackerBot\Listeners\AppRequestListener::class => [
            'enabled' => env('TRACKER_BOT_APP_REQUEST_LISTENER', true),
            'size_limit' => env('TRACKER_BOT_RESPONSE_SIZE_LIMIT', 64),
            'ignore_http_methods' => [],
            'ignore_status_codes' => [],
        ],

        Delta4op\Laravel\TrackerBot\Listeners\DbQueryListener::class => [
            'enabled' => env('TRACKER_BOT_DB_QUERY_LISTENER', true),
            'ignore_packages' => true,
            'ignore_paths' => [],
            'slow' => 100,
        ],

        Delta4op\Laravel\TrackerBot\Listeners\ConsoleCommandListener::class => [
            'enabled' => env('TRACKER_BOT_CONSOLE_COMMAND_LISTENER', true),
            'ignore' => [],
        ],

        Delta4op\Laravel\TrackerBot\Listeners\AppErrorListener::class => [
            'enabled' => env('TRACKER_BOT_APP_ERROR_LISTENER', true),
        ],

        Delta4op\Laravel\TrackerBot\Listeners\CacheListener::class => [
            'enabled' => env('TRACKER_BOT_CACHE_LISTENER', true),
            'hidden' => [],
        ],

        Delta4op\Laravel\TrackerBot\Listeners\ScheduleListener::class => [
            'enabled' => env('TRACKER_BOT_SCHEDULE_COMMAND_LISTENER', true),
        ],

        Delta4op\Laravel\TrackerBot\Listeners\MailListener::class => [
            'enabled' => env('TRACKER_BOT_MAIL_LISTENER', true),
        ],

//        Delta4op\Laravel\TrackerBot\Listeners\ModelListener::class => [
//            'enabled' => env('TRACKER_BOT_MODEL_LISTENER', true),
//            'events' => ['eloquent.*'],
//            'hydrations' => true,
//        ],

        Delta4op\Laravel\TrackerBot\Listeners\EventListener::class => [
            'enabled' => env('TRACKER_BOT_EVENT_LISTENER', true),
            'ignore' => [],
        ],
    ],
];
