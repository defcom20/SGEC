<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\OrdenServicio;
use App\Models\OrdenServicioDetalle;
use App\Models\Uuid;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use JMac\Testing\Traits\AdditionalAssertions;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\OrdenServicioDetalleController
 */
final class OrdenServicioDetalleControllerTest extends TestCase
{
    use AdditionalAssertions, RefreshDatabase, WithFaker;

    #[Test]
    public function index_displays_view(): void
    {
        $ordenServicioDetalles = OrdenServicioDetalle::factory()->count(3)->create();

        $response = $this->get(route('orden-servicio-detalles.index'));

        $response->assertOk();
        $response->assertViewIs('ordenServicioDetalle.index');
        $response->assertViewHas('ordenServicioDetalles', $ordenServicioDetalles);
    }


    #[Test]
    public function create_displays_view(): void
    {
        $response = $this->get(route('orden-servicio-detalles.create'));

        $response->assertOk();
        $response->assertViewIs('ordenServicioDetalle.create');
    }


    #[Test]
    public function store_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\OrdenServicioDetalleController::class,
            'store',
            \App\Http\Requests\OrdenServicioDetalleStoreRequest::class
        );
    }

    #[Test]
    public function store_saves_and_redirects(): void
    {
        $uuid = Uuid::factory()->create();
        $orden_servicio = OrdenServicio::factory()->create();
        $descripcion = fake()->word();
        $cantidad = fake()->randomFloat(/** decimal_attributes **/);
        $unidad_medida = fake()->word();
        $precio_unitario = fake()->randomFloat(/** decimal_attributes **/);
        $subtotal = fake()->randomFloat(/** decimal_attributes **/);

        $response = $this->post(route('orden-servicio-detalles.store'), [
            'uuid' => $uuid->id,
            'orden_servicio_id' => $orden_servicio->id,
            'descripcion' => $descripcion,
            'cantidad' => $cantidad,
            'unidad_medida' => $unidad_medida,
            'precio_unitario' => $precio_unitario,
            'subtotal' => $subtotal,
        ]);

        $ordenServicioDetalles = OrdenServicioDetalle::query()
            ->where('uuid', $uuid->id)
            ->where('orden_servicio_id', $orden_servicio->id)
            ->where('descripcion', $descripcion)
            ->where('cantidad', $cantidad)
            ->where('unidad_medida', $unidad_medida)
            ->where('precio_unitario', $precio_unitario)
            ->where('subtotal', $subtotal)
            ->get();
        $this->assertCount(1, $ordenServicioDetalles);
        $ordenServicioDetalle = $ordenServicioDetalles->first();

        $response->assertRedirect(route('ordenServicioDetalles.index'));
        $response->assertSessionHas('ordenServicioDetalle.id', $ordenServicioDetalle->id);
    }


    #[Test]
    public function show_displays_view(): void
    {
        $ordenServicioDetalle = OrdenServicioDetalle::factory()->create();

        $response = $this->get(route('orden-servicio-detalles.show', $ordenServicioDetalle));

        $response->assertOk();
        $response->assertViewIs('ordenServicioDetalle.show');
        $response->assertViewHas('ordenServicioDetalle', $ordenServicioDetalle);
    }


    #[Test]
    public function edit_displays_view(): void
    {
        $ordenServicioDetalle = OrdenServicioDetalle::factory()->create();

        $response = $this->get(route('orden-servicio-detalles.edit', $ordenServicioDetalle));

        $response->assertOk();
        $response->assertViewIs('ordenServicioDetalle.edit');
        $response->assertViewHas('ordenServicioDetalle', $ordenServicioDetalle);
    }


    #[Test]
    public function update_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\OrdenServicioDetalleController::class,
            'update',
            \App\Http\Requests\OrdenServicioDetalleUpdateRequest::class
        );
    }

    #[Test]
    public function update_redirects(): void
    {
        $ordenServicioDetalle = OrdenServicioDetalle::factory()->create();
        $uuid = Uuid::factory()->create();
        $orden_servicio = OrdenServicio::factory()->create();
        $descripcion = fake()->word();
        $cantidad = fake()->randomFloat(/** decimal_attributes **/);
        $unidad_medida = fake()->word();
        $precio_unitario = fake()->randomFloat(/** decimal_attributes **/);
        $subtotal = fake()->randomFloat(/** decimal_attributes **/);

        $response = $this->put(route('orden-servicio-detalles.update', $ordenServicioDetalle), [
            'uuid' => $uuid->id,
            'orden_servicio_id' => $orden_servicio->id,
            'descripcion' => $descripcion,
            'cantidad' => $cantidad,
            'unidad_medida' => $unidad_medida,
            'precio_unitario' => $precio_unitario,
            'subtotal' => $subtotal,
        ]);

        $ordenServicioDetalle->refresh();

        $response->assertRedirect(route('ordenServicioDetalles.index'));
        $response->assertSessionHas('ordenServicioDetalle.id', $ordenServicioDetalle->id);

        $this->assertEquals($uuid->id, $ordenServicioDetalle->uuid);
        $this->assertEquals($orden_servicio->id, $ordenServicioDetalle->orden_servicio_id);
        $this->assertEquals($descripcion, $ordenServicioDetalle->descripcion);
        $this->assertEquals($cantidad, $ordenServicioDetalle->cantidad);
        $this->assertEquals($unidad_medida, $ordenServicioDetalle->unidad_medida);
        $this->assertEquals($precio_unitario, $ordenServicioDetalle->precio_unitario);
        $this->assertEquals($subtotal, $ordenServicioDetalle->subtotal);
    }


    #[Test]
    public function destroy_deletes_and_redirects(): void
    {
        $ordenServicioDetalle = OrdenServicioDetalle::factory()->create();

        $response = $this->delete(route('orden-servicio-detalles.destroy', $ordenServicioDetalle));

        $response->assertRedirect(route('ordenServicioDetalles.index'));

        $this->assertModelMissing($ordenServicioDetalle);
    }
}
