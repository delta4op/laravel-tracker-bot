<?php

namespace Delta4op\Laravel\TrackerBot\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Delta4op\Laravel\TrackerBot\TrackerBot
 */
class TrackerBot extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \Delta4op\Laravel\TrackerBot\TrackerBot::class;
    }
}
