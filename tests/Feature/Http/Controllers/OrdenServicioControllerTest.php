<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\OrdenServicio;
use App\Models\Presupuesto;
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
 * @see \App\Http\Controllers\OrdenServicioController
 */
final class OrdenServicioControllerTest extends TestCase
{
    use AdditionalAssertions, RefreshDatabase, WithFaker;

    #[Test]
    public function index_displays_view(): void
    {
        $ordenServicios = OrdenServicio::factory()->count(3)->create();

        $response = $this->get(route('orden-servicios.index'));

        $response->assertOk();
        $response->assertViewIs('ordenServicio.index');
        $response->assertViewHas('ordenServicios', $ordenServicios);
    }


    #[Test]
    public function create_displays_view(): void
    {
        $response = $this->get(route('orden-servicios.create'));

        $response->assertOk();
        $response->assertViewIs('ordenServicio.create');
    }


    #[Test]
    public function store_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\OrdenServicioController::class,
            'store',
            \App\Http\Requests\OrdenServicioStoreRequest::class
        );
    }

    #[Test]
    public function store_saves_and_redirects(): void
    {
        $uuid = Uuid::factory()->create();
        $numero_orden = fake()->word();
        $fecha_emision = Carbon::parse(fake()->date());
        $presupuesto = Presupuesto::factory()->create();
        $subcontratista = Subcontratista::factory()->create();
        $tipo_contrato = fake()->randomElement(/** enum_attributes **/);
        $estado = fake()->randomElement(/** enum_attributes **/);
        $base_imponible = fake()->randomFloat(/** decimal_attributes **/);
        $igv = fake()->randomFloat(/** decimal_attributes **/);
        $total = fake()->randomFloat(/** decimal_attributes **/);
        $tipo_documento = fake()->randomElement(/** enum_attributes **/);
        $usuario_creacion = UsuarioCreacion::factory()->create();
        $user = User::factory()->create();

        $response = $this->post(route('orden-servicios.store'), [
            'uuid' => $uuid->id,
            'numero_orden' => $numero_orden,
            'fecha_emision' => $fecha_emision->toDateString(),
            'presupuesto_id' => $presupuesto->id,
            'subcontratista_id' => $subcontratista->id,
            'tipo_contrato' => $tipo_contrato,
            'estado' => $estado,
            'base_imponible' => $base_imponible,
            'igv' => $igv,
            'total' => $total,
            'tipo_documento' => $tipo_documento,
            'usuario_creacion_id' => $usuario_creacion->id,
            'user_id' => $user->id,
        ]);

        $ordenServicios = OrdenServicio::query()
            ->where('uuid', $uuid->id)
            ->where('numero_orden', $numero_orden)
            ->where('fecha_emision', $fecha_emision)
            ->where('presupuesto_id', $presupuesto->id)
            ->where('subcontratista_id', $subcontratista->id)
            ->where('tipo_contrato', $tipo_contrato)
            ->where('estado', $estado)
            ->where('base_imponible', $base_imponible)
            ->where('igv', $igv)
            ->where('total', $total)
            ->where('tipo_documento', $tipo_documento)
            ->where('usuario_creacion_id', $usuario_creacion->id)
            ->where('user_id', $user->id)
            ->get();
        $this->assertCount(1, $ordenServicios);
        $ordenServicio = $ordenServicios->first();

        $response->assertRedirect(route('ordenServicios.index'));
        $response->assertSessionHas('ordenServicio.id', $ordenServicio->id);
    }


    #[Test]
    public function show_displays_view(): void
    {
        $ordenServicio = OrdenServicio::factory()->create();

        $response = $this->get(route('orden-servicios.show', $ordenServicio));

        $response->assertOk();
        $response->assertViewIs('ordenServicio.show');
        $response->assertViewHas('ordenServicio', $ordenServicio);
    }


    #[Test]
    public function edit_displays_view(): void
    {
        $ordenServicio = OrdenServicio::factory()->create();

        $response = $this->get(route('orden-servicios.edit', $ordenServicio));

        $response->assertOk();
        $response->assertViewIs('ordenServicio.edit');
        $response->assertViewHas('ordenServicio', $ordenServicio);
    }


    #[Test]
    public function update_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\OrdenServicioController::class,
            'update',
            \App\Http\Requests\OrdenServicioUpdateRequest::class
        );
    }

    #[Test]
    public function update_redirects(): void
    {
        $ordenServicio = OrdenServicio::factory()->create();
        $uuid = Uuid::factory()->create();
        $numero_orden = fake()->word();
        $fecha_emision = Carbon::parse(fake()->date());
        $presupuesto = Presupuesto::factory()->create();
        $subcontratista = Subcontratista::factory()->create();
        $tipo_contrato = fake()->randomElement(/** enum_attributes **/);
        $estado = fake()->randomElement(/** enum_attributes **/);
        $base_imponible = fake()->randomFloat(/** decimal_attributes **/);
        $igv = fake()->randomFloat(/** decimal_attributes **/);
        $total = fake()->randomFloat(/** decimal_attributes **/);
        $tipo_documento = fake()->randomElement(/** enum_attributes **/);
        $usuario_creacion = UsuarioCreacion::factory()->create();
        $user = User::factory()->create();

        $response = $this->put(route('orden-servicios.update', $ordenServicio), [
            'uuid' => $uuid->id,
            'numero_orden' => $numero_orden,
            'fecha_emision' => $fecha_emision->toDateString(),
            'presupuesto_id' => $presupuesto->id,
            'subcontratista_id' => $subcontratista->id,
            'tipo_contrato' => $tipo_contrato,
            'estado' => $estado,
            'base_imponible' => $base_imponible,
            'igv' => $igv,
            'total' => $total,
            'tipo_documento' => $tipo_documento,
            'usuario_creacion_id' => $usuario_creacion->id,
            'user_id' => $user->id,
        ]);

        $ordenServicio->refresh();

        $response->assertRedirect(route('ordenServicios.index'));
        $response->assertSessionHas('ordenServicio.id', $ordenServicio->id);

        $this->assertEquals($uuid->id, $ordenServicio->uuid);
        $this->assertEquals($numero_orden, $ordenServicio->numero_orden);
        $this->assertEquals($fecha_emision, $ordenServicio->fecha_emision);
        $this->assertEquals($presupuesto->id, $ordenServicio->presupuesto_id);
        $this->assertEquals($subcontratista->id, $ordenServicio->subcontratista_id);
        $this->assertEquals($tipo_contrato, $ordenServicio->tipo_contrato);
        $this->assertEquals($estado, $ordenServicio->estado);
        $this->assertEquals($base_imponible, $ordenServicio->base_imponible);
        $this->assertEquals($igv, $ordenServicio->igv);
        $this->assertEquals($total, $ordenServicio->total);
        $this->assertEquals($tipo_documento, $ordenServicio->tipo_documento);
        $this->assertEquals($usuario_creacion->id, $ordenServicio->usuario_creacion_id);
        $this->assertEquals($user->id, $ordenServicio->user_id);
    }


    #[Test]
    public function destroy_deletes_and_redirects(): void
    {
        $ordenServicio = OrdenServicio::factory()->create();

        $response = $this->delete(route('orden-servicios.destroy', $ordenServicio));

        $response->assertRedirect(route('ordenServicios.index'));

        $this->assertSoftDeleted($ordenServicio);
    }
}
