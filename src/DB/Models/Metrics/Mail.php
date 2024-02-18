<?php

namespace Delta4op\Laravel\Tracker\DB\Models\Metrics;

use Delta4op\Laravel\Tracker\DB\Concerns\HasTimestamps;
use Delta4op\Laravel\Tracker\DB\EloquentBuilders\MailEB;
use Delta4op\Laravel\Tracker\DB\EloquentRepositories\MailER;

/**
 * @property ?string $subject
 * @property ?string $mailable
 * @property ?bool $queued
 * @property ?string[] $from
 * @property ?string[] $replyTo
 * @property ?string[] $to
 * @property ?string[] $cc
 * @property ?string[] $bcc
 * @property ?string $html
 * @property ?string $raw
 *
 * @method static MailEB query()
 */
class Mail extends MetricsModel
{
    use HasTimestamps;

    protected $table = 'mails';

    protected $casts = [
        'from' => 'array',
        'replyTo' => 'array',
        'to' => 'array',
        'cc' => 'array',
        'bcc' => 'array',
    ];

    /**
     * @return string
     */
    public function calculateFamilyHash(): string
    {
        return md5(
            ($this->subject ?? '')
        );
    }

    /**
     * @param $query
     * @return MailEB
     */
    public function newEloquentBuilder($query): MailEB
    {
        return new MailEB($query);
    }

    /**
     * @return MailER
     */
    public static function repository(): MailER
    {
        return new MailER;
    }
}
