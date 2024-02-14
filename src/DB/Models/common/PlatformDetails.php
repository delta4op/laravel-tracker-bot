<?php

namespace Delta4op\Laravel\Tracker\DB\Models\common;

use Delta4op\Laravel\Tracker\DB\Models\BaseModel;
use Delta4op\Laravel\Tracker\Enums\Platform;

/**
 * @property ?Platform $type
 */
class PlatformDetails extends BaseModel
{
    protected $casts = [
        'type' => Platform::class
    ];
}
