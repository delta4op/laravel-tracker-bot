<?php

namespace Delta4op\Laravel\TrackerBot\Support;

use Illuminate\Support\Str;

class Helpers
{
    public static function isInternalFile(string $file): bool
    {
        return Str::startsWith($file, base_path('vendor'.DIRECTORY_SEPARATOR.'laravel'.DIRECTORY_SEPARATOR.'pulse'))
            || Str::startsWith($file, base_path('vendor'.DIRECTORY_SEPARATOR.'laravel'.DIRECTORY_SEPARATOR.'framework'))
            || $file === base_path('artisan')
            || $file === public_path('index.php');
    }
}
