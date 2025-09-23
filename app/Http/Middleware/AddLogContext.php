<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str; // Don't forget to import Str
use Symfony\Component\HttpFoundation\Response;

class AddLogContext
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        Log::withContext([
            'url' => $request->fullUrl(),
            'method' => $request->method(),
        ]);

        return $next($request);
    }
}