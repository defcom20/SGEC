<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FacturaSubcontratistumUpdateRequest extends FormRequest
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
            'uuid' => ['required', 'unique:factura_subcontratistas,uuid'],
            'tipo_documento' => ['required', 'in:factura,recibo_honorarios,boleta'],
            'numero_documento' => ['required', 'string', 'max:50'],
            'serie' => ['nullable', 'string', 'max:20'],
            'fecha_emision' => ['required', 'date'],
            'fecha_vencimiento' => ['required', 'date'],
            'subcontratista_id' => ['required', 'integer', 'exists:subcontratistas,id'],
            'orden_servicio_id' => ['required', 'integer', 'exists:ordenes_servicio,id'],
            'base_imponible' => ['required', 'numeric', 'between:-9999999999.99,9999999999.99'],
            'igv' => ['required', 'numeric', 'between:-9999999999.99,9999999999.99'],
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
