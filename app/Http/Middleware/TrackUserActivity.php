<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class TrackUserActivity
{
    public function handle(Request $request, Closure $next): Response
    {
        // Jika user sedang login, update last_seennya ke waktu sekarang
        if (Auth::check()) {
            
            // Memberitahu sistem bahwa ini adalah wujud asli dari Model User
            /** @var \App\Models\User $user */
            $user = Auth::user();
            
            $user->last_seen = now();
            $user->saveQuietly();
        }

        return $next($request);
    }
}
