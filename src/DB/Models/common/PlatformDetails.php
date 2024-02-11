<?php

namespace Delta4op\Laravel\TrackerBot\DB\Models\common;

use Delta4op\Laravel\TrackerBot\DB\Models\BaseModel;
use Delta4op\Laravel\TrackerBot\Enums\Platform;

/**
 * @property ?Platform $type
 */
class PlatformDetails extends BaseModel
{
    protected $casts = [
        'type' => Platform::class
    ];
}
