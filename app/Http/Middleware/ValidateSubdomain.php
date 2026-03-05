<?php

namespace App\Http\Middleware;

use App\Models\Restaurant;
use App\Traits\ApiResponserTrait;
use Closure;
use Illuminate\Http\Request;

class ValidateSubdomain
{
    use ApiResponserTrait;

    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next): mixed
    {
        $subdomain = $request->route('subdomain');
        $restaurant = Restaurant::where('subdomain', $subdomain)->where('is_active', true)->firstOrFail();

        $request->merge(['restaurant' => $restaurant]);
        return $next($request);
    }
}
