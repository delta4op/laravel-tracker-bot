<?php

namespace Delta4op\Laravel\TrackerBot\DB\Models\Metrics;

use Delta4op\Laravel\TrackerBot\DB\Concerns\HasTimestamps;
use Delta4op\Laravel\TrackerBot\DB\Models\AppEntry;
use Delta4op\Laravel\TrackerBot\DB\Models\Environment;
use Delta4op\Laravel\TrackerBot\DB\Models\Source;
use Delta4op\Laravel\TrackerBot\Enums\HttpMethod;

/**
 * @property ?string $protocol
 * @property ?boolean $secure
 * @property ?HttpMethod $method
 * @property ?string $host
 * @property ?string $path
 * @property ?string $url
 * @property ?string $full_url
 * @property ?string $query_string
 * @property ?string $ip
 * @property ?string[] $ips
 * @property ?string[] $middleware
 * @property ?array $headers
 * @property ?string $content
 * @property ?array $session
 * @property ?array $cookies
 *
 * @property ?string $response_content
 * @property ?array $response_headers
 * @property ?int $response_status
 *
 * @property ?float $duration
 * @property ?float $memory
 *
 * @property ?string $controller_class
 * @property ?string $controller_action
 */
class AppRequest extends MetricsModel
{
    use HasTimestamps;

    protected $table = 'app_requests';

    protected $casts = [
        'ips' => 'array',
        'middleware' => 'array',
        'headers' => 'array',
        'session' => 'array',
        'cookies' => 'array',
        'response_headers' => 'array',
    ];

    /**
     * @return string
     */
    public function calculateFamilyHash(): string
    {
        return md5(
            ($this->secure ? 'secure' : 'not-secure') .
            ($this->method?->value ?? '') .
            ($this->full_url ?? '')
            ($this->content ?? '')
        );
    }
}
