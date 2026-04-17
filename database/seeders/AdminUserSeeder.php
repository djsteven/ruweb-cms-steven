<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => config('cms.default_admin.email')],
            [
                'name' => config('cms.default_admin.name'),
                'password' => Hash::make(config('cms.default_admin.password')),
                'role' => 'admin',
                'email_verified_at' => now(),
            ]
        );

        // Backfill any existing users that were created before email verification
        // was enforced, so sign-in isn't blocked by unverified state.
        User::whereNull('email_verified_at')->update(['email_verified_at' => now()]);
    }
}
