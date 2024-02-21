<?php

namespace Delta4op\Laravel\Tracker;

use Delta4op\Laravel\Tracker\DB\Models\Environment;

trait ProvidesEnvironment
{
    /**
     * @return Environment|null
     */
    public static function getEnvironment(): ?Environment
    {
        $envSymbol = Tracker::config()['env'] ?? 'default';

        /** @var ?Environment */
        return Environment::query()->firstOrCreate([
            'symbol' => $envSymbol,
        ]);
    }
}
