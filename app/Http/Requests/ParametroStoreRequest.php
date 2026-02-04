<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ParametroStoreRequest extends FormRequest
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
            'uuid' => ['required', 'unique:parametros,uuid'],
            'clave' => ['required', 'string', 'max:100', 'unique:parametros,clave'],
            'valor' => ['required', 'string'],
            'descripcion' => ['nullable', 'string', 'max:255'],
            'tipo_dato' => ['required', 'in:decimal,string,integer,boolean'],
        ];
    }
}
