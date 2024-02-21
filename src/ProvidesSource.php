<?php

namespace Delta4op\Laravel\Tracker;

use Delta4op\Laravel\Tracker\DB\Models\Source;

trait ProvidesSource
{
    /**
     * @return Source|null
     */
    public static function getSource(): ?Source
    {
        $sourceSymbol = Tracker::config()['source'] ?? 'main';

        /** @var ?Source */
        return Source::query()->firstOrCreate([
            'symbol' => $sourceSymbol,
        ]);
    }
}
