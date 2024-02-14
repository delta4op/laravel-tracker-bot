<?php

namespace Delta4op\Laravel\TrackerBot;

use Delta4op\Laravel\TrackerBot\DB\Models\Source;
use Illuminate\Support\Str;

trait ProvidesSource
{
    /**
     * @return Source|null
     */
    public static function getSource(): ?Source
    {
        $sourceSymbol = Str::upper(Tracker::config()['source'] ?? 'MASTER');

        /** @var ?Source */
        return Source::query()->firstOrCreate([
            'symbol' => $sourceSymbol,
        ]);
    }
}
