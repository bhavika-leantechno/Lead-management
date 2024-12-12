<?php
namespace App\Http\Middleware;

use Closure;
use Exception;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;

class CheckJwtToken
{
    public function handle($request, Closure $next)
    {
        try {
            // Authenticate user with token
            JWTAuth::parseToken()->authenticate();
        } catch (TokenExpiredException $e) {
            // Handle expired token
            return response()->json(['message' => 'Token has expired'], 401);
        } catch (TokenInvalidException $e) {
            // Handle invalid token
            return response()->json(['message' => 'Invalid token'], 401);
        } catch (Exception $e) {
            // Handle other exceptions
            return response()->json(['message' => 'Token not found'], 401);
        }

        return $next($request);
    }
}
