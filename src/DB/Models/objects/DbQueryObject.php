<?php

namespace Delta4op\Laravel\TrackerBot\DB\Models\objects;

/**
 * @property ?string $connection
 * @property ?array $bindings
 * @property ?string $query
 * @property ?float $time
 * @property ?string $file
 * @property ?bool $isInternalFile
 * @property ?string $line
 * @property ?string $hash
 * @property ?int $speedRate
 * @property ?boolean $slow
 */
class DbQueryObject extends EntryObject
{

}
