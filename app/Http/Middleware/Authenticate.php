<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */

    protected function redirectTo($request)
    {
        if (! $request->expectsJson()) {
                return response()->json(['message' => 'Unauthenticated or Token Expired'], 401);
        }

            // For web requests, redirect to the login page
    return redirect()->guest(route('login'));
    }

}
