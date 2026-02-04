<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OrdenServicioDetalleUpdateRequest extends FormRequest
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
            'uuid' => ['required', 'unique:orden_servicio_detalles,uuid'],
            'orden_servicio_id' => ['required', 'integer', 'exists:ordenes_servicio,id'],
            'descripcion' => ['required', 'string', 'max:255'],
            'cantidad' => ['required', 'numeric', 'between:-99999999.99,99999999.99'],
            'unidad_medida' => ['required', 'string', 'max:20'],
            'precio_unitario' => ['required', 'numeric', 'between:-99999999.99,99999999.99'],
            'subtotal' => ['required', 'numeric', 'between:-9999999999.99,9999999999.99'],
        ];
    }
}
