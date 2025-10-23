<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Role check
        $adminRole = Role::where('name', 'admin')->first();
        $kasirRole = Role::where('name', 'kasir')->first();
        $userRole = Role::where('name', 'user')->first();

        if (!$adminRole || !$kasirRole || !$userRole) {
            echo "Error: Role belum di-seed.\n";
            return;
        }

        // Factory untuk user biasa (3 contoh random)
        User::factory(3)->create()->each(function ($u) use ($userRole) {
            $u->roles()->attach($userRole);
        });

        // Manual untuk admin & kasir (atau pakai factory dengan state)
        $admin = User::factory()->withRole('admin')->create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
        ]);
        $kasir = User::factory()->withRole('kasir')->create([
            'name' => 'Kasir User',
            'email' => 'kasir@example.com',
        ]);

        echo "Seeder selesai! User dibuat dengan factory.\n";
        echo "- Admin: admin@example.com / password\n";
        echo "- Kasir: kasir@example.com / password\n";
        echo "- User: user@example.com / password (dan 3 random user)\n";
    }
}