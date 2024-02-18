<?php

namespace Delta4op\Laravel\Tracker\DB\EloquentRepositories;

use Delta4op\Laravel\Tracker\DB\Models\Metrics\Dump;

class DumpER extends EloquentRepository
{
    /**
     * @param int $id
     * @return Dump|null
     */
    public function findById(int $id): ?Dump
    {
        /** @var Dump|null */
        return Dump::query()->whereId($id)->first();
    }
}
