<?php

namespace Delta4op\Laravel\TrackerBot\DB\Models\objects;

/**
 * @property ?string $uri
 * @property ?string $method
 * @property ?array $headers
 * @property ?string $content
 * @property ?array $input
 * @property ?array $response
 * @property ?array $responseHeaders
 * @property ?int $responseStatus
 * @property ?float $duration
 */
class ClientRequestObject extends EntryObject
{
}
