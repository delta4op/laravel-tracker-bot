<?php

namespace Delta4op\Laravel\Tracker\Helpers;

use Illuminate\Support\Str;

class FileHelpers
{
    /**
     * @param string $file
     * @return bool
     */
    public static function isInternalFile(string $file): bool
    {
        return Str::startsWith($file, base_path('vendor' . DIRECTORY_SEPARATOR . 'laravel' . DIRECTORY_SEPARATOR . 'pulse'))
            || Str::startsWith($file, base_path('vendor' . DIRECTORY_SEPARATOR . 'laravel' . DIRECTORY_SEPARATOR . 'framework'))
            || $file === base_path('artisan')
            || $file === public_path('index.php');
    }
}
