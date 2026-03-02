<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Проверяем, существует ли уже администратор
        $adminExists = User::where('role', UserRole::Admin)->exists();

        if ($adminExists) {
            $this->command->info('Администратор уже существует в базе данных.');

            return;
        }

        // Создаем администратора
        $admin = User::create([
            'name' => 'Администратор',
            'email' => 'admin@bowlance.ge',
            'phone' => '+995555555555',
            'password' => Hash::make('admin'),
            'role' => UserRole::Admin,
            'email_verified_at' => now(),
        ]);

        $this->command->info('Администратор успешно создан!');
        $this->command->table(
            ['Поле', 'Значение'],
            [
                ['Email', $admin->email],
                ['Phone', $admin->phone],
                ['Password', 'password'],
                ['Role', $admin->role->value],
            ]
        );
        $this->command->warn('⚠️  ВАЖНО: Обязательно смените пароль после первого входа!');
    }
}

