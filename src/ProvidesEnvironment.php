<?php

namespace Delta4op\Laravel\Tracker;

use Delta4op\Laravel\Tracker\DB\Models\Environment;
use Illuminate\Support\Str;

trait ProvidesEnvironment
{
    /**
     * @return Environment|null
     */
    public static function getEnvironment(): ?Environment
    {
        $envSymbol = Str::upper(Tracker::config()['env'] ?? 'DEFAULT');

        /** @var ?Environment */
        return Environment::query()->firstOrCreate([
            'symbol' => $envSymbol,
        ]);
    }
}