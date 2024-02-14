<?php

namespace Delta4op\Laravel\Tracker\Enums;

use Delta4op\Laravel\Tracker\Enums\Concerns\StringEnumHelpers;

enum HttpContentType: string
{
    use StringEnumHelpers;

    case JSON = 'JSON';
    case XML = 'XML';
    case TEXT = 'TEXT';

    public static function create()
    {

    }
}
