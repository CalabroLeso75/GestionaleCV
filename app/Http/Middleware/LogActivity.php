<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;

class LogActivity
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, $type = 'action'): Response
    {
        $response = $next($request);

        // Only log if the user is authenticated (or you can log attempts too)
        // For now, let's log standard modifying actions or login events
        
        if (Auth::check()) {
            // Simplified logging logic - in production this might be more selective
            if ($request->isMethod('post') || $request->isMethod('put') || $request->isMethod('delete')) {
                 ActivityLog::create([
                    'user_id' => Auth::id(),
                    'action' => $request->path() . ' [' . $request->method() . ']',
                    'model' => null, // Would need more complex logic to determine model
                    'details' => json_encode($request->except(['password', 'password_confirmation'])),
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                ]);
            }
        }

        return $response;
    }
}
