<?php

namespace Delta4op\Laravel\Tracker\Support;

use Illuminate\Support\Str;

trait FetchesStackTrace
{
    /**
     * Find the first frame in the stack trace outside of TrackerBot/Laravel.
     *
     * @param int|array|string $forgetLines
     */
    protected function getCallerFromStackTrace(int|array|string $forgetLines = 0): ?array
    {
        $trace = collect(debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS))->forget($forgetLines);

        return $trace->first(function ($frame) {
            if (! isset($frame['file'])) {
                return false;
            }

            return ! Str::contains($frame['file'], $this->ignoredPaths());
        });
    }

    /**
     * Get the file paths that should not be used by backtraces.
     */
    protected function ignoredPaths(): array
    {
        return array_merge(
            [base_path('vendor'.DIRECTORY_SEPARATOR.$this->ignoredVendorPath())],
            $this->options['ignore_paths'] ?? []
        );
    }

    /**
     * Choose the frame outside of either TrackerBot / Laravel or all packages.
     *
     * @return string|null
     */
    protected function ignoredVendorPath()
    {
        if (! ($this->options['ignore_packages'] ?? true)) {
            return 'laravel';
        }
    }
}
