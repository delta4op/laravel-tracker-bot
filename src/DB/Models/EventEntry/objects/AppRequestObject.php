<?php

namespace Delta4op\Laravel\TrackerBot\DB\Models\EventEntry\objects;

use Delta4op\Laravel\TrackerBot\DB\Models\common\BrowserDetails;
use Jenssegers\Mongodb\Relations\EmbedsOne;

/**
 * @property ?string $ip
 * @property ?string[] $ips
 * @property ?string $uri
 * @property ?string $method
 * @property ?string $controllerAction
 * @property ?string[] $middleware
 * @property ?array $headers
 * @property ?string $content
 * @property ?array $input
 * @property ?array $session
 * @property ?array $response
 * @property ?array $responseHeaders
 * @property ?int $responseStatus
 * @property ?int $duration
 * @property ?int $memory
 * @property ?int $hostName
 * @property ?BrowserDetails $browserDetails
*/
class AppRequestObject extends EntryObject
{
    /**
     * @return EmbedsOne
     */
    public function browser(): EmbedsOne
    {
        return $this->embedsOne(BrowserDetails::class);
    }
}
