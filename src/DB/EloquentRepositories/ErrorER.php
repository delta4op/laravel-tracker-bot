<?php

namespace Delta4op\Laravel\Tracker\DB\EloquentRepositories;

use Delta4op\Laravel\Tracker\DB\Models\Metrics\Error;

class ErrorER extends EloquentRepository
{
    /**
     * @param int $id
     * @return Error|null
     */
    public function findById(int $id): ?Error
    {
        /** @var Error|null */
        return Error::query()->whereId($id)->first();
    }
}
