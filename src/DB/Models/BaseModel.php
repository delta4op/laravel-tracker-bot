<?php

namespace Delta4op\Laravel\Tracker\DB\Models;

use Illuminate\Database\Eloquent\Model;

class BaseModel extends Model
{
    /**
     * @return mixed
     */
    public function getConnectionName(): mixed
    {
        return config('tracker.storage.database.connection', 'tracker');
    }
}
