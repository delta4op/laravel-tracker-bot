<?php

namespace Delta4op\Laravel\Tracker\Watchers;

use Delta4op\Laravel\Tracker\DB\Models\Metrics\Mail;
use Delta4op\Laravel\Tracker\Tracker;
use Illuminate\Foundation\Application;
use Illuminate\Mail\Events\MessageSent;
use Symfony\Component\Mime\Address;

class MailWatcher extends Watcher
{
    /**
     * Register the watcher.
     *
     * @param Application $app
     * @return void
     */
    public function register(Application $app): void
    {
        $app['events']->listen(MessageSent::class, [$this, 'recordMail']);
    }

    /**
     * @param MessageSent $event
     * @return void
     */
    public function recordMail(MessageSent $event): void
    {
        if (!Tracker::isRecording()) {
            return;
        }

        Tracker::recordEntry(
            $this->prepareMail($event)
        );
    }

    /**
     * @param MessageSent $event
     * @return Mail
     */
    protected function prepareMail(MessageSent $event): Mail
    {
        $object = new Mail;

        $body = $event->message->getBody();

        $object->mailable = $this->getMailable($event);
        $object->queued = $this->getQueuedStatus($event);
        $object->from = $this->formatAddresses($event->message->getFrom());
        $object->replyTo = $this->formatAddresses($event->message->getReplyTo());
        $object->to = $this->formatAddresses($event->message->getTo());
        $object->cc = $this->formatAddresses($event->message->getCc());
        $object->bcc = $this->formatAddresses($event->message->getBcc());
        $object->subject = $event->message->getSubject();
        $object->html = (string)($event->message->getHtmlBody() ?? $event->message->getTextBody());
        $object->raw = $event->message->toString();

        return $object;
    }

    /**
     * Get the name of the mailable.
     *
     * @param MessageSent $event
     * @return string
     */
    protected function getMailable(MessageSent $event): string
    {
        if (isset($event->data['__laravel_notification'])) {
            return $event->data['__laravel_notification'];
        }

        return $event->data['__telescope_mailable'] ?? '';
    }

    /**
     * Determine whether the mailable was queued.
     *
     * @param MessageSent $event
     * @return bool
     */
    protected function getQueuedStatus(MessageSent $event): bool
    {
        if (isset($event->data['__laravel_notification_queued'])) {
            return $event->data['__laravel_notification_queued'];
        }

        return $event->data['__telescope_queued'] ?? false;
    }

    /**
     * Convert the given addresses into a readable format.
     *
     * @param  array|null  $addresses
     * @return array
     */
    protected function formatAddresses(?array $addresses): array
    {
        if (is_null($addresses)) {
            return [];
        }

        return collect($addresses)->flatMap(function ($address, $key) {
            if ($address instanceof Address) {
                return [$address->getAddress() => $address->getName()];
            }

            return [$key => $address];
        })->all();
    }

}
