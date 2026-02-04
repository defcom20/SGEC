<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Presupuesto;
use App\Models\PresupuestoDetalle;
use App\Models\Uuid;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use JMac\Testing\Traits\AdditionalAssertions;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\PresupuestoDetalleController
 */
final class PresupuestoDetalleControllerTest extends TestCase
{
    use AdditionalAssertions, RefreshDatabase, WithFaker;

    #[Test]
    public function index_displays_view(): void
    {
        $presupuestoDetalles = PresupuestoDetalle::factory()->count(3)->create();

        $response = $this->get(route('presupuesto-detalles.index'));

        $response->assertOk();
        $response->assertViewIs('presupuestoDetalle.index');
        $response->assertViewHas('presupuestoDetalles', $presupuestoDetalles);
    }


    #[Test]
    public function create_displays_view(): void
    {
        $response = $this->get(route('presupuesto-detalles.create'));

        $response->assertOk();
        $response->assertViewIs('presupuestoDetalle.create');
    }


    #[Test]
    public function store_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\PresupuestoDetalleController::class,
            'store',
            \App\Http\Requests\PresupuestoDetalleStoreRequest::class
        );
    }

    #[Test]
    public function store_saves_and_redirects(): void
    {
        $uuid = Uuid::factory()->create();
        $presupuesto = Presupuesto::factory()->create();
        $descripcion = fake()->word();
        $cantidad = fake()->randomFloat(/** decimal_attributes **/);
        $unidad_medida = fake()->word();
        $precio_unitario = fake()->randomFloat(/** decimal_attributes **/);
        $subtotal = fake()->randomFloat(/** decimal_attributes **/);
        $orden = fake()->numberBetween(-10000, 10000);

        $response = $this->post(route('presupuesto-detalles.store'), [
            'uuid' => $uuid->id,
            'presupuesto_id' => $presupuesto->id,
            'descripcion' => $descripcion,
            'cantidad' => $cantidad,
            'unidad_medida' => $unidad_medida,
            'precio_unitario' => $precio_unitario,
            'subtotal' => $subtotal,
            'orden' => $orden,
        ]);

        $presupuestoDetalles = PresupuestoDetalle::query()
            ->where('uuid', $uuid->id)
            ->where('presupuesto_id', $presupuesto->id)
            ->where('descripcion', $descripcion)
            ->where('cantidad', $cantidad)
            ->where('unidad_medida', $unidad_medida)
            ->where('precio_unitario', $precio_unitario)
            ->where('subtotal', $subtotal)
            ->where('orden', $orden)
            ->get();
        $this->assertCount(1, $presupuestoDetalles);
        $presupuestoDetalle = $presupuestoDetalles->first();

        $response->assertRedirect(route('presupuestoDetalles.index'));
        $response->assertSessionHas('presupuestoDetalle.id', $presupuestoDetalle->id);
    }


    #[Test]
    public function show_displays_view(): void
    {
        $presupuestoDetalle = PresupuestoDetalle::factory()->create();

        $response = $this->get(route('presupuesto-detalles.show', $presupuestoDetalle));

        $response->assertOk();
        $response->assertViewIs('presupuestoDetalle.show');
        $response->assertViewHas('presupuestoDetalle', $presupuestoDetalle);
    }


    #[Test]
    public function edit_displays_view(): void
    {
        $presupuestoDetalle = PresupuestoDetalle::factory()->create();

        $response = $this->get(route('presupuesto-detalles.edit', $presupuestoDetalle));

        $response->assertOk();
        $response->assertViewIs('presupuestoDetalle.edit');
        $response->assertViewHas('presupuestoDetalle', $presupuestoDetalle);
    }


    #[Test]
    public function update_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\PresupuestoDetalleController::class,
            'update',
            \App\Http\Requests\PresupuestoDetalleUpdateRequest::class
        );
    }

    #[Test]
    public function update_redirects(): void
    {
        $presupuestoDetalle = PresupuestoDetalle::factory()->create();
        $uuid = Uuid::factory()->create();
        $presupuesto = Presupuesto::factory()->create();
        $descripcion = fake()->word();
        $cantidad = fake()->randomFloat(/** decimal_attributes **/);
        $unidad_medida = fake()->word();
        $precio_unitario = fake()->randomFloat(/** decimal_attributes **/);
        $subtotal = fake()->randomFloat(/** decimal_attributes **/);
        $orden = fake()->numberBetween(-10000, 10000);

        $response = $this->put(route('presupuesto-detalles.update', $presupuestoDetalle), [
            'uuid' => $uuid->id,
            'presupuesto_id' => $presupuesto->id,
            'descripcion' => $descripcion,
            'cantidad' => $cantidad,
            'unidad_medida' => $unidad_medida,
            'precio_unitario' => $precio_unitario,
            'subtotal' => $subtotal,
            'orden' => $orden,
        ]);

        $presupuestoDetalle->refresh();

        $response->assertRedirect(route('presupuestoDetalles.index'));
        $response->assertSessionHas('presupuestoDetalle.id', $presupuestoDetalle->id);

        $this->assertEquals($uuid->id, $presupuestoDetalle->uuid);
        $this->assertEquals($presupuesto->id, $presupuestoDetalle->presupuesto_id);
        $this->assertEquals($descripcion, $presupuestoDetalle->descripcion);
        $this->assertEquals($cantidad, $presupuestoDetalle->cantidad);
        $this->assertEquals($unidad_medida, $presupuestoDetalle->unidad_medida);
        $this->assertEquals($precio_unitario, $presupuestoDetalle->precio_unitario);
        $this->assertEquals($subtotal, $presupuestoDetalle->subtotal);
        $this->assertEquals($orden, $presupuestoDetalle->orden);
    }


    #[Test]
    public function destroy_deletes_and_redirects(): void
    {
        $presupuestoDetalle = PresupuestoDetalle::factory()->create();

        $response = $this->delete(route('presupuesto-detalles.destroy', $presupuestoDetalle));

        $response->assertRedirect(route('presupuestoDetalles.index'));

        $this->assertModelMissing($presupuestoDetalle);
    }
}
