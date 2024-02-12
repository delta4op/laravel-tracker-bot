<?php

namespace Delta4op\Laravel\TrackerBot;

class TrackerBot
{
    /**
     * @return bool
     */
    public static function isEnabled(): bool
    {
        $enabled = config('tracker-bot.enabled', false);

        return in_array($enabled, [true, 'true']);
    }

    /**
     * @return bool
     */
    public static function isDisabled(): bool
    {
        return !static::isEnabled();
    }
}
