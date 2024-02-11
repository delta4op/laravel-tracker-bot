<?php

namespace Delta4op\Laravel\TrackerBot;

class TrackerBot
{
    public static function isEnabled()
    {
        return config('tracker-bot.enabled', false);
    }
}
