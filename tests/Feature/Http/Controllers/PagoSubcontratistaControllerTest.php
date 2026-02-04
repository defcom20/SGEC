<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\FacturaSubcontratista;
use App\Models\PagoSubcontratista;
use App\Models\PagoSubcontratistum;
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
 * @see \App\Http\Controllers\PagoSubcontratistaController
 */
final class PagoSubcontratistaControllerTest extends TestCase
{
    use AdditionalAssertions, RefreshDatabase, WithFaker;

    #[Test]
    public function index_displays_view(): void
    {
        $pagoSubcontratista = PagoSubcontratista::factory()->count(3)->create();

        $response = $this->get(route('pago-subcontratista.index'));

        $response->assertOk();
        $response->assertViewIs('pagoSubcontratistum.index');
        $response->assertViewHas('pagoSubcontratista', $pagoSubcontratista);
    }


    #[Test]
    public function create_displays_view(): void
    {
        $response = $this->get(route('pago-subcontratista.create'));

        $response->assertOk();
        $response->assertViewIs('pagoSubcontratistum.create');
    }


    #[Test]
    public function store_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\PagoSubcontratistaController::class,
            'store',
            \App\Http\Requests\PagoSubcontratistaStoreRequest::class
        );
    }

    #[Test]
    public function store_saves_and_redirects(): void
    {
        $uuid = Uuid::factory()->create();
        $factura_subcontratista = FacturaSubcontratista::factory()->create();
        $numero_pago = fake()->word();
        $fecha_pago = Carbon::parse(fake()->date());
        $monto = fake()->randomFloat(/** decimal_attributes **/);
        $metodo_pago = fake()->randomElement(/** enum_attributes **/);
        $cuenta_detraccion_usada = fake()->boolean();
        $usuario_registro = UsuarioRegistro::factory()->create();
        $user = User::factory()->create();

        $response = $this->post(route('pago-subcontratista.store'), [
            'uuid' => $uuid->id,
            'factura_subcontratista_id' => $factura_subcontratista->id,
            'numero_pago' => $numero_pago,
            'fecha_pago' => $fecha_pago->toDateString(),
            'monto' => $monto,
            'metodo_pago' => $metodo_pago,
            'cuenta_detraccion_usada' => $cuenta_detraccion_usada,
            'usuario_registro_id' => $usuario_registro->id,
            'user_id' => $user->id,
        ]);

        $pagoSubcontratista = PagoSubcontratistum::query()
            ->where('uuid', $uuid->id)
            ->where('factura_subcontratista_id', $factura_subcontratista->id)
            ->where('numero_pago', $numero_pago)
            ->where('fecha_pago', $fecha_pago)
            ->where('monto', $monto)
            ->where('metodo_pago', $metodo_pago)
            ->where('cuenta_detraccion_usada', $cuenta_detraccion_usada)
            ->where('usuario_registro_id', $usuario_registro->id)
            ->where('user_id', $user->id)
            ->get();
        $this->assertCount(1, $pagoSubcontratista);
        $pagoSubcontratistum = $pagoSubcontratista->first();

        $response->assertRedirect(route('pagoSubcontratista.index'));
        $response->assertSessionHas('pagoSubcontratistum.id', $pagoSubcontratistum->id);
    }


    #[Test]
    public function show_displays_view(): void
    {
        $pagoSubcontratistum = PagoSubcontratista::factory()->create();

        $response = $this->get(route('pago-subcontratista.show', $pagoSubcontratistum));

        $response->assertOk();
        $response->assertViewIs('pagoSubcontratistum.show');
        $response->assertViewHas('pagoSubcontratistum', $pagoSubcontratistum);
    }


    #[Test]
    public function edit_displays_view(): void
    {
        $pagoSubcontratistum = PagoSubcontratista::factory()->create();

        $response = $this->get(route('pago-subcontratista.edit', $pagoSubcontratistum));

        $response->assertOk();
        $response->assertViewIs('pagoSubcontratistum.edit');
        $response->assertViewHas('pagoSubcontratistum', $pagoSubcontratistum);
    }


    #[Test]
    public function update_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\PagoSubcontratistaController::class,
            'update',
            \App\Http\Requests\PagoSubcontratistaUpdateRequest::class
        );
    }

    #[Test]
    public function update_redirects(): void
    {
        $pagoSubcontratistum = PagoSubcontratista::factory()->create();
        $uuid = Uuid::factory()->create();
        $factura_subcontratista = FacturaSubcontratista::factory()->create();
        $numero_pago = fake()->word();
        $fecha_pago = Carbon::parse(fake()->date());
        $monto = fake()->randomFloat(/** decimal_attributes **/);
        $metodo_pago = fake()->randomElement(/** enum_attributes **/);
        $cuenta_detraccion_usada = fake()->boolean();
        $usuario_registro = UsuarioRegistro::factory()->create();
        $user = User::factory()->create();

        $response = $this->put(route('pago-subcontratista.update', $pagoSubcontratistum), [
            'uuid' => $uuid->id,
            'factura_subcontratista_id' => $factura_subcontratista->id,
            'numero_pago' => $numero_pago,
            'fecha_pago' => $fecha_pago->toDateString(),
            'monto' => $monto,
            'metodo_pago' => $metodo_pago,
            'cuenta_detraccion_usada' => $cuenta_detraccion_usada,
            'usuario_registro_id' => $usuario_registro->id,
            'user_id' => $user->id,
        ]);

        $pagoSubcontratistum->refresh();

        $response->assertRedirect(route('pagoSubcontratista.index'));
        $response->assertSessionHas('pagoSubcontratistum.id', $pagoSubcontratistum->id);

        $this->assertEquals($uuid->id, $pagoSubcontratistum->uuid);
        $this->assertEquals($factura_subcontratista->id, $pagoSubcontratistum->factura_subcontratista_id);
        $this->assertEquals($numero_pago, $pagoSubcontratistum->numero_pago);
        $this->assertEquals($fecha_pago, $pagoSubcontratistum->fecha_pago);
        $this->assertEquals($monto, $pagoSubcontratistum->monto);
        $this->assertEquals($metodo_pago, $pagoSubcontratistum->metodo_pago);
        $this->assertEquals($cuenta_detraccion_usada, $pagoSubcontratistum->cuenta_detraccion_usada);
        $this->assertEquals($usuario_registro->id, $pagoSubcontratistum->usuario_registro_id);
        $this->assertEquals($user->id, $pagoSubcontratistum->user_id);
    }


    #[Test]
    public function destroy_deletes_and_redirects(): void
    {
        $pagoSubcontratistum = PagoSubcontratista::factory()->create();
        $pagoSubcontratistum = PagoSubcontratistum::factory()->create();

        $response = $this->delete(route('pago-subcontratista.destroy', $pagoSubcontratistum));

        $response->assertRedirect(route('pagoSubcontratista.index'));

        $this->assertModelMissing($pagoSubcontratistum);
    }
}
