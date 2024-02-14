<?php

namespace Delta4op\Laravel\TrackerBot\Enums\Concerns;

use Delta4op\Laravel\TrackerBot\Enums\HttpMethod;

trait StringEnumHelpers
{
    /**
     * @return string[]
     */
    public static function values(): array
    {
        return collect(self::cases())->map(function(self $method){
            return $method->value;
        })->toArray();
    }
}
