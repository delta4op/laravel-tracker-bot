<?php

namespace Delta4op\Laravel\Tracker\DB\Models\objects;

use Delta4op\Laravel\Tracker\DB\Models\common\BrowserDetails;

/**
 * @property ?string $ip
 * @property ?string[] $ips
 * @property ?string $uri
 * @property ?string $method
 * @property ?string $controllerAction
 * @property ?string[] $middleware
 * @property ?array $headers
 * @property ?string $host
 * @property ?string $getScheme
 * @property ?string $path
 * @property ?string $content
 * @property ?array $input
 * @property ?array $session
 * @property ?string $response
 * @property ?array $responseHeaders
 * @property ?int $responseStatus
 * @property ?int $duration
 * @property ?int $memory
 * @property ?int $hostName
 * @property ?BrowserDetails $browserDetails
 */
class AppRequestObject extends EntryObject
{
    public function browser(): EmbedsOne
    {
        return $this->embedsOne(BrowserDetails::class);
    }
}
