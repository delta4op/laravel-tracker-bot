<?php

namespace Delta4op\Laravel\Tracker\Rules;

use Delta4op\Laravel\Tracker\DB\Models\Environment;
use Illuminate\Validation\Rules\Exists;

class EnvironmentExists extends Exists
{
    public function __construct()
    {
        parent::__construct(
            table: (new Environment())->getTable()
        );
    }
}
