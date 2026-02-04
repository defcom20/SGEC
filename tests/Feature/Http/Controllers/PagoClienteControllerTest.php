<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\FacturaCliente;
use App\Models\PagoCliente;
use App\Models\User;
use App\Models\UsuarioRegistro;
use App\Models\Uuid;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Carbon;
use JMac\Testing\Traits\AdditionalAssertions;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\PagoClienteController
 */
final class PagoClienteControllerTest extends TestCase
{
    use AdditionalAssertions, RefreshDatabase, WithFaker;

    #[Test]
    public function index_displays_view(): void
    {
        $pagoClientes = PagoCliente::factory()->count(3)->create();

        $response = $this->get(route('pago-clientes.index'));

        $response->assertOk();
        $response->assertViewIs('pagoCliente.index');
        $response->assertViewHas('pagoClientes', $pagoClientes);
    }


    #[Test]
    public function create_displays_view(): void
    {
        $response = $this->get(route('pago-clientes.create'));

        $response->assertOk();
        $response->assertViewIs('pagoCliente.create');
    }


    #[Test]
    public function store_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\PagoClienteController::class,
            'store',
            \App\Http\Requests\PagoClienteStoreRequest::class
        );
    }

    #[Test]
    public function store_saves_and_redirects(): void
    {
        $uuid = Uuid::factory()->create();
        $factura_cliente = FacturaCliente::factory()->create();
        $numero_pago = fake()->word();
        $fecha_pago = Carbon::parse(fake()->date());
        $monto = fake()->randomFloat(/** decimal_attributes **/);
        $metodo_pago = fake()->randomElement(/** enum_attributes **/);
        $usuario_registro = UsuarioRegistro::factory()->create();
        $user = User::factory()->create();

        $response = $this->post(route('pago-clientes.store'), [
            'uuid' => $uuid->id,
            'factura_cliente_id' => $factura_cliente->id,
            'numero_pago' => $numero_pago,
            'fecha_pago' => $fecha_pago->toDateString(),
            'monto' => $monto,
            'metodo_pago' => $metodo_pago,
            'usuario_registro_id' => $usuario_registro->id,
            'user_id' => $user->id,
        ]);

        $pagoClientes = PagoCliente::query()
            ->where('uuid', $uuid->id)
            ->where('factura_cliente_id', $factura_cliente->id)
            ->where('numero_pago', $numero_pago)
            ->where('fecha_pago', $fecha_pago)
            ->where('monto', $monto)
            ->where('metodo_pago', $metodo_pago)
            ->where('usuario_registro_id', $usuario_registro->id)
            ->where('user_id', $user->id)
            ->get();
        $this->assertCount(1, $pagoClientes);
        $pagoCliente = $pagoClientes->first();

        $response->assertRedirect(route('pagoClientes.index'));
        $response->assertSessionHas('pagoCliente.id', $pagoCliente->id);
    }


    #[Test]
    public function show_displays_view(): void
    {
        $pagoCliente = PagoCliente::factory()->create();

        $response = $this->get(route('pago-clientes.show', $pagoCliente));

        $response->assertOk();
        $response->assertViewIs('pagoCliente.show');
        $response->assertViewHas('pagoCliente', $pagoCliente);
    }


    #[Test]
    public function edit_displays_view(): void
    {
        $pagoCliente = PagoCliente::factory()->create();

        $response = $this->get(route('pago-clientes.edit', $pagoCliente));

        $response->assertOk();
        $response->assertViewIs('pagoCliente.edit');
        $response->assertViewHas('pagoCliente', $pagoCliente);
    }


    #[Test]
    public function update_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\PagoClienteController::class,
            'update',
            \App\Http\Requests\PagoClienteUpdateRequest::class
        );
    }

    #[Test]
    public function update_redirects(): void
    {
        $pagoCliente = PagoCliente::factory()->create();
        $uuid = Uuid::factory()->create();
        $factura_cliente = FacturaCliente::factory()->create();
        $numero_pago = fake()->word();
        $fecha_pago = Carbon::parse(fake()->date());
        $monto = fake()->randomFloat(/** decimal_attributes **/);
        $metodo_pago = fake()->randomElement(/** enum_attributes **/);
        $usuario_registro = UsuarioRegistro::factory()->create();
        $user = User::factory()->create();

        $response = $this->put(route('pago-clientes.update', $pagoCliente), [
            'uuid' => $uuid->id,
            'factura_cliente_id' => $factura_cliente->id,
            'numero_pago' => $numero_pago,
            'fecha_pago' => $fecha_pago->toDateString(),
            'monto' => $monto,
            'metodo_pago' => $metodo_pago,
            'usuario_registro_id' => $usuario_registro->id,
            'user_id' => $user->id,
        ]);

        $pagoCliente->refresh();

        $response->assertRedirect(route('pagoClientes.index'));
        $response->assertSessionHas('pagoCliente.id', $pagoCliente->id);

        $this->assertEquals($uuid->id, $pagoCliente->uuid);
        $this->assertEquals($factura_cliente->id, $pagoCliente->factura_cliente_id);
        $this->assertEquals($numero_pago, $pagoCliente->numero_pago);
        $this->assertEquals($fecha_pago, $pagoCliente->fecha_pago);
        $this->assertEquals($monto, $pagoCliente->monto);
        $this->assertEquals($metodo_pago, $pagoCliente->metodo_pago);
        $this->assertEquals($usuario_registro->id, $pagoCliente->usuario_registro_id);
        $this->assertEquals($user->id, $pagoCliente->user_id);
    }


    #[Test]
    public function destroy_deletes_and_redirects(): void
    {
        $pagoCliente = PagoCliente::factory()->create();

        $response = $this->delete(route('pago-clientes.destroy', $pagoCliente));

        $response->assertRedirect(route('pagoClientes.index'));

        $this->assertModelMissing($pagoCliente);
    }
}
