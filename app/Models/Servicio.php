<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Servicio extends Model
{
    use HasFactory, SoftDeletes, HasUuid;

    protected $fillable = [
        'uuid',
        'codigo',
        'descripcion',
        'unidad_medida',
        'precio_referencial',
        'estado',
        'usuario_creacion_id',
        'usuario_modificacion_id',
        'user_id',
    ];

    protected $casts = [
        'precio_referencial' => 'decimal:2',
    ];

    // Relaciones
    public function usuarioCreacion()
    {
        return $this->belongsTo(User::class, 'usuario_creacion_id');
    }

    public function usuarioModificacion()
    {
        return $this->belongsTo(User::class, 'usuario_modificacion_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
