<?php

namespace App\Http\Controllers\Admin;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(Request $request): View
    {
        $users = User::query()
            ->when($request->get('q'), function ($q, $s) {
                $q->where(function ($sub) use ($s) {
                    $sub->where('name', 'like', '%'.escapeLike($s).'%')
                        ->orWhere('email', 'like', '%'.escapeLike($s).'%');
                });
            })
            ->when($request->get('role'), fn ($q, $r) => $q->where('role', $r))
            ->when($request->get('status'), fn ($q, $s) => $q->where('is_active', $s === 'active'))
            ->latest()
            ->paginate(20)->withQueryString();

        return view('admin.users.index', compact('users'));
    }

    /** Toggle aktif/nonaktif — BR13: tidak boleh menonaktifkan diri sendiri atau admin terakhir */
    public function toggleActive(Request $request, User $user): RedirectResponse
    {
        // BR13: jangan nonaktifkan diri sendiri
        if ($user->id === $request->user()->id) {
            return back()->withErrors(['toggle' => 'Kamu tidak bisa menonaktifkan akunmu sendiri.']);
        }

        // BR13: jika nonaktifkan, pastikan bukan admin terakhir
        if ($user->is_active && $user->role === UserRole::Admin) {
            $activeAdmins = User::where('role', UserRole::Admin->value)
                ->where('is_active', true)
                ->count();

            if ($activeAdmins <= 1) {
                return back()->withErrors(['toggle' => 'Tidak bisa menonaktifkan admin terakhir.']);
            }
        }

        $user->is_active = ! $user->is_active;
        $user->save();

        // Audit log
        AuditLog::create([
            'user_id' => $request->user()->id,
            'action' => $user->is_active ? 'user.activated' : 'user.deactivated',
            'subject_type' => User::class,
            'subject_id' => $user->id,
            'ip' => $request->ip(),
            'created_at' => now(),
        ]);

        $status = $user->is_active ? 'diaktifkan' : 'dinonaktifkan';

        return back()->with('toast', "Pengguna {$user->name} berhasil {$status}.");
    }
}
