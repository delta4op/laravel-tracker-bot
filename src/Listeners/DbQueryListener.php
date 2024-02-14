<?php

namespace Delta4op\Laravel\TrackerBot\Listeners;

use Delta4op\Laravel\TrackerBot\DB\Models\objects\DbQueryObject;
use Delta4op\Laravel\TrackerBot\Enums\AppEntryType;
use Delta4op\Laravel\TrackerBot\Facades\TrackerBot;
use Illuminate\Database\Events\QueryExecuted;

class DbQueryListener extends Listener
{
    public function handle(QueryExecuted $event): void
    {
        if (!TrackerBot::isEnabled() || $this->isTrackerBotQuery($event)) {
            return;
        }

//        $this->recordEntry(
//            AppEntryType::DB_QUERY,
//            $this->prepareEventObject($event)
//        );
    }

    /**
     * @param QueryExecuted $event
     * @return bool
     */
    protected function isTrackerBotQuery(QueryExecuted $event): bool
    {
        return $event->connectionName === config('tracker-bot.storage.connection');
    }

    protected function prepareEventObject(QueryExecuted $event): DbQueryObject
    {
        $object = new DbQueryObject();

        $object->connection = $event->connectionName;
        $object->bindings = $event->bindings;
        $object->query = $this->replaceBindings($event);
        $object->time = $event->time; // millis
        $object->hash = $this->familyHash($event);

        if ($caller = $this->getCallerFromStackTrace()) {
            $object->file = $caller['file'] ?? null;
            $object->line = $caller['line'] ?? null;
        }

        return $object;
    }

    /**
     * Get the tags for the query.
     *
     * @param  QueryExecuted  $event
     * @return array
     */
    protected function tags($event)
    {
        return isset($this->options['slow']) && $event->time >= $this->options['slow'] ? ['slow'] : [];
    }

    /**
     * Calculate the family look-up hash for the query event.
     *
     * @param  QueryExecuted  $event
     * @return string
     */
    public function familyHash($event)
    {
        return md5($event->sql);
    }

    /**
     * Format the given bindings to strings.
     *
     * @param  QueryExecuted  $event
     * @return array
     */
    protected function formatBindings($event)
    {
        return $event->connection->prepareBindings($event->bindings);
    }

    /**
     * Replace the placeholders with the actual bindings.
     *
     * @param  QueryExecuted  $event
     * @return string
     */
    public function replaceBindings($event)
    {
        $sql = $event->sql;

        foreach ($this->formatBindings($event) as $key => $binding) {
            $regex = is_numeric($key)
                ? "/\?(?=(?:[^'\\\']*'[^'\\\']*')*[^'\\\']*$)/"
                : "/:{$key}(?=(?:[^'\\\']*'[^'\\\']*')*[^'\\\']*$)/";

            if ($binding === null) {
                $binding = 'null';
            } elseif (! is_int($binding) && ! is_float($binding)) {
                $binding = $this->quoteStringBinding($event, $binding);
            }

            $sql = preg_replace($regex, $binding, $sql, 1);
        }

        return $sql;
    }

    /**
     * Add quotes to string bindings.
     *
     * @param  QueryExecuted  $event
     * @param  string  $binding
     * @return string
     */
    protected function quoteStringBinding($event, $binding)
    {
        try {
            return $event->connection->getPdo()->quote($binding);
        } catch (\PDOException $e) {
            throw_if($e->getCode() !== 'IM001', $e);
        }

        // Fallback when PDO::quote function is missing...
        $binding = \strtr($binding, [
            chr(26) => '\\Z',
            chr(8) => '\\b',
            '"' => '\"',
            "'" => "\'",
            '\\' => '\\\\',
        ]);

        return "'".$binding."'";
    }
}
