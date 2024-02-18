<?php

namespace Delta4op\Laravel\Tracker\DB\Models\Metrics;

use Delta4op\Laravel\Tracker\DB\Concerns\HasTimestamps;
use Delta4op\Laravel\Tracker\DB\EloquentBuilders\ClientRequestEB;
use Delta4op\Laravel\Tracker\DB\EloquentRepositories\ClientRequestER;
use Delta4op\Laravel\Tracker\Enums\HttpMethod;

/**
 * @property ?HttpMethod $method
 * @property ?string $url
 * @property ?string $query_string
 * @property ?array $headers
 * @property ?string $content
 *
 * @property ?string $response_content
 * @property ?array $response_headers
 * @property ?int $response_status
 *
 * @property ?float $duration
 *
 * @method static ClientRequestEB query()
 */
class ClientRequest extends MetricsModel
{
    use HasTimestamps;

    protected $table = 'client_requests';

    protected $casts = [
        'headers' => 'array',
        'response_headers' => 'array',
    ];

    /**
     * @return string
     */
    public function calculateFamilyHash(): string
    {
        return md5(
            ($this->method ?? '')
            ($this->url ?? '')
        );
    }

    /**
     * @param $query
     * @return ClientRequestEB
     */
    public function newEloquentBuilder($query): ClientRequestEB
    {
        return new ClientRequestEB($query);
    }

    /**
     * @return ClientRequestER
     */
    public static function repository(): ClientRequestER
    {
        return new ClientRequestER;
    }
}
