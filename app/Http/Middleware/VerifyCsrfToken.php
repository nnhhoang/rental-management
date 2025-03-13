<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that are exempt from CSRF verification.
     *
     * @var array<int, string>
     */
    protected $except = [
        // List routes that should be exempt from CSRF protection
        // Useful for API routes or external webhooks
        // '/api/*',
    ];
}
