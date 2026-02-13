<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;
use Illuminate\Http\Request;

class ExcludeCsrfFromLogout extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array<int, string>
     */
    protected $except = [
        'logout',
        '/logout',
        '*logout*',
    ];

    /**
     * Handle an incoming request.
     */
    protected function tokensMatch($request)
    {
        \Log::info('CSRF check for: ' . $request->path());
        \Log::info('Request method: ' . $request->method());
        
        if ($this->isReading($request) || $this->runningUnitTests() || $this->inExceptArray($request)) {
            \Log::info('Request excluded from CSRF verification');
            return true;
        }
        
        \Log::info('CSRF token verification proceeding');
        return parent::tokensMatch($request);
    }
}
