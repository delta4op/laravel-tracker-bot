<?php

namespace Delta4op\Laravel\TrackerBot\Enums;

use Delta4op\Laravel\TrackerBot\Enums\Concerns\StringEnumHelpers;

enum HttpMethod: string
{
    use StringEnumHelpers;

    case GET = 'GET';
    case POST = 'POST';
    case PUT = 'PUT';
    case UPDATE = 'UPDATE';
    case DELETE = 'DELETE';
    case CONNECT = 'CONNECT';
    case OPTIONS = 'OPTIONS';
    case PATCH = 'PATCH';
    case PURGE = 'PURGE';
    case TRACE = 'TRACE';
}
