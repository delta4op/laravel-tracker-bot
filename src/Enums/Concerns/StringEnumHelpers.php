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
        return collect(self::cases())->map(function(HttpMethod $method){
            return $method->value;
        })->toArray();
    }
}
