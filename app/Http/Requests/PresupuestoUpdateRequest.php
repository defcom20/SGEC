<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PresupuestoUpdateRequest extends FormRequest
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
            'uuid' => ['required', 'unique:presupuestos,uuid'],
            'numero_presupuesto' => ['required', 'string', 'max:50', 'unique:presupuestos,numero_presupuesto'],
            'fecha_emision' => ['required', 'date'],
            'fecha_vencimiento' => ['required', 'date'],
            'cliente_id' => ['required', 'integer', 'exists:clientes,id'],
            'persona_contacto' => ['nullable', 'string', 'max:150'],
            'supervisor_id' => ['required', 'integer', 'exists:users,id'],
            'estado' => ['required', 'in:en_revision,aprobado,rechazado,vencido,en_ejecucion,finalizado'],
            'fecha_aceptacion' => ['nullable', 'date'],
            'fecha_inicio' => ['nullable', 'date'],
            'fecha_finalizacion_estimada' => ['nullable', 'date'],
            'periodo_ejecucion_dias' => ['nullable', 'integer'],
            'base_imponible' => ['required', 'numeric', 'between:-9999999999.99,9999999999.99'],
            'igv' => ['required', 'numeric', 'between:-9999999999.99,9999999999.99'],
            'descuento_porcentaje' => ['nullable', 'numeric', 'between:-999.99,999.99'],
            'descuento_monto' => ['nullable', 'numeric', 'between:-9999999999.99,9999999999.99'],
            'total' => ['required', 'numeric', 'between:-9999999999.99,9999999999.99'],
            'observaciones' => ['nullable', 'string'],
            'usuario_creacion_id' => ['required', 'integer', 'exists:users,id'],
            'usuario_modificacion_id' => ['nullable', 'integer', 'exists:users,id'],
            'user_id' => ['required', 'integer', 'exists:users,id'],
        ];
    }
}
