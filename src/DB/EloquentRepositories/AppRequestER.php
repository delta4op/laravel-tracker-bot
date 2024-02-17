<?php

namespace Delta4op\Laravel\Tracker\DB\EloquentRepositories;

use Delta4op\Laravel\Tracker\DB\Models\Metrics\AppRequest;

class AppRequestER extends EloquentRepository
{
    /**
     * @param int $id
     * @return AppRequest|null
     */
    public function findById(int $id): ?AppRequest
    {
        /** @var AppRequest|null */
        return AppRequest::query()->whereId($id)->first();
    }
}
