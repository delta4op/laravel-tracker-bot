<?php

namespace Delta4op\Laravel\TrackerBot\DB\Models;

use Jenssegers\Mongodb\Eloquent\Model;

class BaseModel extends Model
{
    protected $guarded = [];

    const CREATED_AT = 'createdAt';

    const UPDATED_AT = 'updatedAt';

    /**
     * Set a given attribute on the model.
     *
     * @param  string  $key
     * @param  mixed  $value
     * @return $this
     */
    public function setAttribute($key, $value): static
    {
        if ($value === null) {
            unset($this->attributes[$key]);
        }

        return parent::setAttribute($key, $value);
    }

    /**
     * @return bool
     */
    public function hasAllEmptyAttributes(): bool
    {
        foreach($this->getAttributes() as $key => $value) {
            if(!empty($value)) {
                return false;
            }
        }
        return true;
    }

    /**
     * @return bool
     */
    public function hasAnyValidAttribute(): bool
    {
        return !$this->hasAllEmptyAttributes();
    }

    /**
     * @return mixed
     */
    public function getConnectionName(): mixed
    {
        return config('tracker-bot.storage.connection', 'tracker-bot');
    }
}
