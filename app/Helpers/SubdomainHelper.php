<?php

namespace App\Helpers;

class SubdomainHelper
{
    /**
     * Get current subdomain from active request.
     */
    public static function getCurrentSubdomain(): ?string
    {
        $host = TenantUrl::root();
        if (!$host) return null;

        $host = parse_url($host, PHP_URL_HOST);
        $segments = explode('.', $host);

        // Karena kamu jamin selalu pakai subdomain
        // minimal format: subdomain.domain.com
        return count($segments) >= 3 ? $segments[0] : null;
    }
}
