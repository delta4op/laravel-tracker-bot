<?php

namespace Delta4op\Laravel\Tracker\DB\EloquentRepositories;

use Delta4op\Laravel\Tracker\DB\Models\Metrics\Log;

class LogER extends EloquentRepository
{
    /**
     * @param int $id
     * @return Log|null
     */
    public function findById(int $id): ?Log
    {
        /** @var Log|null */
        return Log::query()->whereId($id)->first();
    }
}
