<?php

namespace Delta4op\Laravel\TrackerBot\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method bool isEnabled()
 *
 * @see \Delta4op\Laravel\TrackerBot\TrackerBot
 */
class TrackerBot extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \Delta4op\Laravel\TrackerBot\TrackerBot::class;
    }
}
