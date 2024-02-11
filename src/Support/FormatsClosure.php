<?php

namespace Delta4op\Laravel\TrackerBot\Support;

use Closure;
use ReflectionException;
use ReflectionFunction;

trait FormatsClosure
{
    /**
     * Format a closure-based listener.
     *
     * @param Closure $listener
     * @return string
     *
     * @throws ReflectionException
     */
    protected function formatClosureListener(Closure $listener): string
    {
        $listener = new ReflectionFunction($listener);

        return sprintf('Closure at %s[%s:%s]',
            $listener->getFileName(),
            $listener->getStartLine(),
            $listener->getEndLine()
        );
    }
}
