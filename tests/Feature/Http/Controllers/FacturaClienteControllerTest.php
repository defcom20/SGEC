<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Cliente;
use App\Models\FacturaCliente;
use App\Models\Presupuesto;
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
 * @see \App\Http\Controllers\FacturaClienteController
 */
final class FacturaClienteControllerTest extends TestCase
{
    use AdditionalAssertions, RefreshDatabase, WithFaker;

    #[Test]
    public function index_displays_view(): void
    {
        $facturaClientes = FacturaCliente::factory()->count(3)->create();

        $response = $this->get(route('factura-clientes.index'));

        $response->assertOk();
        $response->assertViewIs('facturaCliente.index');
        $response->assertViewHas('facturaClientes', $facturaClientes);
    }


    #[Test]
    public function create_displays_view(): void
    {
        $response = $this->get(route('factura-clientes.create'));

        $response->assertOk();
        $response->assertViewIs('facturaCliente.create');
    }


    #[Test]
    public function store_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\FacturaClienteController::class,
            'store',
            \App\Http\Requests\FacturaClienteStoreRequest::class
        );
    }

    #[Test]
    public function store_saves_and_redirects(): void
    {
        $uuid = Uuid::factory()->create();
        $numero_factura = fake()->word();
        $serie = fake()->word();
        $fecha_emision = Carbon::parse(fake()->date());
        $fecha_vencimiento = Carbon::parse(fake()->date());
        $cliente = Cliente::factory()->create();
        $presupuesto = Presupuesto::factory()->create();
        $base_imponible = fake()->randomFloat(/** decimal_attributes **/);
        $igv = fake()->randomFloat(/** decimal_attributes **/);
        $total = fake()->randomFloat(/** decimal_attributes **/);
        $estado = fake()->randomElement(/** enum_attributes **/);
        $monto_pagado = fake()->randomFloat(/** decimal_attributes **/);
        $monto_pendiente = fake()->randomFloat(/** decimal_attributes **/);
        $usuario_creacion = UsuarioCreacion::factory()->create();
        $user = User::factory()->create();

        $response = $this->post(route('factura-clientes.store'), [
            'uuid' => $uuid->id,
            'numero_factura' => $numero_factura,
            'serie' => $serie,
            'fecha_emision' => $fecha_emision->toDateString(),
            'fecha_vencimiento' => $fecha_vencimiento->toDateString(),
            'cliente_id' => $cliente->id,
            'presupuesto_id' => $presupuesto->id,
            'base_imponible' => $base_imponible,
            'igv' => $igv,
            'total' => $total,
            'estado' => $estado,
            'monto_pagado' => $monto_pagado,
            'monto_pendiente' => $monto_pendiente,
            'usuario_creacion_id' => $usuario_creacion->id,
            'user_id' => $user->id,
        ]);

        $facturaClientes = FacturaCliente::query()
            ->where('uuid', $uuid->id)
            ->where('numero_factura', $numero_factura)
            ->where('serie', $serie)
            ->where('fecha_emision', $fecha_emision)
            ->where('fecha_vencimiento', $fecha_vencimiento)
            ->where('cliente_id', $cliente->id)
            ->where('presupuesto_id', $presupuesto->id)
            ->where('base_imponible', $base_imponible)
            ->where('igv', $igv)
            ->where('total', $total)
            ->where('estado', $estado)
            ->where('monto_pagado', $monto_pagado)
            ->where('monto_pendiente', $monto_pendiente)
            ->where('usuario_creacion_id', $usuario_creacion->id)
            ->where('user_id', $user->id)
            ->get();
        $this->assertCount(1, $facturaClientes);
        $facturaCliente = $facturaClientes->first();

        $response->assertRedirect(route('facturaClientes.index'));
        $response->assertSessionHas('facturaCliente.id', $facturaCliente->id);
    }


    #[Test]
    public function show_displays_view(): void
    {
        $facturaCliente = FacturaCliente::factory()->create();

        $response = $this->get(route('factura-clientes.show', $facturaCliente));

        $response->assertOk();
        $response->assertViewIs('facturaCliente.show');
        $response->assertViewHas('facturaCliente', $facturaCliente);
    }


    #[Test]
    public function edit_displays_view(): void
    {
        $facturaCliente = FacturaCliente::factory()->create();

        $response = $this->get(route('factura-clientes.edit', $facturaCliente));

        $response->assertOk();
        $response->assertViewIs('facturaCliente.edit');
        $response->assertViewHas('facturaCliente', $facturaCliente);
    }


    #[Test]
    public function update_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\FacturaClienteController::class,
            'update',
            \App\Http\Requests\FacturaClienteUpdateRequest::class
        );
    }

    #[Test]
    public function update_redirects(): void
    {
        $facturaCliente = FacturaCliente::factory()->create();
        $uuid = Uuid::factory()->create();
        $numero_factura = fake()->word();
        $serie = fake()->word();
        $fecha_emision = Carbon::parse(fake()->date());
        $fecha_vencimiento = Carbon::parse(fake()->date());
        $cliente = Cliente::factory()->create();
        $presupuesto = Presupuesto::factory()->create();
        $base_imponible = fake()->randomFloat(/** decimal_attributes **/);
        $igv = fake()->randomFloat(/** decimal_attributes **/);
        $total = fake()->randomFloat(/** decimal_attributes **/);
        $estado = fake()->randomElement(/** enum_attributes **/);
        $monto_pagado = fake()->randomFloat(/** decimal_attributes **/);
        $monto_pendiente = fake()->randomFloat(/** decimal_attributes **/);
        $usuario_creacion = UsuarioCreacion::factory()->create();
        $user = User::factory()->create();

        $response = $this->put(route('factura-clientes.update', $facturaCliente), [
            'uuid' => $uuid->id,
            'numero_factura' => $numero_factura,
            'serie' => $serie,
            'fecha_emision' => $fecha_emision->toDateString(),
            'fecha_vencimiento' => $fecha_vencimiento->toDateString(),
            'cliente_id' => $cliente->id,
            'presupuesto_id' => $presupuesto->id,
            'base_imponible' => $base_imponible,
            'igv' => $igv,
            'total' => $total,
            'estado' => $estado,
            'monto_pagado' => $monto_pagado,
            'monto_pendiente' => $monto_pendiente,
            'usuario_creacion_id' => $usuario_creacion->id,
            'user_id' => $user->id,
        ]);

        $facturaCliente->refresh();

        $response->assertRedirect(route('facturaClientes.index'));
        $response->assertSessionHas('facturaCliente.id', $facturaCliente->id);

        $this->assertEquals($uuid->id, $facturaCliente->uuid);
        $this->assertEquals($numero_factura, $facturaCliente->numero_factura);
        $this->assertEquals($serie, $facturaCliente->serie);
        $this->assertEquals($fecha_emision, $facturaCliente->fecha_emision);
        $this->assertEquals($fecha_vencimiento, $facturaCliente->fecha_vencimiento);
        $this->assertEquals($cliente->id, $facturaCliente->cliente_id);
        $this->assertEquals($presupuesto->id, $facturaCliente->presupuesto_id);
        $this->assertEquals($base_imponible, $facturaCliente->base_imponible);
        $this->assertEquals($igv, $facturaCliente->igv);
        $this->assertEquals($total, $facturaCliente->total);
        $this->assertEquals($estado, $facturaCliente->estado);
        $this->assertEquals($monto_pagado, $facturaCliente->monto_pagado);
        $this->assertEquals($monto_pendiente, $facturaCliente->monto_pendiente);
        $this->assertEquals($usuario_creacion->id, $facturaCliente->usuario_creacion_id);
        $this->assertEquals($user->id, $facturaCliente->user_id);
    }


    #[Test]
    public function destroy_deletes_and_redirects(): void
    {
        $facturaCliente = FacturaCliente::factory()->create();

        $response = $this->delete(route('factura-clientes.destroy', $facturaCliente));

        $response->assertRedirect(route('facturaClientes.index'));

        $this->assertSoftDeleted($facturaCliente);
    }
}
