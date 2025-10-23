<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\User;
use App\Models\Role;

class UserFactory extends Factory
{
    protected $model = User::class;

    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => Hash::make('password'),  // Default password
            'po_code' => null,  // Generate saat dibutuhkan
            'remember_token' => Str::random(10),
        ];
    }

    // State untuk role spesifik (opsional, untuk testing)
    public function withRole($roleName = 'user'): static
    {
        return $this->afterCreating(function (User $user) use ($roleName) {
            $role = Role::where('name', $roleName)->first();
            if ($role) {
                $user->roles()->attach($role);
            }
        });
    }
}