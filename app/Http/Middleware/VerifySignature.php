<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class VerifySignature
{
    public function handle(Request $request, Closure $next)
    {
        $sharedSecret = env('API_SHARED_SECRET');

        $signature = $request->header('X-API-Signature');
        $timestamp = $request->header('X-API-Timestamp');

        if (!$signature || !$timestamp) {
            return response()->json(['error' => 'Signature or timestamp missing'], 401);
        }

        // Get the full URL of the incoming request
        $url = $request->fullUrl();

        // Recreate the string to sign (URL + '|' + timestamp)
        $stringToSign = $url . '|' . $timestamp;

        // Calculate expected signature using HMAC SHA256
        $expectedSignature = hash_hmac('sha256', $stringToSign, $sharedSecret);

        // Timing safe compare signatures
        if (!hash_equals($expectedSignature, $signature)) {
            return response()->json(['error' => 'Invalid signature'], 401);
        }

        // Optional: check timestamp freshness (e.g., within 5 minutes)
        if (abs(time() - (int)$timestamp) > 300) {
            return response()->json(['error' => 'Timestamp expired'], 401);
        }

        return $next($request);
    }
}
