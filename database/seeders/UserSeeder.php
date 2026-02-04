<?php

namespace Database\Seeders;

use App\Models\Rol;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Obtener roles
        $adminRol = Rol::where('nombre', 'Administrador')->first();
        $supervisorRol = Rol::where('nombre', 'Supervisor')->first();
        $operadorRol = Rol::where('nombre', 'Operador')->first();

        // Crear usuario administrador
        User::firstOrCreate(
            ['email' => 'admin@sgec.com'],
            [
                'name' => 'Administrador del Sistema',
                'password' => Hash::make('password'),
                'rol_id' => $adminRol?->id,
            ]
        );

        // Crear usuario supervisor
        User::firstOrCreate(
            ['email' => 'supervisor@sgec.com'],
            [
                'name' => 'Supervisor General',
                'password' => Hash::make('password'),
                'rol_id' => $supervisorRol?->id,
            ]
        );

        // Crear usuario operador
        User::firstOrCreate(
            ['email' => 'operador@sgec.com'],
            [
                'name' => 'Operador de Campo',
                'password' => Hash::make('password'),
                'rol_id' => $operadorRol?->id,
            ]
        );

        $this->command->info('✅ Usuarios de prueba creados');
        $this->command->info('   - admin@sgec.com (password)');
        $this->command->info('   - supervisor@sgec.com (password)');
        $this->command->info('   - operador@sgec.com (password)');
    }
}
