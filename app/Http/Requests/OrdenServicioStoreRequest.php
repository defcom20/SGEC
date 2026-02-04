<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OrdenServicioStoreRequest extends FormRequest
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
            'uuid' => ['required', 'unique:orden_servicios,uuid'],
            'numero_orden' => ['required', 'string', 'max:50', 'unique:orden_servicios,numero_orden'],
            'fecha_emision' => ['required', 'date'],
            'presupuesto_id' => ['required', 'integer', 'exists:presupuestos,id'],
            'subcontratista_id' => ['required', 'integer', 'exists:subcontratistas,id'],
            'tipo_contrato' => ['required', 'in:servicio_completo,solo_mano_obra'],
            'estado' => ['required', 'in:pendiente,aprobado,en_ejecucion,finalizado,pagado'],
            'fecha_aprobacion' => ['nullable', 'date'],
            'fecha_inicio_ejecucion' => ['nullable', 'date'],
            'fecha_finalizacion' => ['nullable', 'date'],
            'base_imponible' => ['required', 'numeric', 'between:-9999999999.99,9999999999.99'],
            'igv' => ['required', 'numeric', 'between:-9999999999.99,9999999999.99'],
            'total' => ['required', 'numeric', 'between:-9999999999.99,9999999999.99'],
            'porcentaje_detraccion' => ['nullable', 'numeric', 'between:-999.99,999.99'],
            'monto_detraccion' => ['nullable', 'numeric', 'between:-9999999999.99,9999999999.99'],
            'tipo_documento' => ['required', 'in:factura,recibo_honorarios,boleta'],
            'observaciones' => ['nullable', 'string'],
            'usuario_creacion_id' => ['required', 'integer', 'exists:users,id'],
            'usuario_modificacion_id' => ['nullable', 'integer', 'exists:users,id'],
            'user_id' => ['required', 'integer', 'exists:users,id'],
        ];
    }
}
