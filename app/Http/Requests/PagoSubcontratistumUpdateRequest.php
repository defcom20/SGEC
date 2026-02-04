<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PagoSubcontratistumUpdateRequest extends FormRequest
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
            'uuid' => ['required', 'unique:pago_subcontratistas,uuid'],
            'factura_subcontratista_id' => ['required', 'integer', 'exists:facturas_subcontratistas,id'],
            'numero_pago' => ['required', 'string', 'max:50'],
            'fecha_pago' => ['required', 'date'],
            'monto' => ['required', 'numeric', 'between:-9999999999.99,9999999999.99'],
            'metodo_pago' => ['required', 'in:efectivo,transferencia,cheque,deposito'],
            'banco' => ['nullable', 'string', 'max:100'],
            'numero_operacion' => ['nullable', 'string', 'max:100'],
            'cuenta_detraccion_usada' => ['required'],
            'comprobante' => ['nullable', 'string', 'max:255'],
            'observaciones' => ['nullable', 'string'],
            'usuario_registro_id' => ['required', 'integer', 'exists:users,id'],
            'user_id' => ['required', 'integer', 'exists:users,id'],
        ];
    }
}
