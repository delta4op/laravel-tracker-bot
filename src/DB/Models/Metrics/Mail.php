<?php

namespace Delta4op\Laravel\TrackerBot\DB\Models\Metrics;

use Delta4op\Laravel\TrackerBot\DB\Concerns\HasTimestamps;

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
}
