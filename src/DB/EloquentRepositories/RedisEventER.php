<?php

namespace Delta4op\Laravel\Tracker\DB\EloquentRepositories;

use Delta4op\Laravel\Tracker\DB\Models\Metrics\RedisEvent;

class RedisEventER extends EloquentRepository
{
    /**
     * @param int $id
     * @return RedisEvent|null
     */
    public function findById(int $id): ?RedisEvent
    {
        /** @var RedisEvent|null */
        return RedisEvent::query()->whereId($id)->first();
    }
}
