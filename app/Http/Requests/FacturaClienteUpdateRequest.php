<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FacturaClienteUpdateRequest extends FormRequest
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
            'uuid' => ['required', 'unique:factura_clientes,uuid'],
            'numero_factura' => ['required', 'string', 'max:50'],
            'serie' => ['required', 'string', 'max:20'],
            'fecha_emision' => ['required', 'date'],
            'fecha_vencimiento' => ['required', 'date'],
            'cliente_id' => ['required', 'integer', 'exists:clientes,id'],
            'presupuesto_id' => ['required', 'integer', 'exists:presupuestos,id'],
            'base_imponible' => ['required', 'numeric', 'between:-9999999999.99,9999999999.99'],
            'igv' => ['required', 'numeric', 'between:-9999999999.99,9999999999.99'],
            'descuento_porcentaje' => ['nullable', 'numeric', 'between:-999.99,999.99'],
            'descuento_descripcion' => ['nullable', 'string', 'max:255'],
            'descuento_monto' => ['nullable', 'numeric', 'between:-9999999999.99,9999999999.99'],
            'total' => ['required', 'numeric', 'between:-9999999999.99,9999999999.99'],
            'porcentaje_detraccion' => ['nullable', 'numeric', 'between:-999.99,999.99'],
            'monto_detraccion' => ['nullable', 'numeric', 'between:-9999999999.99,9999999999.99'],
            'estado' => ['required', 'in:pendiente,pago_parcial,pagado_completo'],
            'monto_pagado' => ['required', 'numeric', 'between:-9999999999.99,9999999999.99'],
            'monto_pendiente' => ['required', 'numeric', 'between:-9999999999.99,9999999999.99'],
            'observaciones' => ['nullable', 'string'],
            'usuario_creacion_id' => ['required', 'integer', 'exists:users,id'],
            'usuario_modificacion_id' => ['nullable', 'integer', 'exists:users,id'],
            'user_id' => ['required', 'integer', 'exists:users,id'],
        ];
    }
}
