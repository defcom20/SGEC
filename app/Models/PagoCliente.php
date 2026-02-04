<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PagoCliente extends Model
{
    use HasFactory, HasUuid;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'uuid',
        'factura_cliente_id',
        'numero_pago',
        'fecha_pago',
        'monto',
        'metodo_pago',
        'banco',
        'numero_operacion',
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
            'factura_cliente_id' => 'integer',
            'fecha_pago' => 'date',
            'monto' => 'decimal:2',
            'usuario_registro_id' => 'integer',
            'user_id' => 'integer',
        ];
    }

    public function facturaCliente(): BelongsTo
    {
        return $this->belongsTo(FacturaCliente::class);
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
