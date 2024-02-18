<?php

namespace Delta4op\Laravel\Tracker\DB\EloquentRepositories;

use Delta4op\Laravel\Tracker\DB\Models\Metrics\Mail;

class MailER extends EloquentRepository
{
    /**
     * @param int $id
     * @return Mail|null
     */
    public function findById(int $id): ?Mail
    {
        /** @var Mail|null */
        return Mail::query()->whereId($id)->first();
    }
}
