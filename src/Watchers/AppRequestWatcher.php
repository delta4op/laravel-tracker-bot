<?php

namespace Delta4op\Laravel\Tracker\Watchers;

use Delta4op\Laravel\Tracker\DB\Models\Metrics\AppRequest;
use Delta4op\Laravel\Tracker\Enums\HttpMethod;
use Delta4op\Laravel\Tracker\Tracker;
use Delta4op\Laravel\Tracker\Support\FormatModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Http\Events\RequestHandled;
use Illuminate\Http\Request;
use Illuminate\Http\Response as IlluminateResponse;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class AppRequestWatcher extends Watcher
{
    /**
     * @param Application $app
     * @return void
     */
    public function register(Application $app): void
    {
        $app['events']->listen(RequestHandled::class, [$this, 'recordAppRequest']);
    }

    public function recordAppRequest(RequestHandled $event): void
    {
        if (
            !Tracker::isRecording() ||
            !$this->isWatcherEnabled() ||
            $this->shouldIgnoreHttpMethod($event) ||
            $this->shouldIgnoreStatusCode($event)) {
            return;
        }

        Tracker::recordEntry(
            $this->prepareAppRequest($event)
        );
    }

    /**
     * @param RequestHandled $event
     * @return AppRequest
     */
    protected function prepareAppRequest(RequestHandled $event): AppRequest
    {
        $startTime = defined('LARAVEL_START') ? LARAVEL_START : $event->request->server('REQUEST_TIME_FLOAT');

        $appRequest = new AppRequest;
        $appRequest->secure = $event->request->secure();
        $appRequest->protocol = Str::upper($event->request->getProtocolVersion());
        $appRequest->method = HttpMethod::tryFrom(Str::upper($event->request->getMethod()));
        $appRequest->host = $event->request->host();
        $appRequest->path = $event->request->path();
        $appRequest->url = $event->request->url();
        $appRequest->full_url = $event->request->fullUrl();
        $appRequest->query_string = $event->request->getQueryString();
        $appRequest->ip = $event->request->ip();
        $appRequest->ips = $event->request->ips();
        $appRequest->middleware = array_values($event->request->route()?->gatherMiddleware() ?? []);
        $appRequest->headers = $this->headers($event->request->headers->all());
        $appRequest->content = (string)$event->request->getContent();
        $appRequest->session = $this->payload($this->sessionVariables($event->request));
        $appRequest->cookies = [];

        $appRequest->response_content = $event->response->getContent();
        $appRequest->response_status = $event->response->getStatusCode();
        $appRequest->response_headers = $this->headers($event->response->headers->all());

        $appRequest->duration = $startTime ? floor((microtime(true) - $startTime) * 1000) : null;
        $appRequest->memory = round(memory_get_peak_usage(true) / 1024 / 1024, 2);

        try {
            $appRequest->path_template = Route::current()?->uri();
        } catch(Throwable) {}


        if(!$appRequest->path_template) {
            $appRequest->path_template = $appRequest->path;
        }

        try {
            $appRequest->controller_action = $event->request->route()?->getActionName();
            $appRequest->controller_class = $event->request->route()?->getControllerClass();
        } catch (Throwable) {
        }

        return $appRequest;
    }

    /**
     * Get headers to be saved in request objects.
     */
    protected function headers(array $headers): array
    {
        $headers = collect($headers)
            ->map(fn($header) => implode(', ', $header))
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
                'size' => $file->isFile() ? ($file->getSize() / 1000) . 'KB' : '0',
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
            return 'Redirected to ' . $response->getTargetUrl();
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

    /**
     * Determine if the request should be ignored based on its method.
     *
     * @param mixed $event
     * @return bool
     */
    protected function shouldIgnoreHttpMethod($event): bool
    {
        return in_array(
            strtolower($event->request->method()),
            collect($this->options['ignore_http_methods'] ?? [])->map(function ($method) {
                return strtolower($method);
            })->all()
        );
    }

    /**
     * Determine if the request should be ignored based on its status code.
     *
     * @param mixed $event
     * @return bool
     */
    protected function shouldIgnoreStatusCode($event): bool
    {
        return in_array(
            $event->response->getStatusCode(),
            $this->options['ignore_status_codes'] ?? []
        );
    }
}
