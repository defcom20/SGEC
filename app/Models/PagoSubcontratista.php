<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PagoSubcontratista extends Model
{
    use HasFactory, HasUuid;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'uuid',
        'factura_subcontratista_id',
        'numero_pago',
        'fecha_pago',
        'monto',
        'metodo_pago',
        'banco',
        'numero_operacion',
        'cuenta_detraccion_usada',
        'comprobante',
        'observaciones',
        'usuario_registro_id',
        'user_id',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'id' => 'integer',
            'factura_subcontratista_id' => 'integer',
            'fecha_pago' => 'date',
            'monto' => 'decimal:2',
            'cuenta_detraccion_usada' => 'boolean',
            'usuario_registro_id' => 'integer',
            'user_id' => 'integer',
        ];
    }

    public function facturaSubcontratista(): BelongsTo
    {
        return $this->belongsTo(FacturaSubcontratista::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function usuarioRegistro(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
