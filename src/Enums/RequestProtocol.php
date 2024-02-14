<?php

namespace Delta4op\Laravel\TrackerBot\Enums;

use Delta4op\Laravel\TrackerBot\Enums\Concerns\StringEnumHelpers;

enum RequestProtocol: string
{
    use StringEnumHelpers;

    case HTTP = 'HTTP';
    case HTTPS = 'HTTPS';
}
