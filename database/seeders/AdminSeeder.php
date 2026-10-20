<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        $email = env('ADMIN_EMAIL', '');
        $password = env('ADMIN_PASSWORD', '');

        // Di environment local: gunakan default demo jika env belum diisi
        if (app()->environment('local')) {
            if (empty($email)) {
                $email = 'admin@karirti.test';
            }
            if (empty($password)) {
                $password = 'Password123!';
            }
        } elseif (empty($email) || empty($password)) {
            // Production: wajib diisi
            throw new \RuntimeException(
                'ADMIN_EMAIL dan ADMIN_PASSWORD wajib diisi di .env untuk environment non-local.'
            );
        }

        $user = User::updateOrCreate(
            ['email' => $email],
            ['name' => 'Admin KarirTI', 'password' => $password]
        );

        $user->role = UserRole::Admin;
        $user->is_active = true;
        $user->email_verified_at = $user->email_verified_at ?? now();
        $user->save();

        $this->command->info("Admin dibuat: {$email}");
    }
}
