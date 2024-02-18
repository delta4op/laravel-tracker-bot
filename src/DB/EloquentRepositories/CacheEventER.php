<?php

namespace Delta4op\Laravel\Tracker\DB\EloquentRepositories;

use Delta4op\Laravel\Tracker\DB\Models\Metrics\CacheEvent;

class CacheEventER extends EloquentRepository
{
    /**
     * @param int $id
     * @return CacheEvent|null
     */
    public function findById(int $id): ?CacheEvent
    {
        /** @var CacheEvent|null */
        return CacheEvent::query()->whereId($id)->first();
    }
}
