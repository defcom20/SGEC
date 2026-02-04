<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Servicio;
use App\Models\User;
use App\Models\UsuarioCreacion;
use App\Models\Uuid;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use JMac\Testing\Traits\AdditionalAssertions;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\ServicioController
 */
final class ServicioControllerTest extends TestCase
{
    use AdditionalAssertions, RefreshDatabase, WithFaker;

    #[Test]
    public function index_displays_view(): void
    {
        $servicios = Servicio::factory()->count(3)->create();

        $response = $this->get(route('servicios.index'));

        $response->assertOk();
        $response->assertViewIs('servicio.index');
        $response->assertViewHas('servicios', $servicios);
    }


    #[Test]
    public function create_displays_view(): void
    {
        $response = $this->get(route('servicios.create'));

        $response->assertOk();
        $response->assertViewIs('servicio.create');
    }


    #[Test]
    public function store_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\ServicioController::class,
            'store',
            \App\Http\Requests\ServicioStoreRequest::class
        );
    }

    #[Test]
    public function store_saves_and_redirects(): void
    {
        $uuid = Uuid::factory()->create();
        $codigo = fake()->word();
        $descripcion = fake()->word();
        $unidad_medida = fake()->word();
        $precio_referencial = fake()->randomFloat(/** decimal_attributes **/);
        $estado = fake()->randomElement(/** enum_attributes **/);
        $usuario_creacion = UsuarioCreacion::factory()->create();
        $user = User::factory()->create();

        $response = $this->post(route('servicios.store'), [
            'uuid' => $uuid->id,
            'codigo' => $codigo,
            'descripcion' => $descripcion,
            'unidad_medida' => $unidad_medida,
            'precio_referencial' => $precio_referencial,
            'estado' => $estado,
            'usuario_creacion_id' => $usuario_creacion->id,
            'user_id' => $user->id,
        ]);

        $servicios = Servicio::query()
            ->where('uuid', $uuid->id)
            ->where('codigo', $codigo)
            ->where('descripcion', $descripcion)
            ->where('unidad_medida', $unidad_medida)
            ->where('precio_referencial', $precio_referencial)
            ->where('estado', $estado)
            ->where('usuario_creacion_id', $usuario_creacion->id)
            ->where('user_id', $user->id)
            ->get();
        $this->assertCount(1, $servicios);
        $servicio = $servicios->first();

        $response->assertRedirect(route('servicios.index'));
        $response->assertSessionHas('servicio.id', $servicio->id);
    }


    #[Test]
    public function show_displays_view(): void
    {
        $servicio = Servicio::factory()->create();

        $response = $this->get(route('servicios.show', $servicio));

        $response->assertOk();
        $response->assertViewIs('servicio.show');
        $response->assertViewHas('servicio', $servicio);
    }


    #[Test]
    public function edit_displays_view(): void
    {
        $servicio = Servicio::factory()->create();

        $response = $this->get(route('servicios.edit', $servicio));

        $response->assertOk();
        $response->assertViewIs('servicio.edit');
        $response->assertViewHas('servicio', $servicio);
    }


    #[Test]
    public function update_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\ServicioController::class,
            'update',
            \App\Http\Requests\ServicioUpdateRequest::class
        );
    }

    #[Test]
    public function update_redirects(): void
    {
        $servicio = Servicio::factory()->create();
        $uuid = Uuid::factory()->create();
        $codigo = fake()->word();
        $descripcion = fake()->word();
        $unidad_medida = fake()->word();
        $precio_referencial = fake()->randomFloat(/** decimal_attributes **/);
        $estado = fake()->randomElement(/** enum_attributes **/);
        $usuario_creacion = UsuarioCreacion::factory()->create();
        $user = User::factory()->create();

        $response = $this->put(route('servicios.update', $servicio), [
            'uuid' => $uuid->id,
            'codigo' => $codigo,
            'descripcion' => $descripcion,
            'unidad_medida' => $unidad_medida,
            'precio_referencial' => $precio_referencial,
            'estado' => $estado,
            'usuario_creacion_id' => $usuario_creacion->id,
            'user_id' => $user->id,
        ]);

        $servicio->refresh();

        $response->assertRedirect(route('servicios.index'));
        $response->assertSessionHas('servicio.id', $servicio->id);

        $this->assertEquals($uuid->id, $servicio->uuid);
        $this->assertEquals($codigo, $servicio->codigo);
        $this->assertEquals($descripcion, $servicio->descripcion);
        $this->assertEquals($unidad_medida, $servicio->unidad_medida);
        $this->assertEquals($precio_referencial, $servicio->precio_referencial);
        $this->assertEquals($estado, $servicio->estado);
        $this->assertEquals($usuario_creacion->id, $servicio->usuario_creacion_id);
        $this->assertEquals($user->id, $servicio->user_id);
    }


    #[Test]
    public function destroy_deletes_and_redirects(): void
    {
        $servicio = Servicio::factory()->create();

        $response = $this->delete(route('servicios.destroy', $servicio));

        $response->assertRedirect(route('servicios.index'));

        $this->assertSoftDeleted($servicio);
    }
}
