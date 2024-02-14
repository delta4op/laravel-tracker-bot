<?php

namespace Delta4op\Laravel\TrackerBot;

use Delta4op\Laravel\TrackerBot\DB\Models\Environment;
use Delta4op\Laravel\TrackerBot\DB\Models\Source;
use Illuminate\Support\Str;

class TrackerBot
{
    protected array $configs = [];

    public function __construct()
    {
        $this->configs = config('tracker-bot');
    }

    /**
     * @return Source|null
     */
    public function getSource(): ?Source
    {
        $sourceSymbol = Str::upper($this->configs['source'] ?? 'MASTER');

        /** @var ?Source */
        return Source::query()->firstOrCreate([
            'symbol' => $sourceSymbol,
        ]);
    }

    /**
     * @return Environment|null
     */
    public function getEnvironment(): ?Environment
    {
        $envSymbol = Str::upper($this->configs['env'] ?? 'DEFAULT');

        /** @var ?Environment */
        return Environment::query()->firstOrCreate([
            'symbol' => $envSymbol,
        ]);
    }

    /**
     * @return bool
     */
    public function isEnabled(): bool
    {
        $enabled = config('tracker-bot.enabled', false);

        return in_array($enabled, [true, 'true']);
    }

    /**
     * @return bool
     */
    public function isDisabled(): bool
    {
        return !static::isEnabled();
    }
}
