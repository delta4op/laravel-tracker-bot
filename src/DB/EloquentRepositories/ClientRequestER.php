<?php

namespace Delta4op\Laravel\Tracker\DB\EloquentRepositories;

use Delta4op\Laravel\Tracker\DB\Models\Metrics\ClientRequest;

class ClientRequestER extends EloquentRepository
{
    /**
     * @param int $id
     * @return ClientRequest|null
     */
    public function findById(int $id): ?ClientRequest
    {
        /** @var ClientRequest|null */
        return ClientRequest::query()->whereId($id)->first();
    }
}
