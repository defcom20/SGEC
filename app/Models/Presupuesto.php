<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Presupuesto extends Model
{
    use HasFactory, SoftDeletes, HasUuid;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'uuid',
        'numero_presupuesto',
        'fecha_emision',
        'fecha_vencimiento',
        'cliente_id',
        'persona_contacto',
        'supervisor_id',
        'estado',
        'fecha_aceptacion',
        'fecha_inicio',
        'fecha_finalizacion_estimada',
        'periodo_ejecucion_dias',
        'base_imponible',
        'igv',
        'descuento_porcentaje',
        'descuento_monto',
        'total',
        'observaciones',
        'usuario_creacion_id',
        'usuario_modificacion_id',
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
            'fecha_emision' => 'date',
            'fecha_vencimiento' => 'date',
            'cliente_id' => 'integer',
            'supervisor_id' => 'integer',
            'fecha_aceptacion' => 'date',
            'fecha_inicio' => 'date',
            'fecha_finalizacion_estimada' => 'date',
            'base_imponible' => 'decimal:2',
            'igv' => 'decimal:2',
            'descuento_porcentaje' => 'decimal:2',
            'descuento_monto' => 'decimal:2',
            'total' => 'decimal:2',
            'usuario_creacion_id' => 'integer',
            'usuario_modificacion_id' => 'integer',
            'user_id' => 'integer',
        ];
    }

    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function supervisor(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function usuarioCreacion(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function usuarioModificacion(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function presupuestoDetalles(): HasMany
    {
        return $this->hasMany(PresupuestoDetalle::class);
    }

    public function ordenServicios(): HasMany
    {
        return $this->hasMany(OrdenServicio::class);
    }

    public function facturaCliente(): HasOne
    {
        return $this->hasOne(FacturaCliente::class);
    }
}
