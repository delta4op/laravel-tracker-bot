<?php

namespace Delta4op\Laravel\Tracker\Watchers;

use Delta4op\Laravel\Tracker\DB\Models\Metrics\DbQuery;
use Delta4op\Laravel\Tracker\Enums\Database;
use Delta4op\Laravel\Tracker\Helpers\FileHelpers;
use Delta4op\Laravel\Tracker\Tracker;
use Illuminate\Foundation\Application;
use Illuminate\Database\Events\QueryExecuted;
use PDOException;
use Throwable;

class DbQueryWatcher extends Watcher
{
    /**
     * Register the watcher.
     *
     * @param  Application  $app
     * @return void
     */
    public function register(Application $app): void
    {
        $app['events']->listen(QueryExecuted::class, [$this, 'recordDbQuery']);
    }

    /**
     * @param QueryExecuted $event
     * @return void
     */
    public function recordDbQuery(QueryExecuted $event): void
    {
        if (!Tracker::isRecording() || $this->shouldIgnore($event)) {
            return;
        }

        Tracker::recordEntry(
            $this->prepareQueryModel($event)
        );
    }

    /**
     * @param QueryExecuted $event
     * @return bool
     */
    protected function isTrackerBotQuery(QueryExecuted $event): bool
    {
        return $event->connectionName === Tracker::dbConnection();
    }

    protected function prepareQueryModel(QueryExecuted $event): DbQuery
    {
        $dbQuery = new DbQuery();

        $dbQuery->connection = $event->connectionName;
        $dbQuery->db = $this->resolveDatabase($event);
        $dbQuery->query = $this->replaceBindings($event);
        $dbQuery->time = $event->time; // millis
        $dbQuery->bindings = $event->bindings;


        if ($caller = $this->getCallerFromStackTrace()) {
            $dbQuery->file = $caller['file'] ?? null;
            $dbQuery->line = $caller['line'] ?? null;

            if (is_string($dbQuery->file)) {
                $dbQuery->is_internal_file = FileHelpers::isInternalFile($dbQuery->file);
            }
        }

        return $dbQuery;
    }

    /**
     * @param QueryExecuted $event
     * @return Database
     */
    protected function resolveDatabase(QueryExecuted $event): Database
    {
        return match($event->connection->getDriverName()){
            'mysql' => Database::MYSQL,
            'mongodb' => Database::MONGODB,
            'sqlite' => Database::SQLITE,
            'postgresql' => Database::POSTGRESQL,
            'sqlsrv' => Database::SQLSRV,
            default => Database::OTHER,
        };
    }

    /**
     * Format the given bindings to strings.
     *
     * @param QueryExecuted $event
     * @return array
     */
    protected function formatBindings(QueryExecuted $event): array
    {
        return $event->connection->prepareBindings($event->bindings);
    }

    /**
     * Replace the placeholders with the actual bindings.
     *
     * @param QueryExecuted $event
     * @return string
     * @throws Throwable
     */
    public function replaceBindings(QueryExecuted $event): string
    {
        $sql = $event->sql;

        foreach ($this->formatBindings($event) as $key => $binding) {
            $regex = is_numeric($key)
                ? "/\?(?=(?:[^'\\\']*'[^'\\\']*')*[^'\\\']*$)/"
                : "/:{$key}(?=(?:[^'\\\']*'[^'\\\']*')*[^'\\\']*$)/";

            if ($binding === null) {
                $binding = 'null';
            } elseif (!is_int($binding) && !is_float($binding)) {
                $binding = $this->quoteStringBinding($event, $binding);
            }

            $sql = preg_replace($regex, $binding, $sql, 1);
        }

        return $sql;
    }

    /**
     * Add quotes to string bindings.
     *
     * @param QueryExecuted $event
     * @param string $binding
     * @return string
     * @throws Throwable
     */
    protected function quoteStringBinding(QueryExecuted $event, string $binding): string
    {
        try {
            return $event->connection->getPdo()->quote($binding);
        } catch (PDOException $e) {
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

        return "'" . $binding . "'";
    }

    /**
     * @param QueryExecuted $event
     * @return bool
     */
    protected function shouldIgnore(QueryExecuted $event): bool
    {
        return !$this->isWatcherEnabled() ||
            $this->isTrackerBotQuery($event);
    }
}
