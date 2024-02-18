<?php

namespace Delta4op\Laravel\Tracker\DB\EloquentRepositories;

use Delta4op\Laravel\Tracker\DB\Models\Metrics\ConsoleSchedule;

class ConsoleScheduleER extends EloquentRepository
{
    /**
     * @param int $id
     * @return ConsoleSchedule|null
     */
    public function findById(int $id): ?ConsoleSchedule
    {
        /** @var ConsoleSchedule|null */
        return ConsoleSchedule::query()->whereId($id)->first();
    }
}
