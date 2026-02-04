<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ClienteUpdateRequest extends FormRequest
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
            'uuid' => ['required', 'unique:clientes,uuid'],
            'tipo_documento' => ['required', 'in:RUC,DNI'],
            'numero_documento' => ['required', 'string', 'max:20', 'unique:clientes,numero_documento'],
            'razon_social' => ['required', 'string', 'max:255'],
            'direccion' => ['nullable', 'string', 'max:255'],
            'distrito' => ['nullable', 'string', 'max:100'],
            'provincia' => ['nullable', 'string', 'max:100'],
            'departamento' => ['nullable', 'string', 'max:100'],
            'persona_contacto' => ['nullable', 'string', 'max:150'],
            'cargo_contacto' => ['nullable', 'string', 'max:100'],
            'telefono' => ['nullable', 'string', 'max:20'],
            'email' => ['nullable', 'email', 'max:150'],
            'estado' => ['required', 'in:activo,inactivo'],
            'observaciones' => ['nullable', 'string'],
            'usuario_creacion_id' => ['required', 'integer', 'exists:users,id'],
            'usuario_modificacion_id' => ['nullable', 'integer', 'exists:users,id'],
            'user_id' => ['required', 'integer', 'exists:users,id'],
        ];
    }
}
