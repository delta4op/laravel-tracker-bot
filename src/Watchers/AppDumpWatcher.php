<?php

namespace Delta4op\Laravel\Tracker\Watchers;

use Delta4op\Laravel\Tracker\DB\Models\Metrics\Dump;
use Delta4op\Laravel\Tracker\Tracker;
use Exception;
use Illuminate\Contracts\Cache\Factory as CacheFactory;
use Illuminate\Contracts\Foundation\Application;
use Symfony\Component\VarDumper\Cloner\VarCloner;
use Symfony\Component\VarDumper\Dumper\HtmlDumper;
use Symfony\Component\VarDumper\VarDumper;

class AppDumpWatcher extends Watcher
{
    /**
     * The cache factory implementation.
     *
     * @var CacheFactory
     */
    protected CacheFactory $cache;

    /**
     * Create a new watcher instance.
     *
     * @param CacheFactory $cache
     * @param  array  $options
     * @return void
     */
    public function __construct(CacheFactory $cache, array $options = [])
    {
        parent::__construct($options);

        $this->cache = $cache;
    }

    /**
     * Register the watcher.
     *
     * @param  Application  $app
     * @return void
     */
    public function register(Application $app): void
    {
        $dumpWatcherCache = false;

        try {
            $dumpWatcherCache = $this->cache->get('tracker:dump-watcher');
        } catch (Exception) {
            //
        }

        if (! ($this->options['always'] ?? false) && ! $dumpWatcherCache) {
            return;
        }

        $htmlDumper = new HtmlDumper;
        $htmlDumper->setDumpHeader('');

        VarDumper::setHandler(function ($var) use ($htmlDumper) {
            $this->recordDump($htmlDumper->dump(
                (new VarCloner)->cloneVar($var), true
            ));
        });
    }

    /**
     * @param string|null $content
     * @return void
     */
    public function recordDump(string|null $content): void
    {
        Tracker::recordEntry(
            $this->prepareAppDump($content)
        );
    }

    /**
     * @param string $content
     * @return Dump
     */
    protected function prepareAppDump(string $content): Dump
    {
        $object = new Dump;
        $object->content = $content;

        return $object;
    }
}
