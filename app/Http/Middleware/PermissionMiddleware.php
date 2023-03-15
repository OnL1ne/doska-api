<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Closure;
use Tymon\JWTAuth\JWTAuth;

class PermissionMiddleware
{
    /**
     * @param Request $request
     * @param Closure $next
     * @param string $permission
     * @return \Illuminate\Http\JsonResponse
     */
    public function handle($request, Closure $next, $permission)
    {
        $user = auth()->user();
        if (!$user) {
            return response()->json(['message' => 'Error!']);
        }

        $permission = $user->role ? $user->role->permissions()->where('title', $permission)->first() : false;
        if (!$permission) {
            return response()->json(['message' => 'Permission is denied']);
        }

        return $next($request);
    }
}
