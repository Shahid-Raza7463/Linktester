<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
use Predis\Client;
use Symfony\Component\HttpFoundation\Response;

class Linktester
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    // Middleware
    public function handle(Request $request, Closure $next): Response
    {
        $linkId = $request->route('unique_id');

        // Define a Redis key for the link checks,
        // $redisKey = "link:$linkId:limit";
        // Redis::set($redisKey, 5);

        // $remainingChecks = Redis::get($redisKey);

        // if ($remainingChecks === null || $remainingChecks <= 0) {
        //     return redirect()->route('dashboard', ['unique_id' => $linkId])->with('error', 'You have reached the maximum check limit.');
        // }

        // return $next($request, $redisKey);
        return $next($request);
    }
}
