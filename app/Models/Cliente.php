<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Cliente extends Model
{
    use HasFactory, SoftDeletes, HasUuid;

    protected $fillable = [
        'uuid',
        'tipo_persona',
        'tipo_documento',
        'numero_documento',
        'razon_social', // Principal
        'nombre',       // Mantener por compatibilidad si es requerido en BD, lo llenaremos igual
        'email',
        'telefono',
        'direccion',
        'distrito',
        'provincia',
        'departamento',
        'persona_contacto',
        'cargo_contacto',
        'observaciones',
        'estado',
        'usuario_creacion_id',
        'usuario_modificacion_id',
        'user_id',
    ];

    protected $casts = [
        'estado' => 'boolean', // El output de model:show dice enum o boolean, pero tratemos como boolean o string según controller
    ];
}
