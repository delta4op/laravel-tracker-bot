<?php

return [

    /**
     * The source is an optional configuration that gets added
     * to the appEntries collection in the database
     *
     * The purpose is to segregate code from multiple servers or multiple projects using this key.
     */
    'source' => env('TRACKER_BOT_SOURCE', 'MASTER'),

    /**
     * The env is an required configuration that gets added
     * to the appEntries collection in the database
     *
     * The purpose is to segregate code from multiple servers or multiple projects using this key.
     */
    'env' => env('TRACKER_BOT_ENV', env('app.env', 'DEFAULT')),

    /**
     * If this flag is true
     * then the package will do its magic
     */
    'enabled' => env('TRACKER_BOT_ENABLED', false),

    /**
     * storage configuration
     */
    'storage' => [
        'database' => [
            'connection' => env('TRACKER_BOT_DB_CONNECTION', 'tracker-bot'),
        ],
    ],

    // todo - morph maps to be added
    // env/source etc case sensitive or not

    /**
     * Listeners and their behaviors can be controller
     * from here
     */
    'listeners' => [
        Delta4op\Laravel\TrackerBot\Listeners\AppRequestListener::class => [
            'enabled' => env('TRACKER_BOT_APP_REQUEST_LISTENER', false),
            'size_limit' => env('TRACKER_BOT_RESPONSE_SIZE_LIMIT', 64),
            'ignore_http_methods' => [],
            'ignore_status_codes' => [],
        ],

        Delta4op\Laravel\TrackerBot\Listeners\DbQueryListener::class => [
            'enabled' => env('TRACKER_BOT_DB_QUERY_LISTENER', false),
            'ignore_packages' => true,
            'ignore_paths' => [],
            'slow' => 100,
        ],

        Delta4op\Laravel\TrackerBot\Listeners\ConsoleCommandListener::class => [
            'enabled' => env('TRACKER_BOT_CONSOLE_COMMAND_LISTENER', false),
            'ignore' => [],
        ],

        Delta4op\Laravel\TrackerBot\Listeners\AppErrorListener::class => [
            'enabled' => env('TRACKER_BOT_APP_ERROR_LISTENER', false),
        ],

        Delta4op\Laravel\TrackerBot\Listeners\CacheListener::class => [
            'enabled' => env('TRACKER_BOT_CACHE_LISTENER', false),
            'hidden' => [],
        ],

        Delta4op\Laravel\TrackerBot\Listeners\ScheduleListener::class => [
            'enabled' => env('TRACKER_BOT_SCHEDULE_COMMAND_LISTENER', false),
        ],

        Delta4op\Laravel\TrackerBot\Listeners\MailListener::class => [
            'enabled' => env('TRACKER_BOT_MAIL_LISTENER', false),
        ],

//        Delta4op\Laravel\TrackerBot\Listeners\ModelListener::class => [
//            'enabled' => env('TRACKER_BOT_MODEL_LISTENER', false),
//            'events' => ['eloquent.*'],
//            'hydrations' => true,
//        ],

        Delta4op\Laravel\TrackerBot\Listeners\EventListener::class => [
            'enabled' => env('TRACKER_BOT_EVENT_LISTENER', false),
            'ignore' => [],
        ],

        Delta4op\Laravel\TrackerBot\Listeners\LogListener::class => [
            'enabled' => env('TRACKER_BOT_LOG_LISTENER', false),
            'ignore' => [],
        ],
    ],
];
