<?php

namespace Delta4op\Laravel\TrackerBot\DB\Models\EventEntry\objects;

/**
 * @property ?string $mailable
 * @property ?bool $queued
 * @property ?string[] $from
 * @property ?string[] $replyTo
 * @property ?string[] $to
 * @property ?string[] $cc
 * @property ?string[] $bcc
 * @property ?string $subject
 * @property ?string $html
 * @property ?string $raw
 */
class MailObject extends EntryObject
{
    // todo define speed rate
}
