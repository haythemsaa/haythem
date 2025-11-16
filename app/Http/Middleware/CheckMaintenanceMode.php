<?php

namespace App\Http\Middleware;

use App\Models\Setting;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckMaintenanceMode
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if maintenance mode is enabled in settings
        $maintenanceMode = Setting::where('key', 'app.maintenance_mode')
            ->first()?->value ?? false;

        if ($maintenanceMode && !$this->isExempt($request)) {
            // Get maintenance message
            $message = Setting::where('key', 'app.maintenance_message')
                ->first()?->value ?? 'Le système est actuellement en maintenance. Veuillez réessayer plus tard.';

            if ($request->expectsJson()) {
                return response()->json([
                    'message' => $message,
                    'status' => 'maintenance',
                ], 503);
            }

            abort(503, $message);
        }

        return $next($request);
    }

    /**
     * Determine if the request is exempt from maintenance mode
     */
    protected function isExempt(Request $request): bool
    {
        // Allow Super Admins to access during maintenance
        if (auth()->check() && auth()->user()->hasRole('Super Admin')) {
            return true;
        }

        // Allow access to login and logout routes
        $exemptRoutes = [
            'login',
            'logout',
            'password/*',
        ];

        foreach ($exemptRoutes as $route) {
            if ($request->is($route)) {
                return true;
            }
        }

        return false;
    }
}
