<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SubcontratistumStoreRequest extends FormRequest
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
            'uuid' => ['required', 'unique:subcontratistas,uuid'],
            'tipo' => ['required', 'in:empresa,persona_natural'],
            'tipo_documento' => ['required', 'in:RUC,DNI'],
            'numero_documento' => ['required', 'string', 'max:20', 'unique:subcontratistas,numero_documento'],
            'razon_social_nombre' => ['required', 'string', 'max:255'],
            'direccion' => ['nullable', 'string', 'max:255'],
            'telefono' => ['nullable', 'string', 'max:20'],
            'email' => ['nullable', 'email', 'max:150'],
            'banco' => ['nullable', 'string', 'max:100'],
            'numero_cuenta' => ['nullable', 'string', 'max:50'],
            'cci' => ['nullable', 'string', 'max:50'],
            'numero_cuenta_detraccion' => ['nullable', 'string', 'max:50'],
            'estado' => ['required', 'in:activo,inactivo'],
            'usuario_creacion_id' => ['required', 'integer', 'exists:users,id'],
            'usuario_modificacion_id' => ['nullable', 'integer', 'exists:users,id'],
            'user_id' => ['required', 'integer', 'exists:users,id'],
        ];
    }
}
