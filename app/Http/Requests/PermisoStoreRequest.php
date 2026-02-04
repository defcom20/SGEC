<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PermisoStoreRequest extends FormRequest
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
            'uuid' => ['required', 'unique:permisos,uuid'],
            'modulo' => ['required', 'string', 'max:100'],
            'accion' => ['required', 'string', 'max:50'],
            'descripcion' => ['nullable', 'string', 'max:255'],
        ];
    }
}
