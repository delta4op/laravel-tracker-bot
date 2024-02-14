<?php

namespace Delta4op\Laravel\TrackerBot\DB\Models\Metrics\AppRequest;

use Delta4op\Laravel\TrackerBot\DB\Concerns\HasTimestamps;
use Delta4op\Laravel\TrackerBot\DB\Concerns\MetricsModel;
use Delta4op\Laravel\TrackerBot\DB\Models\AppEntry\AppEntry;
use Delta4op\Laravel\TrackerBot\DB\Models\Environment\Environment;
use Delta4op\Laravel\TrackerBot\DB\Models\Source\Source;
use Delta4op\Laravel\TrackerBot\Enums\AppEntryType;
use Delta4op\Laravel\TrackerBot\Enums\HttpMethod;
use Delta4op\Laravel\TrackerBot\Enums\HttpContentType;
use Delta4op\Laravel\TrackerBot\Enums\RequestProtocol;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Support\Str;

/**
 * @property ?RequestProtocol $protocol
 * @property ?HttpMethod $method
 * @property ?string $host
 * @property ?string $path
 * @property ?string $url
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
 * @property ?AppEntry $appEntry
 * @property ?Source $source
 * @property ?Environment $env
 */
class AppRequest extends MetricsModel
{
    use HasTimestamps;

    protected $table = 'app_requests';

    protected $casts = [
        'protocol' => RequestProtocol::class,
        'ips' => 'array',
        'middleware' => 'array',
        'headers' => 'array',
        'session' => 'array',
        'cookies' => 'array',
        'response_headers' => 'array',
    ];

    /**
     * The "booted" method of the model.
     */
    protected static function booted(): void
    {
        static::creating(function (AppRequest $appRequest) {
            $appRequest->uuid = Str::orderedUuid()->toString();
        });
    }
}
