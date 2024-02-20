<?php

namespace Delta4op\Laravel\Tracker\DB\Models\Metrics;

use Delta4op\Laravel\Tracker\DB\Concerns\HasTimestamps;
use Delta4op\Laravel\Tracker\DB\EloquentBuilders\AppRequestEB;
use Delta4op\Laravel\Tracker\DB\EloquentRepositories\AppRequestER;
use Delta4op\Laravel\Tracker\Enums\HttpMethod;

/**
 * @property ?string $protocol
 * @property ?boolean $secure
 * @property ?HttpMethod $method
 * @property ?string $host
 * @property ?string $path
 * @property ?string $path_template
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
 *
 * @method static AppRequestEB query()
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
            ($this->full_url ?? ''),
            ($this->content ?? '')
        );
    }

    /**
     * @param $query
     * @return AppRequestEB
     */
    public function newEloquentBuilder($query): AppRequestEB
    {
        return new AppRequestEB($query);
    }

    /**
     * @return AppRequestER
     */
    public static function repository(): AppRequestER
    {
        return new AppRequestER;
    }
}
