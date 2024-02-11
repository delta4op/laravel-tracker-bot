<?php

namespace Delta4op\Laravel\TrackerBot;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Delta4op\Laravel\TrackerBot\Commands\SkeletonCommand;

class TrackerBotProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('tracker-bot')
            ->hasConfigFile('tracker-bot');
    }
}
