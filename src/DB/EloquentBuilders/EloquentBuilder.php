<?php

namespace Delta4op\Laravel\Tracker\DB\EloquentBuilders;

use Illuminate\Database\Eloquent\Builder;

class EloquentBuilder extends Builder
{
    /**
     * @param string $column
     * @param $values
     * @return EloquentBuilder
     */
    public function whereOrWhereIn(string $column, $values): EloquentBuilder
    {
        if(is_array($values)) {
            return $this->whereIn($column, $values);
        }

        return $this->where($column, $values);
    }
}
