<?php

namespace App\Http\Middleware;

use App\Repositories\Response;

class FormattingRequestMiddleware
{
    public function handle($request, \Closure $next)
    {
        return $next($request);
    }
}