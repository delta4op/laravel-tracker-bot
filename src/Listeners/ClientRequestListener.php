<?php

namespace Delta4op\Laravel\TrackerBot\Listeners;

use Delta4op\Laravel\TrackerBot\DB\Models\objects\ClientRequestObject;
use Delta4op\Laravel\TrackerBot\Enums\EntryType;
use Illuminate\Http\Client\Events\ConnectionFailed;
use Illuminate\Http\Client\Events\ResponseReceived;
use Illuminate\Http\Client\Request;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\File\File;

class ClientRequestListener extends Listener
{
    public function handle(ResponseReceived|ConnectionFailed $event): void
    {
        $this->logEntry(
            EntryType::CLIENT_REQUEST,
            $this->prepareEventObject($event)
        );
    }

    protected function prepareEventObject(ResponseReceived|ConnectionFailed $event): ClientRequestObject
    {
        $object = new ClientRequestObject;
        $object->uri = $event->request->url();
        $object->method = $event->request->method();
        $object->headers = $this->headers($event->request->headers());
        $object->input = $this->payload($this->input($event->request));
        $object->content = $event->request->body();

        if ($event instanceof ResponseReceived) {
            $object->responseStatus = $event->response->status();
            $object->responseHeaders = $this->headers($event->response->headers());
            $object->response = $this->response($event->response);
            $object->duration = $this->duration($event->response);
        }

        return $object;
    }

    /**
     * Determine if the content is within the set limits.
     */
    public function contentWithinLimits(string $content): bool
    {
        $limit = $this->options['size_limit'] ?? 64;

        return mb_strlen($content) / 1000 <= $limit;
    }

    /**
     * Format the given response object.
     */
    protected function response(\Illuminate\Http\Client\Response $response): array|string
    {
        $content = $response->body();

        $stream = $response->toPsrResponse()->getBody();

        if ($stream->isSeekable()) {
            $stream->rewind();
        }

        if (is_string($content)) {
            if (is_array(json_decode($content, true)) &&
                json_last_error() === JSON_ERROR_NONE) {
                return $this->contentWithinLimits($content)
                    ? $this->hideParameters(json_decode($content, true), [])
                    : 'Purged By TrackerBot';
            }

            if (Str::startsWith(strtolower($response->header('Content-Type')), 'text/plain')) {
                return $this->contentWithinLimits($content) ? $content : 'Purged By TrackerBot';
            }
        }

        if ($response->redirect()) {
            return 'Redirected to '.$response->header('Location');
        }

        if (empty($content)) {
            return 'Empty Response';
        }

        return 'HTML Response';
    }

    /**
     * Format the given headers.
     *
     * @param  array  $headers
     */
    protected function headers($headers): array
    {
        $headerNames = collect($headers)->keys()->map(function ($headerName) {
            return strtolower($headerName);
        })->toArray();

        $headerValues = collect($headers)
            ->map(fn ($header) => implode(', ', $header))
            ->all();

        $headers = array_combine($headerNames, $headerValues);

        return $this->hideParameters($headers,
            $this->options['hidden'] ?? []
        );
    }

    /**
     * Format the given payload.
     *
     * @param  array  $payload
     */
    protected function payload($payload): array
    {
        return $this->hideParameters($payload,
            []
        );
    }

    /**
     * Hide the given parameters.
     *
     * @param  array  $data
     * @param  array  $hidden
     */
    protected function hideParameters(array $data, array $hidden = []): mixed
    {
        foreach ($hidden as $parameter) {
            if (Arr::get($data, $parameter)) {
                Arr::set($data, $parameter, '********');
            }
        }

        return $data;
    }

    /**
     * Extract the input from the given request.
     */
    protected function input(Request $request): array
    {
        if (! $request->isMultipart()) {
            return $request->data();
        }

        return collect($request->data())->mapWithKeys(function ($data) {
            if ($data['contents'] instanceof File) {
                $value = [
                    'name' => $data['filename'] ?? $data['contents']->getClientOriginalName(),
                    'size' => ($data['contents']->getSize() / 1000).'KB',
                    'headers' => $data['headers'] ?? [],
                ];
            } elseif (is_resource($data['contents'])) {
                $filesize = @filesize(stream_get_meta_data($data['contents'])['uri']);

                $value = [
                    'name' => $data['filename'] ?? null,
                    'size' => $filesize ? ($filesize / 1000).'KB' : null,
                    'headers' => $data['headers'] ?? [],
                ];
            } elseif (json_encode($data['contents']) === false) {
                $value = [
                    'name' => $data['filename'] ?? null,
                    'size' => (strlen($data['contents']) / 1000).'KB',
                    'headers' => $data['headers'] ?? [],
                ];
            } else {
                $value = $data['contents'];
            }

            return [$data['name'] => $value];
        })->toArray();
    }

    /**
     * Get the request duration in milliseconds.
     */
    protected function duration(Response $response): float|int|null
    {
        if (property_exists($response, 'transferStats') &&
            $response->transferStats &&
            $response->transferStats->getTransferTime()) {
            return floor($response->transferStats->getTransferTime() * 1000);
        }

        return null;
    }
}
