<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class ConvertPointsToXp extends Seeder
{
    public function run()
    {
        User::all()->each(function ($user) {
            $user->xp = $user->points * 100; // Konversi points ke XP
            $user->save();
        });
    }
}