<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Modulo extends Model
{
    use HasFactory, HasUuid, SoftDeletes, HasUuid;

    protected $fillable = [
        'uuid',
        'nombre',
        'slug',
        'icono',
        'ruta',
        'descripcion',
        'orden',
        'activo',
        'visible_menu',
        'categoria',
        'acciones',
        'usuario_creacion_id',
        'usuario_modificacion_id',
        'parent_id',
        'nivel',
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
            'activo' => 'boolean',
            'visible_menu' => 'boolean',
            'acciones' => 'array',
            'orden' => 'integer',
            'parent_id' => 'integer',
            'nivel' => 'integer',
        ];
    }

    /**
     * Scopes
     */
    public function scopeActivos($query)
    {
        return $query->where('activo', true);
    }

    public function scopeVisiblesEnMenu($query)
    {
        return $query->where('visible_menu', true);
    }

    public function scopeOrdenados($query)
    {
        return $query->orderBy('orden');
    }

    public function scopePadres($query)
    {
        return $query->where('nivel', 1)->whereNull('parent_id');
    }

    public function scopeHijos($query)
    {
        return $query->where('nivel', 2)->whereNotNull('parent_id');
    }

    /**
     * Scope por categoría
     */
    public function scopeCategoria($query, $categoria)
    {
        return $query->where('categoria', $categoria);
    }

    /**
     * Relaciones
     */
    public function parent()
    {
        return $this->belongsTo(Modulo::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Modulo::class, 'parent_id')->orderBy('orden');
    }

    /**
     * Métodos auxiliares
     */
    public function esModuloPadre(): bool
    {
        return $this->nivel === 1 && $this->parent_id === null;
    }

    public function esSubmodulo(): bool
    {
        return $this->nivel === 2 && $this->parent_id !== null;
    }

    public function tieneHijos(): bool
    {
        return $this->children()->count() > 0;
    }
}
