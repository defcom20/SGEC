<?php

namespace Database\Seeders;

use App\Models\Modulo;
use App\Models\Permiso;
use Illuminate\Database\Seeder;

class PermisoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Obtener solo los SUBMÓDULOS (nivel 2) para generar permisos
        $submodulos = Modulo::where('nivel', 2)->get();

        foreach ($submodulos as $modulo) {
            $acciones = $modulo->acciones ?? ['ver'];

            foreach ($acciones as $accion) {
                Permiso::create([
                    'modulo' => $modulo->slug,
                    'accion' => $accion,
                    'descripcion' => 'Permiso para ' . $accion . ' en ' . $modulo->nombre,
                ]);
            }
        }

        $this->command->info('✅ Permisos generados desde submódulos');
    }
}
