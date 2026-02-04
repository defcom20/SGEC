<?php

namespace Database\Seeders;

use App\Models\Empresa;
use Illuminate\Database\Seeder;

class EmpresaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Empresa::firstOrCreate(
            ['ruc' => '20123456789'],
            [
                'razon_social' => 'SGEC - Sistema de Gestión Empresarial y Construcción S.A.C.',
                'nombre_comercial' => 'SGEC',
                'direccion' => 'Av. Principal 123, Lima, Perú',
                'telefono' => '+51 999 999 999',
                'email' => 'contacto@sgec.com',
            ]
        );

        $this->command->info('✅ Datos de empresa creados');
    }
}
