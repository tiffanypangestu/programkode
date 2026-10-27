<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();
        if (! $user) {
            return redirect()->route('login');
        }

        $namaRole = $user->role?->nama_role;

        if (! $namaRole) {
            abort(403, 'ANDA TIDAK MEMILIKI HAK AKSES!');
        }

        // Peta kesetaraan singkatan dan nama lengkap (Dua arah)
        $roleMap = [
            'bc' => 'beauty consultant',
            'spg' => 'sales promotion girl',
            'beauty consultant' => 'bc',
            'sales promotion girl' => 'spg',
            'admin' => 'admin',
            'supervisor' => 'supervisor',
        ];

        $userRole = strtolower($namaRole);
        $allowedRoles = array_map('strtolower', $roles);

        // Cek apakah role user langsung cocok ATAU versi peta/aliasnya cocok dengan role yang diizinkan
        $isAllowed = in_array($userRole, $allowedRoles, true) ||
                     (isset($roleMap[$userRole]) && in_array($roleMap[$userRole], $allowedRoles, true));

        if (! $isAllowed) {
            abort(403, 'ANDA TIDAK MEMILIKI HAK AKSES!');
        }

        return $next($request);
    }
}
