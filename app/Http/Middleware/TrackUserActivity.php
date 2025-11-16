<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TrackUserActivity
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check()) {
            $user = auth()->user();

            // Update last activity timestamp
            $user->update([
                'last_activity_at' => now(),
            ]);

            // Log user activity (only for important actions)
            if ($this->shouldLogActivity($request)) {
                activity()
                    ->causedBy($user)
                    ->withProperties([
                        'ip' => $request->ip(),
                        'user_agent' => $request->userAgent(),
                        'url' => $request->fullUrl(),
                        'method' => $request->method(),
                    ])
                    ->log('page_visit');
            }
        }

        return $next($request);
    }

    /**
     * Determine if the activity should be logged
     */
    protected function shouldLogActivity(Request $request): bool
    {
        // Don't log AJAX requests, API calls, or asset requests
        $excludedPaths = [
            'api/*',
            'livewire/*',
            '_ignition/*',
            'telescope/*',
        ];

        foreach ($excludedPaths as $pattern) {
            if ($request->is($pattern)) {
                return false;
            }
        }

        // Only log GET requests for page visits
        return $request->method() === 'GET';
    }
}
