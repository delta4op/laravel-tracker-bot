<?php

namespace Delta4op\Laravel\Tracker\DB\EloquentRepositories;

use Delta4op\Laravel\Tracker\DB\Models\Metrics\ConsoleCommandLog;

class ConsoleCommandER extends EloquentRepository
{
    /**
     * @param int $id
     * @return ConsoleCommandLog|null
     */
    public function findById(int $id): ?ConsoleCommandLog
    {
        /** @var ConsoleCommandLog|null */
        return ConsoleCommandLog::query()->whereId($id)->first();
    }
}
