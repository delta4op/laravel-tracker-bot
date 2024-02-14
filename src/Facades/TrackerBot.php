<?php

namespace Delta4op\Laravel\TrackerBot\Facades;

use Delta4op\Laravel\TrackerBot\DB\Models\Environment;
use Delta4op\Laravel\TrackerBot\DB\Models\Source;
use Illuminate\Support\Facades\Facade;

/**
 * @method static bool isEnabled()
 * @method static bool isDisabled()
 * @method static Source|null getSource()
 * @method static Environment|null getEnvironment()
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
