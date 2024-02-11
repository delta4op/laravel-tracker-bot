<?php

namespace Delta4op\Laravel\TrackerBot\Enums;

enum CacheEventType: string
{
    case HIT = 'HIT';
    case MISSED = 'MISSED';
    case SET = 'SET';
    case FORGET = 'FORGET';
}
