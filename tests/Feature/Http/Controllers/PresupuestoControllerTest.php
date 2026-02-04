<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Cliente;
use App\Models\Presupuesto;
use App\Models\Supervisor;
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
 * @see \App\Http\Controllers\PresupuestoController
 */
final class PresupuestoControllerTest extends TestCase
{
    use AdditionalAssertions, RefreshDatabase, WithFaker;

    #[Test]
    public function index_displays_view(): void
    {
        $presupuestos = Presupuesto::factory()->count(3)->create();

        $response = $this->get(route('presupuestos.index'));

        $response->assertOk();
        $response->assertViewIs('presupuesto.index');
        $response->assertViewHas('presupuestos', $presupuestos);
    }


    #[Test]
    public function create_displays_view(): void
    {
        $response = $this->get(route('presupuestos.create'));

        $response->assertOk();
        $response->assertViewIs('presupuesto.create');
    }


    #[Test]
    public function store_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\PresupuestoController::class,
            'store',
            \App\Http\Requests\PresupuestoStoreRequest::class
        );
    }

    #[Test]
    public function store_saves_and_redirects(): void
    {
        $uuid = Uuid::factory()->create();
        $numero_presupuesto = fake()->word();
        $fecha_emision = Carbon::parse(fake()->date());
        $fecha_vencimiento = Carbon::parse(fake()->date());
        $cliente = Cliente::factory()->create();
        $supervisor = Supervisor::factory()->create();
        $estado = fake()->randomElement(/** enum_attributes **/);
        $base_imponible = fake()->randomFloat(/** decimal_attributes **/);
        $igv = fake()->randomFloat(/** decimal_attributes **/);
        $total = fake()->randomFloat(/** decimal_attributes **/);
        $usuario_creacion = UsuarioCreacion::factory()->create();
        $user = User::factory()->create();

        $response = $this->post(route('presupuestos.store'), [
            'uuid' => $uuid->id,
            'numero_presupuesto' => $numero_presupuesto,
            'fecha_emision' => $fecha_emision->toDateString(),
            'fecha_vencimiento' => $fecha_vencimiento->toDateString(),
            'cliente_id' => $cliente->id,
            'supervisor_id' => $supervisor->id,
            'estado' => $estado,
            'base_imponible' => $base_imponible,
            'igv' => $igv,
            'total' => $total,
            'usuario_creacion_id' => $usuario_creacion->id,
            'user_id' => $user->id,
        ]);

        $presupuestos = Presupuesto::query()
            ->where('uuid', $uuid->id)
            ->where('numero_presupuesto', $numero_presupuesto)
            ->where('fecha_emision', $fecha_emision)
            ->where('fecha_vencimiento', $fecha_vencimiento)
            ->where('cliente_id', $cliente->id)
            ->where('supervisor_id', $supervisor->id)
            ->where('estado', $estado)
            ->where('base_imponible', $base_imponible)
            ->where('igv', $igv)
            ->where('total', $total)
            ->where('usuario_creacion_id', $usuario_creacion->id)
            ->where('user_id', $user->id)
            ->get();
        $this->assertCount(1, $presupuestos);
        $presupuesto = $presupuestos->first();

        $response->assertRedirect(route('presupuestos.index'));
        $response->assertSessionHas('presupuesto.id', $presupuesto->id);
    }


    #[Test]
    public function show_displays_view(): void
    {
        $presupuesto = Presupuesto::factory()->create();

        $response = $this->get(route('presupuestos.show', $presupuesto));

        $response->assertOk();
        $response->assertViewIs('presupuesto.show');
        $response->assertViewHas('presupuesto', $presupuesto);
    }


    #[Test]
    public function edit_displays_view(): void
    {
        $presupuesto = Presupuesto::factory()->create();

        $response = $this->get(route('presupuestos.edit', $presupuesto));

        $response->assertOk();
        $response->assertViewIs('presupuesto.edit');
        $response->assertViewHas('presupuesto', $presupuesto);
    }


    #[Test]
    public function update_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\PresupuestoController::class,
            'update',
            \App\Http\Requests\PresupuestoUpdateRequest::class
        );
    }

    #[Test]
    public function update_redirects(): void
    {
        $presupuesto = Presupuesto::factory()->create();
        $uuid = Uuid::factory()->create();
        $numero_presupuesto = fake()->word();
        $fecha_emision = Carbon::parse(fake()->date());
        $fecha_vencimiento = Carbon::parse(fake()->date());
        $cliente = Cliente::factory()->create();
        $supervisor = Supervisor::factory()->create();
        $estado = fake()->randomElement(/** enum_attributes **/);
        $base_imponible = fake()->randomFloat(/** decimal_attributes **/);
        $igv = fake()->randomFloat(/** decimal_attributes **/);
        $total = fake()->randomFloat(/** decimal_attributes **/);
        $usuario_creacion = UsuarioCreacion::factory()->create();
        $user = User::factory()->create();

        $response = $this->put(route('presupuestos.update', $presupuesto), [
            'uuid' => $uuid->id,
            'numero_presupuesto' => $numero_presupuesto,
            'fecha_emision' => $fecha_emision->toDateString(),
            'fecha_vencimiento' => $fecha_vencimiento->toDateString(),
            'cliente_id' => $cliente->id,
            'supervisor_id' => $supervisor->id,
            'estado' => $estado,
            'base_imponible' => $base_imponible,
            'igv' => $igv,
            'total' => $total,
            'usuario_creacion_id' => $usuario_creacion->id,
            'user_id' => $user->id,
        ]);

        $presupuesto->refresh();

        $response->assertRedirect(route('presupuestos.index'));
        $response->assertSessionHas('presupuesto.id', $presupuesto->id);

        $this->assertEquals($uuid->id, $presupuesto->uuid);
        $this->assertEquals($numero_presupuesto, $presupuesto->numero_presupuesto);
        $this->assertEquals($fecha_emision, $presupuesto->fecha_emision);
        $this->assertEquals($fecha_vencimiento, $presupuesto->fecha_vencimiento);
        $this->assertEquals($cliente->id, $presupuesto->cliente_id);
        $this->assertEquals($supervisor->id, $presupuesto->supervisor_id);
        $this->assertEquals($estado, $presupuesto->estado);
        $this->assertEquals($base_imponible, $presupuesto->base_imponible);
        $this->assertEquals($igv, $presupuesto->igv);
        $this->assertEquals($total, $presupuesto->total);
        $this->assertEquals($usuario_creacion->id, $presupuesto->usuario_creacion_id);
        $this->assertEquals($user->id, $presupuesto->user_id);
    }


    #[Test]
    public function destroy_deletes_and_redirects(): void
    {
        $presupuesto = Presupuesto::factory()->create();

        $response = $this->delete(route('presupuestos.destroy', $presupuesto));

        $response->assertRedirect(route('presupuestos.index'));

        $this->assertSoftDeleted($presupuesto);
    }
}
