<?php

namespace App\Helpers;

use Illuminate\Support\Facades\URL;

class TenantUrl
{
    /**
     * Generate full URL for storage file (public disk).
     */
    public static function asset(?string $path): ?string
    {
        if (!$path) {
            return null;
        }

        return URL::to('storage/' . ltrim($path, '/'));
    }

    /**
     * Generate full URL from relative path.
     */
    public static function url(string $path = ''): string
    {
        return URL::to(ltrim($path, '/'));
    }

    /**
     * Get current root URL (scheme + host).
     */
    public static function root(): string
    {
        return URL::to('/');
    }
}
