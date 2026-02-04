<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class FacturaCliente extends Model
{
    use HasFactory, SoftDeletes, HasUuid;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'uuid',
        'numero_factura',
        'serie',
        'fecha_emision',
        'fecha_vencimiento',
        'cliente_id',
        'presupuesto_id',
        'base_imponible',
        'igv',
        'descuento_porcentaje',
        'descuento_descripcion',
        'descuento_monto',
        'total',
        'porcentaje_detraccion',
        'monto_detraccion',
        'estado',
        'monto_pagado',
        'monto_pendiente',
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
            'presupuesto_id' => 'integer',
            'base_imponible' => 'decimal:2',
            'igv' => 'decimal:2',
            'descuento_porcentaje' => 'decimal:2',
            'descuento_monto' => 'decimal:2',
            'total' => 'decimal:2',
            'porcentaje_detraccion' => 'decimal:2',
            'monto_detraccion' => 'decimal:2',
            'monto_pagado' => 'decimal:2',
            'monto_pendiente' => 'decimal:2',
            'usuario_creacion_id' => 'integer',
            'usuario_modificacion_id' => 'integer',
            'user_id' => 'integer',
        ];
    }

    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class);
    }

    public function presupuesto(): BelongsTo
    {
        return $this->belongsTo(Presupuesto::class);
    }

    public function user(): BelongsTo
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

    public function pagoClientes(): HasMany
    {
        return $this->hasMany(PagoCliente::class);
    }
}
