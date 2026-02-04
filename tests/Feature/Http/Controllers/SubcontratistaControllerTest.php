<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Subcontratista;
use App\Models\Subcontratistum;
use App\Models\User;
use App\Models\UsuarioCreacion;
use App\Models\Uuid;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use JMac\Testing\Traits\AdditionalAssertions;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\SubcontratistaController
 */
final class SubcontratistaControllerTest extends TestCase
{
    use AdditionalAssertions, RefreshDatabase, WithFaker;

    #[Test]
    public function index_displays_view(): void
    {
        $subcontratista = Subcontratista::factory()->count(3)->create();

        $response = $this->get(route('subcontratista.index'));

        $response->assertOk();
        $response->assertViewIs('subcontratistum.index');
        $response->assertViewHas('subcontratista', $subcontratista);
    }


    #[Test]
    public function create_displays_view(): void
    {
        $response = $this->get(route('subcontratista.create'));

        $response->assertOk();
        $response->assertViewIs('subcontratistum.create');
    }


    #[Test]
    public function store_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\SubcontratistaController::class,
            'store',
            \App\Http\Requests\SubcontratistaStoreRequest::class
        );
    }

    #[Test]
    public function store_saves_and_redirects(): void
    {
        $uuid = Uuid::factory()->create();
        $tipo = fake()->randomElement(/** enum_attributes **/);
        $tipo_documento = fake()->randomElement(/** enum_attributes **/);
        $numero_documento = fake()->word();
        $razon_social_nombre = fake()->word();
        $estado = fake()->randomElement(/** enum_attributes **/);
        $usuario_creacion = UsuarioCreacion::factory()->create();
        $user = User::factory()->create();

        $response = $this->post(route('subcontratista.store'), [
            'uuid' => $uuid->id,
            'tipo' => $tipo,
            'tipo_documento' => $tipo_documento,
            'numero_documento' => $numero_documento,
            'razon_social_nombre' => $razon_social_nombre,
            'estado' => $estado,
            'usuario_creacion_id' => $usuario_creacion->id,
            'user_id' => $user->id,
        ]);

        $subcontratista = Subcontratistum::query()
            ->where('uuid', $uuid->id)
            ->where('tipo', $tipo)
            ->where('tipo_documento', $tipo_documento)
            ->where('numero_documento', $numero_documento)
            ->where('razon_social_nombre', $razon_social_nombre)
            ->where('estado', $estado)
            ->where('usuario_creacion_id', $usuario_creacion->id)
            ->where('user_id', $user->id)
            ->get();
        $this->assertCount(1, $subcontratista);
        $subcontratistum = $subcontratista->first();

        $response->assertRedirect(route('subcontratista.index'));
        $response->assertSessionHas('subcontratistum.id', $subcontratistum->id);
    }


    #[Test]
    public function show_displays_view(): void
    {
        $subcontratistum = Subcontratista::factory()->create();

        $response = $this->get(route('subcontratista.show', $subcontratistum));

        $response->assertOk();
        $response->assertViewIs('subcontratistum.show');
        $response->assertViewHas('subcontratistum', $subcontratistum);
    }


    #[Test]
    public function edit_displays_view(): void
    {
        $subcontratistum = Subcontratista::factory()->create();

        $response = $this->get(route('subcontratista.edit', $subcontratistum));

        $response->assertOk();
        $response->assertViewIs('subcontratistum.edit');
        $response->assertViewHas('subcontratistum', $subcontratistum);
    }


    #[Test]
    public function update_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\SubcontratistaController::class,
            'update',
            \App\Http\Requests\SubcontratistaUpdateRequest::class
        );
    }

    #[Test]
    public function update_redirects(): void
    {
        $subcontratistum = Subcontratista::factory()->create();
        $uuid = Uuid::factory()->create();
        $tipo = fake()->randomElement(/** enum_attributes **/);
        $tipo_documento = fake()->randomElement(/** enum_attributes **/);
        $numero_documento = fake()->word();
        $razon_social_nombre = fake()->word();
        $estado = fake()->randomElement(/** enum_attributes **/);
        $usuario_creacion = UsuarioCreacion::factory()->create();
        $user = User::factory()->create();

        $response = $this->put(route('subcontratista.update', $subcontratistum), [
            'uuid' => $uuid->id,
            'tipo' => $tipo,
            'tipo_documento' => $tipo_documento,
            'numero_documento' => $numero_documento,
            'razon_social_nombre' => $razon_social_nombre,
            'estado' => $estado,
            'usuario_creacion_id' => $usuario_creacion->id,
            'user_id' => $user->id,
        ]);

        $subcontratistum->refresh();

        $response->assertRedirect(route('subcontratista.index'));
        $response->assertSessionHas('subcontratistum.id', $subcontratistum->id);

        $this->assertEquals($uuid->id, $subcontratistum->uuid);
        $this->assertEquals($tipo, $subcontratistum->tipo);
        $this->assertEquals($tipo_documento, $subcontratistum->tipo_documento);
        $this->assertEquals($numero_documento, $subcontratistum->numero_documento);
        $this->assertEquals($razon_social_nombre, $subcontratistum->razon_social_nombre);
        $this->assertEquals($estado, $subcontratistum->estado);
        $this->assertEquals($usuario_creacion->id, $subcontratistum->usuario_creacion_id);
        $this->assertEquals($user->id, $subcontratistum->user_id);
    }


    #[Test]
    public function destroy_deletes_and_redirects(): void
    {
        $subcontratistum = Subcontratista::factory()->create();
        $subcontratistum = Subcontratistum::factory()->create();

        $response = $this->delete(route('subcontratista.destroy', $subcontratistum));

        $response->assertRedirect(route('subcontratista.index'));

        $this->assertSoftDeleted($subcontratistum);
    }
}
