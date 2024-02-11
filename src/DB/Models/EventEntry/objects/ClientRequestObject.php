<?php

namespace Delta4op\Laravel\TrackerBot\DB\Models\EventEntry\objects;

/**
 * @property ?string $uri
 * @property ?string $method
 * @property ?array $headers
 * @property ?string $content
 * @property ?array $input
 * @property ?array $response
 * @property ?array $responseHeaders
 * @property ?int $responseStatus
 * @property ?int $duration
*/
class ClientRequestObject extends EntryObject
{
}
