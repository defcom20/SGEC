<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RolUpdateRequest extends FormRequest
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
            'uuid' => ['required', 'unique:rols,uuid'],
            'nombre' => ['required', 'string', 'max:100', 'unique:rols,nombre'],
            'descripcion' => ['nullable', 'string'],
        ];
    }
}
