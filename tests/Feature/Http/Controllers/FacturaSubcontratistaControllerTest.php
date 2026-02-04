<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\FacturaSubcontratista;
use App\Models\FacturaSubcontratistum;
use App\Models\OrdenServicio;
use App\Models\Subcontratista;
use App\Models\User;
use App\Models\UsuarioCreacion;
use App\Models\Uuid;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Carbon;
use JMac\Testing\Traits\AdditionalAssertions;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\FacturaSubcontratistaController
 */
final class FacturaSubcontratistaControllerTest extends TestCase
{
    use AdditionalAssertions, RefreshDatabase, WithFaker;

    #[Test]
    public function index_displays_view(): void
    {
        $facturaSubcontratista = FacturaSubcontratista::factory()->count(3)->create();

        $response = $this->get(route('factura-subcontratista.index'));

        $response->assertOk();
        $response->assertViewIs('facturaSubcontratistum.index');
        $response->assertViewHas('facturaSubcontratista', $facturaSubcontratista);
    }


    #[Test]
    public function create_displays_view(): void
    {
        $response = $this->get(route('factura-subcontratista.create'));

        $response->assertOk();
        $response->assertViewIs('facturaSubcontratistum.create');
    }


    #[Test]
    public function store_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\FacturaSubcontratistaController::class,
            'store',
            \App\Http\Requests\FacturaSubcontratistaStoreRequest::class
        );
    }

    #[Test]
    public function store_saves_and_redirects(): void
    {
        $uuid = Uuid::factory()->create();
        $tipo_documento = fake()->randomElement(/** enum_attributes **/);
        $numero_documento = fake()->word();
        $fecha_emision = Carbon::parse(fake()->date());
        $fecha_vencimiento = Carbon::parse(fake()->date());
        $subcontratista = Subcontratista::factory()->create();
        $orden_servicio = OrdenServicio::factory()->create();
        $base_imponible = fake()->randomFloat(/** decimal_attributes **/);
        $igv = fake()->randomFloat(/** decimal_attributes **/);
        $total = fake()->randomFloat(/** decimal_attributes **/);
        $estado = fake()->randomElement(/** enum_attributes **/);
        $monto_pagado = fake()->randomFloat(/** decimal_attributes **/);
        $monto_pendiente = fake()->randomFloat(/** decimal_attributes **/);
        $usuario_creacion = UsuarioCreacion::factory()->create();
        $user = User::factory()->create();

        $response = $this->post(route('factura-subcontratista.store'), [
            'uuid' => $uuid->id,
            'tipo_documento' => $tipo_documento,
            'numero_documento' => $numero_documento,
            'fecha_emision' => $fecha_emision->toDateString(),
            'fecha_vencimiento' => $fecha_vencimiento->toDateString(),
            'subcontratista_id' => $subcontratista->id,
            'orden_servicio_id' => $orden_servicio->id,
            'base_imponible' => $base_imponible,
            'igv' => $igv,
            'total' => $total,
            'estado' => $estado,
            'monto_pagado' => $monto_pagado,
            'monto_pendiente' => $monto_pendiente,
            'usuario_creacion_id' => $usuario_creacion->id,
            'user_id' => $user->id,
        ]);

        $facturaSubcontratista = FacturaSubcontratistum::query()
            ->where('uuid', $uuid->id)
            ->where('tipo_documento', $tipo_documento)
            ->where('numero_documento', $numero_documento)
            ->where('fecha_emision', $fecha_emision)
            ->where('fecha_vencimiento', $fecha_vencimiento)
            ->where('subcontratista_id', $subcontratista->id)
            ->where('orden_servicio_id', $orden_servicio->id)
            ->where('base_imponible', $base_imponible)
            ->where('igv', $igv)
            ->where('total', $total)
            ->where('estado', $estado)
            ->where('monto_pagado', $monto_pagado)
            ->where('monto_pendiente', $monto_pendiente)
            ->where('usuario_creacion_id', $usuario_creacion->id)
            ->where('user_id', $user->id)
            ->get();
        $this->assertCount(1, $facturaSubcontratista);
        $facturaSubcontratistum = $facturaSubcontratista->first();

        $response->assertRedirect(route('facturaSubcontratista.index'));
        $response->assertSessionHas('facturaSubcontratistum.id', $facturaSubcontratistum->id);
    }


    #[Test]
    public function show_displays_view(): void
    {
        $facturaSubcontratistum = FacturaSubcontratista::factory()->create();

        $response = $this->get(route('factura-subcontratista.show', $facturaSubcontratistum));

        $response->assertOk();
        $response->assertViewIs('facturaSubcontratistum.show');
        $response->assertViewHas('facturaSubcontratistum', $facturaSubcontratistum);
    }


    #[Test]
    public function edit_displays_view(): void
    {
        $facturaSubcontratistum = FacturaSubcontratista::factory()->create();

        $response = $this->get(route('factura-subcontratista.edit', $facturaSubcontratistum));

        $response->assertOk();
        $response->assertViewIs('facturaSubcontratistum.edit');
        $response->assertViewHas('facturaSubcontratistum', $facturaSubcontratistum);
    }


    #[Test]
    public function update_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\FacturaSubcontratistaController::class,
            'update',
            \App\Http\Requests\FacturaSubcontratistaUpdateRequest::class
        );
    }

    #[Test]
    public function update_redirects(): void
    {
        $facturaSubcontratistum = FacturaSubcontratista::factory()->create();
        $uuid = Uuid::factory()->create();
        $tipo_documento = fake()->randomElement(/** enum_attributes **/);
        $numero_documento = fake()->word();
        $fecha_emision = Carbon::parse(fake()->date());
        $fecha_vencimiento = Carbon::parse(fake()->date());
        $subcontratista = Subcontratista::factory()->create();
        $orden_servicio = OrdenServicio::factory()->create();
        $base_imponible = fake()->randomFloat(/** decimal_attributes **/);
        $igv = fake()->randomFloat(/** decimal_attributes **/);
        $total = fake()->randomFloat(/** decimal_attributes **/);
        $estado = fake()->randomElement(/** enum_attributes **/);
        $monto_pagado = fake()->randomFloat(/** decimal_attributes **/);
        $monto_pendiente = fake()->randomFloat(/** decimal_attributes **/);
        $usuario_creacion = UsuarioCreacion::factory()->create();
        $user = User::factory()->create();

        $response = $this->put(route('factura-subcontratista.update', $facturaSubcontratistum), [
            'uuid' => $uuid->id,
            'tipo_documento' => $tipo_documento,
            'numero_documento' => $numero_documento,
            'fecha_emision' => $fecha_emision->toDateString(),
            'fecha_vencimiento' => $fecha_vencimiento->toDateString(),
            'subcontratista_id' => $subcontratista->id,
            'orden_servicio_id' => $orden_servicio->id,
            'base_imponible' => $base_imponible,
            'igv' => $igv,
            'total' => $total,
            'estado' => $estado,
            'monto_pagado' => $monto_pagado,
            'monto_pendiente' => $monto_pendiente,
            'usuario_creacion_id' => $usuario_creacion->id,
            'user_id' => $user->id,
        ]);

        $facturaSubcontratistum->refresh();

        $response->assertRedirect(route('facturaSubcontratista.index'));
        $response->assertSessionHas('facturaSubcontratistum.id', $facturaSubcontratistum->id);

        $this->assertEquals($uuid->id, $facturaSubcontratistum->uuid);
        $this->assertEquals($tipo_documento, $facturaSubcontratistum->tipo_documento);
        $this->assertEquals($numero_documento, $facturaSubcontratistum->numero_documento);
        $this->assertEquals($fecha_emision, $facturaSubcontratistum->fecha_emision);
        $this->assertEquals($fecha_vencimiento, $facturaSubcontratistum->fecha_vencimiento);
        $this->assertEquals($subcontratista->id, $facturaSubcontratistum->subcontratista_id);
        $this->assertEquals($orden_servicio->id, $facturaSubcontratistum->orden_servicio_id);
        $this->assertEquals($base_imponible, $facturaSubcontratistum->base_imponible);
        $this->assertEquals($igv, $facturaSubcontratistum->igv);
        $this->assertEquals($total, $facturaSubcontratistum->total);
        $this->assertEquals($estado, $facturaSubcontratistum->estado);
        $this->assertEquals($monto_pagado, $facturaSubcontratistum->monto_pagado);
        $this->assertEquals($monto_pendiente, $facturaSubcontratistum->monto_pendiente);
        $this->assertEquals($usuario_creacion->id, $facturaSubcontratistum->usuario_creacion_id);
        $this->assertEquals($user->id, $facturaSubcontratistum->user_id);
    }


    #[Test]
    public function destroy_deletes_and_redirects(): void
    {
        $facturaSubcontratistum = FacturaSubcontratista::factory()->create();
        $facturaSubcontratistum = FacturaSubcontratistum::factory()->create();

        $response = $this->delete(route('factura-subcontratista.destroy', $facturaSubcontratistum));

        $response->assertRedirect(route('facturaSubcontratista.index'));

        $this->assertSoftDeleted($facturaSubcontratistum);
    }
}
