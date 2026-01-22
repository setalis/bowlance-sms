<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Seeder;

class AdminRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Назначаем роль администратора первому пользователю, если он существует
        $firstUser = User::first();
        if ($firstUser) {
            $firstUser->update(['role' => UserRole::Admin]);
        }
    }
}
