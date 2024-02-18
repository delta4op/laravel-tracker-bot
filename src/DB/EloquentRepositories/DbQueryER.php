<?php

namespace Delta4op\Laravel\Tracker\DB\EloquentRepositories;

use Delta4op\Laravel\Tracker\DB\Models\Metrics\DbQuery;

class DbQueryER extends EloquentRepository
{
    /**
     * @param int $id
     * @return DbQuery|null
     */
    public function findById(int $id): ?DbQuery
    {
    /** @var DbQuery|null */
        return DbQuery::query()->whereId($id)->first();
    }
}
