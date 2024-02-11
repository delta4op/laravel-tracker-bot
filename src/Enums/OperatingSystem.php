<?php

namespace Delta4op\Laravel\TrackerBot\Enums;

enum OperatingSystem: string
{
    case ANDROID = 'ANDROID';
    case WINDOWS = 'WINDOWS';
    case MAC = 'MAC';
    case LINUX = 'LINUX';
}
