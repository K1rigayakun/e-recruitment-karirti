<?php

namespace App\Http\Middleware;

use App\Enums\UserRole;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Pastikan pengguna memiliki role yang diizinkan.
 * Role tidak pernah dari input; enum dipakai sebagai sumber kebenaran.
 */
class EnsureRole
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if (! $user) {
            return redirect()->route('login');
        }

        foreach ($roles as $role) {
            if ($user->role === UserRole::from($role)) {
                return $next($request);
            }
        }

        abort(403, 'Akses ditolak.');
    }
}
