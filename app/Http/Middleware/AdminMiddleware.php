<?php
namespace App\Http\Middleware;

use Closure;

class AdminMiddleware
{
// app/Http/Middleware/AdminMiddleware.php
    public function handle($request, Closure $next)
    {
        if (! auth()->check() || ! auth()->user()->is_admin) {
            abort(403, 'Unauthorized');
        }
        return $next($request);
    }
}
