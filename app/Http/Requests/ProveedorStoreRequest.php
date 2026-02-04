<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProveedorStoreRequest extends FormRequest
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
            'uuid' => ['required', 'unique:proveedors,uuid'],
            'tipo_documento' => ['required', 'in:RUC'],
            'numero_documento' => ['required', 'string', 'max:20', 'unique:proveedors,numero_documento'],
            'razon_social' => ['required', 'string', 'max:255'],
            'rubro' => ['nullable', 'string', 'max:150'],
            'contacto' => ['nullable', 'string', 'max:150'],
            'telefono' => ['nullable', 'string', 'max:20'],
            'email' => ['nullable', 'email', 'max:150'],
            'estado' => ['required', 'in:activo,inactivo'],
            'usuario_creacion_id' => ['required', 'integer', 'exists:users,id'],
            'usuario_modificacion_id' => ['nullable', 'integer', 'exists:users,id'],
            'user_id' => ['required', 'integer', 'exists:users,id'],
        ];
    }
}
