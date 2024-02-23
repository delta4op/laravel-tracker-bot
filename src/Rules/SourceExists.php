<?php

namespace Delta4op\Laravel\Tracker\Rules;

use Delta4op\Laravel\Tracker\DB\Models\Source;
use Illuminate\Validation\Rules\Exists;

class SourceExists extends Exists
{
    public function __construct()
    {
        parent::__construct(
            table: (new Source())->getTable()
        );
    }
}
