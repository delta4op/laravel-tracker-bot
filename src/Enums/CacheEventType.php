<?php

namespace Delta4op\Laravel\TrackerBot\Enums;

use Delta4op\Laravel\TrackerBot\Enums\Concerns\StringEnumHelpers;

enum CacheEventType: string
{
    use StringEnumHelpers;

    case HIT = 'HIT';
    case MISSED = 'MISSED';
    case SET = 'SET';
    case FORGET = 'FORGET';
}
