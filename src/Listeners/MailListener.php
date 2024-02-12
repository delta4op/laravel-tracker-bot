<?php

namespace Delta4op\Laravel\TrackerBot\Listeners;

use Delta4op\Laravel\TrackerBot\DB\Models\objects\MailObject;
use Delta4op\Laravel\TrackerBot\Enums\EntryType;
use Delta4op\Laravel\TrackerBot\Facades\TrackerBot;
use Illuminate\Mail\Events\MessageSent;
use Symfony\Component\Mime\Address;

class MailListener extends Listener
{
    /**
     * @param MessageSent $event
     * @return void
     */
    public function handle(MessageSent $event): void
    {
        if(!TrackerBot::isEnabled()) {
            return;
        }

        $this->logEntry(
            EntryType::MAIL,
            $this->prepareEventObject($event)
        );
    }

    /**
     * @param MessageSent $event
     * @return MailObject|null
     */
    protected function prepareEventObject(MessageSent $event): ?MailObject
    {
        $object = new MailObject;

        $object->mailable = $this->getMailable($event);
        $object->queued = $this->getQueuedStatus($event);
        $object->from = $this->formatAddresses($event->message->getFrom());
        $object->replyTo = $this->formatAddresses($event->message->getReplyTo());
        $object->to = $this->formatAddresses($event->message->getTo());
        $object->cc = $this->formatAddresses($event->message->getCc());
        $object->bcc = $this->formatAddresses($event->message->getBcc());
        $object->subject = $event->message->getSubject();
        $object->html = (string) ($event->message->getHtmlBody() ?? $event->message->getTextBody());
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

        return $event->data['__trackerbot_mailable'] ?? '';
    }

    /**
     * Determine whether the mailable was queued.
     */
    protected function getQueuedStatus(MessageSent $event): bool
    {
        if (isset($event->data['__laravel_notification_queued'])) {
            return $event->data['__laravel_notification_queued'];
        }

        return $event->data['__trackerbot_queued'] ?? false;
    }

    /**
     * Convert the given addresses into a readable format.
     */
    protected function formatAddresses(?array $addresses): ?array
    {
        if (is_null($addresses)) {
            return null;
        }

        return collect($addresses)->flatMap(function ($address, $key) {
            if ($address instanceof Address) {
                return [$address->getAddress() => $address->getName()];
            }

            return [$key => $address];
        })->all();
    }
}
