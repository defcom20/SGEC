<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->command->info('🌱 Iniciando seeders del sistema SGEC...');
        $this->command->info('');

        // 1. Módulos del sistema
        $this->call(ModuloSeeder::class);

        // 2. Roles
        $this->call(RolSeeder::class);

        // 3. Permisos (basados en módulos)
        $this->call(PermisoSeeder::class);

        // 4. Asignar permisos a roles
        $this->call(RolPermisoSeeder::class);

        // 5. Usuarios de prueba
        $this->call(UserSeeder::class);

        $this->command->info('');
        $this->command->info('🎉 ¡Seeders completados exitosamente!');
        $this->command->info('');
        $this->command->info('🚀 Ya puedes iniciar sesión con:');
        $this->command->info('   📧 Email: admin@sgec.com');
        $this->command->info('   🔑 Password: admin123');
    }
}
