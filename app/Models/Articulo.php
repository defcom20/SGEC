<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Articulo extends Model
{
    use HasFactory, SoftDeletes, HasUuid;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'uuid',
        'codigo',
        'descripcion',
        'unidad_medida',
        'proveedor_id',
        'precio_compra',
        'precio_venta',
        'stock',
        'fecha_vencimiento',
        'estado',
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
            'proveedor_id' => 'integer',
            'precio_compra' => 'decimal:2',
            'precio_venta' => 'decimal:2',
            'stock' => 'decimal:2',
            'fecha_vencimiento' => 'date',
            'usuario_creacion_id' => 'integer',
            'usuario_modificacion_id' => 'integer',
            'user_id' => 'integer',
        ];
    }

    public function proveedor(): BelongsTo
    {
        return $this->belongsTo(Proveedor::class);
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
}
