<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PresupuestoDetalleStoreRequest extends FormRequest
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
            'uuid' => ['required', 'unique:presupuesto_detalles,uuid'],
            'presupuesto_id' => ['required', 'integer', 'exists:presupuestos,id'],
            'servicio_id' => ['nullable', 'integer', 'exists:servicios,id'],
            'descripcion' => ['required', 'string', 'max:255'],
            'cantidad' => ['required', 'numeric', 'between:-99999999.99,99999999.99'],
            'unidad_medida' => ['required', 'string', 'max:20'],
            'precio_unitario' => ['required', 'numeric', 'between:-99999999.99,99999999.99'],
            'subtotal' => ['required', 'numeric', 'between:-9999999999.99,9999999999.99'],
            'orden' => ['required', 'integer'],
        ];
    }
}
