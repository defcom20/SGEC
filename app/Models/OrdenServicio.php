<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrdenServicio extends Model
{
    use HasFactory, SoftDeletes, HasUuid;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'uuid',
        'numero_orden',
        'fecha_emision',
        'presupuesto_id',
        'subcontratista_id',
        'tipo_contrato',
        'estado',
        'fecha_aprobacion',
        'fecha_inicio_ejecucion',
        'fecha_finalizacion',
        'base_imponible',
        'igv',
        'total',
        'porcentaje_detraccion',
        'monto_detraccion',
        'tipo_documento',
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
            'presupuesto_id' => 'integer',
            'subcontratista_id' => 'integer',
            'fecha_aprobacion' => 'date',
            'fecha_inicio_ejecucion' => 'date',
            'fecha_finalizacion' => 'date',
            'base_imponible' => 'decimal:2',
            'igv' => 'decimal:2',
            'total' => 'decimal:2',
            'porcentaje_detraccion' => 'decimal:2',
            'monto_detraccion' => 'decimal:2',
            'usuario_creacion_id' => 'integer',
            'usuario_modificacion_id' => 'integer',
            'user_id' => 'integer',
        ];
    }

    public function presupuesto(): BelongsTo
    {
        return $this->belongsTo(Presupuesto::class);
    }

    public function subcontratista(): BelongsTo
    {
        return $this->belongsTo(Subcontratista::class);
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

    public function ordenServicioDetalles(): HasMany
    {
        return $this->hasMany(OrdenServicioDetalle::class);
    }

    public function facturaSubcontratista(): HasOne
    {
        return $this->hasOne(FacturaSubcontratista::class);
    }
}
