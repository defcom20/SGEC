<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class AddUuidToModels extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'models:add-uuid';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Add HasUuid trait to all models that have uuid field';

    /**
     * Models that should have UUID
     */
    protected array $modelsWithUuid = [
        'Rol',
        'Permiso',
        'Cliente',
        'Subcontratista',
        'Proveedor',
        'Articulo',
        'Servicio',
        'Presupuesto',
        'PresupuestoDetalle',
        'OrdenServicio',
        'OrdenServicioDetalle',
        'FacturaCliente',
        'PagoCliente',
        'FacturaSubcontratista',
        'PagoSubcontratista',
        'Empresa',
        'Parametro',
        'Modulo',
    ];

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Adding HasUuid trait to models...');

        foreach ($this->modelsWithUuid as $modelName) {
            $this->addUuidToModel($modelName);
        }

        $this->info('✅ Done! All models now have HasUuid trait.');
    }

    /**
     * Add HasUuid trait to a specific model
     */
    protected function addUuidToModel(string $modelName): void
    {
        $path = app_path("Models/{$modelName}.php");

        if (!File::exists($path)) {
            $this->warn("⚠️  Model {$modelName} not found, skipping...");
            return;
        }

        $content = File::get($path);

        // Check if already has the trait
        if (str_contains($content, 'use HasUuid')) {
            $this->line("   {$modelName} already has HasUuid trait");
            return;
        }

        // Add use statement
        if (!str_contains($content, 'use App\\Traits\\HasUuid;')) {
            $content = preg_replace(
                '/(namespace App\\\\Models;.*?\n)/s',
                "$1\nuse App\\Traits\\HasUuid;",
                $content
            );
        }

        // Add trait to class
        $content = preg_replace(
            '/(use\s+HasFactory(?:,\s*\w+)*);/',
            '$1, HasUuid;',
            $content
        );

        File::put($path, $content);
        $this->info("✅ {$modelName} updated");
    }
}
