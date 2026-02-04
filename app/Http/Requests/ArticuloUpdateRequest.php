<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ArticuloUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'uuid' => ['required', 'unique:articulos,uuid'],
            'codigo' => ['required', 'string', 'max:50', 'unique:articulos,codigo'],
            'descripcion' => ['required', 'string', 'max:255'],
            'unidad_medida' => ['required', 'string', 'max:20'],
            'proveedor_id' => ['required', 'integer', 'exists:proveedores,id'],
            'precio_compra' => ['required', 'numeric', 'between:-99999999.99,99999999.99'],
            'precio_venta' => ['required', 'numeric', 'between:-99999999.99,99999999.99'],
            'stock' => ['required', 'numeric', 'between:-99999999.99,99999999.99'],
            'fecha_vencimiento' => ['nullable', 'date'],
            'estado' => ['required', 'in:activo,inactivo'],
            'usuario_creacion_id' => ['required', 'integer', 'exists:users,id'],
            'usuario_modificacion_id' => ['nullable', 'integer', 'exists:users,id'],
            'user_id' => ['required', 'integer', 'exists:users,id'],
        ];
    }
}
