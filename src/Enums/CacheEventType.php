<?php

namespace Delta4op\Laravel\Tracker\Enums;

use Delta4op\Laravel\Tracker\Enums\Concerns\StringEnumHelpers;

enum CacheEventType: string
{
    use StringEnumHelpers;

    case HIT = 'HIT';
    case MISSED = 'MISSED';
    case SET = 'SET';
    case FORGET = 'FORGET';
}
