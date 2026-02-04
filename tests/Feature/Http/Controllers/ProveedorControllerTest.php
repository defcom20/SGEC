<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Proveedor;
use App\Models\User;
use App\Models\UsuarioCreacion;
use App\Models\Uuid;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use JMac\Testing\Traits\AdditionalAssertions;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\ProveedorController
 */
final class ProveedorControllerTest extends TestCase
{
    use AdditionalAssertions, RefreshDatabase, WithFaker;

    #[Test]
    public function index_displays_view(): void
    {
        $proveedors = Proveedor::factory()->count(3)->create();

        $response = $this->get(route('proveedors.index'));

        $response->assertOk();
        $response->assertViewIs('proveedor.index');
        $response->assertViewHas('proveedors', $proveedors);
    }


    #[Test]
    public function create_displays_view(): void
    {
        $response = $this->get(route('proveedors.create'));

        $response->assertOk();
        $response->assertViewIs('proveedor.create');
    }


    #[Test]
    public function store_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\ProveedorController::class,
            'store',
            \App\Http\Requests\ProveedorStoreRequest::class
        );
    }

    #[Test]
    public function store_saves_and_redirects(): void
    {
        $uuid = Uuid::factory()->create();
        $tipo_documento = fake()->randomElement(/** enum_attributes **/);
        $numero_documento = fake()->word();
        $razon_social = fake()->word();
        $estado = fake()->randomElement(/** enum_attributes **/);
        $usuario_creacion = UsuarioCreacion::factory()->create();
        $user = User::factory()->create();

        $response = $this->post(route('proveedors.store'), [
            'uuid' => $uuid->id,
            'tipo_documento' => $tipo_documento,
            'numero_documento' => $numero_documento,
            'razon_social' => $razon_social,
            'estado' => $estado,
            'usuario_creacion_id' => $usuario_creacion->id,
            'user_id' => $user->id,
        ]);

        $proveedors = Proveedor::query()
            ->where('uuid', $uuid->id)
            ->where('tipo_documento', $tipo_documento)
            ->where('numero_documento', $numero_documento)
            ->where('razon_social', $razon_social)
            ->where('estado', $estado)
            ->where('usuario_creacion_id', $usuario_creacion->id)
            ->where('user_id', $user->id)
            ->get();
        $this->assertCount(1, $proveedors);
        $proveedor = $proveedors->first();

        $response->assertRedirect(route('proveedors.index'));
        $response->assertSessionHas('proveedor.id', $proveedor->id);
    }


    #[Test]
    public function show_displays_view(): void
    {
        $proveedor = Proveedor::factory()->create();

        $response = $this->get(route('proveedors.show', $proveedor));

        $response->assertOk();
        $response->assertViewIs('proveedor.show');
        $response->assertViewHas('proveedor', $proveedor);
    }


    #[Test]
    public function edit_displays_view(): void
    {
        $proveedor = Proveedor::factory()->create();

        $response = $this->get(route('proveedors.edit', $proveedor));

        $response->assertOk();
        $response->assertViewIs('proveedor.edit');
        $response->assertViewHas('proveedor', $proveedor);
    }


    #[Test]
    public function update_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\ProveedorController::class,
            'update',
            \App\Http\Requests\ProveedorUpdateRequest::class
        );
    }

    #[Test]
    public function update_redirects(): void
    {
        $proveedor = Proveedor::factory()->create();
        $uuid = Uuid::factory()->create();
        $tipo_documento = fake()->randomElement(/** enum_attributes **/);
        $numero_documento = fake()->word();
        $razon_social = fake()->word();
        $estado = fake()->randomElement(/** enum_attributes **/);
        $usuario_creacion = UsuarioCreacion::factory()->create();
        $user = User::factory()->create();

        $response = $this->put(route('proveedors.update', $proveedor), [
            'uuid' => $uuid->id,
            'tipo_documento' => $tipo_documento,
            'numero_documento' => $numero_documento,
            'razon_social' => $razon_social,
            'estado' => $estado,
            'usuario_creacion_id' => $usuario_creacion->id,
            'user_id' => $user->id,
        ]);

        $proveedor->refresh();

        $response->assertRedirect(route('proveedors.index'));
        $response->assertSessionHas('proveedor.id', $proveedor->id);

        $this->assertEquals($uuid->id, $proveedor->uuid);
        $this->assertEquals($tipo_documento, $proveedor->tipo_documento);
        $this->assertEquals($numero_documento, $proveedor->numero_documento);
        $this->assertEquals($razon_social, $proveedor->razon_social);
        $this->assertEquals($estado, $proveedor->estado);
        $this->assertEquals($usuario_creacion->id, $proveedor->usuario_creacion_id);
        $this->assertEquals($user->id, $proveedor->user_id);
    }


    #[Test]
    public function destroy_deletes_and_redirects(): void
    {
        $proveedor = Proveedor::factory()->create();

        $response = $this->delete(route('proveedors.destroy', $proveedor));

        $response->assertRedirect(route('proveedors.index'));

        $this->assertSoftDeleted($proveedor);
    }
}
