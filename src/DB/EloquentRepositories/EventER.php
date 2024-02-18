<?php

namespace Delta4op\Laravel\Tracker\DB\EloquentRepositories;

use Delta4op\Laravel\Tracker\DB\Models\Metrics\Event;

class EventER extends EloquentRepository
{
    /**
     * @param int $id
     * @return Event|null
     */
    public function findById(int $id): ?Event
    {
        /** @var Event|null */
        return Event::query()->whereId($id)->first();
    }
}
