<?php

namespace Delta4op\Laravel\TrackerBot\DB\Concerns;

use Carbon\Carbon;

/**
 * @property ?Carbon $created_at
 * @property ?Carbon $updated_at
*/
trait HasTimestamps
{
    use \Illuminate\Database\Eloquent\Concerns\HasTimestamps;
}
