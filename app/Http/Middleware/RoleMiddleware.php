<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if (!$user) {
            return redirect()->route('login');
        }

        if (!in_array($user->role, $roles, true)) {

            if ($user->role === 'petugas') {
                return redirect()->route('petugas.attendance');
            }

            if ($user->role === 'admin') {
                return redirect()->route('dashboard');
            }

            abort(403, 'Anda tidak memiliki akses.');
        }

        return $next($request);
    }
}
