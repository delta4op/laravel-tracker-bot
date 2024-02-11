<?php

namespace Delta4op\Laravel\TrackerBot\Listeners;

use Delta4op\Laravel\TrackerBot\DB\Models\common\BrowserDetails;
use Delta4op\Laravel\TrackerBot\DB\Models\EventEntry\objects\AppRequestObject;
use Delta4op\Laravel\TrackerBot\Enums\EntryType;
use Delta4op\Laravel\TrackerBot\Support\FormatModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Http\Events\RequestHandled;
use Illuminate\Http\Request;
use Illuminate\Http\Response as IlluminateResponse;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;

class AppRequestListener extends Listener
{
    public function handle(RequestHandled $event): void
    {
        $this->logEntry(
            EntryType::APP_REQUEST,
            $this->prepareEventObject($event)
        );
    }

    protected function prepareEventObject(RequestHandled $event): AppRequestObject
    {
        $startTime = defined('LARAVEL_START') ? LARAVEL_START : $event->request->server('REQUEST_TIME_FLOAT');

        $object = new AppRequestObject();
        $object->ip = $event->request->ip();
        $object->ips = $event->request->ips();
        $object->uri = $event->request->getUri();
        $object->controllerAction = $event->request->route()?->getActionName();
        $object->middleware = array_values($event->request->route()?->gatherMiddleware() ?? []);
        $object->method = $event->request->getMethod();
        $object->headers = $this->headers($event->request->headers->all());

        $object->responseStatus = $event->response->getStatusCode();
        $object->responseHeaders = $this->headers($event->response->headers->all());
        $object->response = $this->response($event->response);

        $object->content = (string) $event->request->getContent();
        $object->input = $this->payload($this->input($event->request));
        $object->session = $this->payload($this->sessionVariables($event->request));
        $object->duration = $startTime ? floor((microtime(true) - $startTime) * 1000) : null;
        $object->memory = round(memory_get_peak_usage(true) / 1024 / 1024, 1);

        $object->browser()->associate(BrowserDetails::autoInit());

        return $object;
    }

    /**
     * Get headers to be saved in request objects.
     */
    protected function headers(array $headers): array
    {
        $headers = collect($headers)
            ->map(fn ($header) => implode(', ', $header))
            ->all();

        return $this->hideParameters($headers, []);
    }

    /**
     * Hide the given parameters.
     */
    protected function hideParameters(array $data, array $hidden): array
    {
        foreach ($hidden as $parameter) {
            if (Arr::get($data, $parameter)) {
                Arr::set($data, $parameter, '********');
            }
        }

        return $data;
    }

    /**
     * Format the given payload.
     */
    protected function payload(array $payload): array
    {
        return $this->hideParameters($payload,
            []
        );
    }

    /**
     * Extract the input from the given request.
     */
    private function input(Request $request): array
    {
        $files = $request->files->all();

        array_walk_recursive($files, function (&$file) {
            $file = [
                'name' => $file->getClientOriginalName(),
                'size' => $file->isFile() ? ($file->getSize() / 1000).'KB' : '0',
            ];
        });

        return array_replace_recursive($request->input(), $files);
    }

    /**
     * Extract the session variables from the given request.
     */
    private function sessionVariables(Request $request): array
    {
        return $request->hasSession() ? $request->session()->all() : [];
    }

    /**
     * Format the given response object.
     */
    protected function response(Response $response): array|string
    {
        $content = $response->getContent();

        if (is_string($content)) {
            if (is_array(json_decode($content, true)) &&
                json_last_error() === JSON_ERROR_NONE) {
                return $this->contentWithinLimits($content)
                    ? $this->hideParameters(json_decode($content, true), [])
                    : 'Purged By Tracker bot';
            }

            if (Str::startsWith(strtolower($response->headers->get('Content-Type') ?? ''), 'text/plain')) {
                return $this->contentWithinLimits($content) ? $content : 'Purged By TrackerBot';
            }
        }

        if ($response instanceof RedirectResponse) {
            return 'Redirected to '.$response->getTargetUrl();
        }

        if ($response instanceof IlluminateResponse && $response->getOriginalContent() instanceof View) {
            return [
                'view' => $response->getOriginalContent()->getPath(),
                'data' => $this->extractDataFromView($response->getOriginalContent()),
            ];
        }

        if (is_string($content) && empty($content)) {
            return 'Empty Response';
        }

        return 'HTML Response';
    }

    /**
     * Determine if the content is within the set limits.
     */
    public function contentWithinLimits(string $content): bool
    {
        $limit = $this->options['size_limit'] ?? 64;

        return intdiv(mb_strlen($content), 1000) <= $limit;
    }

    /**
     * Extract the data from the given view in array form.
     */
    protected function extractDataFromView(View $view): array
    {
        return collect($view->getData())->map(function ($value) {
            if ($value instanceof Model) {
                return FormatModel::given($value);
            } elseif (is_object($value)) {
                return [
                    'class' => get_class($value),
                    'properties' => json_decode(json_encode($value), true),
                ];
            } else {
                return json_decode(json_encode($value), true);
            }
        })->toArray();
    }
}
