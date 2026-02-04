<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Cliente;
use App\Models\User;
use App\Models\UsuarioCreacion;
use App\Models\Uuid;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use JMac\Testing\Traits\AdditionalAssertions;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\ClienteController
 */
final class ClienteControllerTest extends TestCase
{
    use AdditionalAssertions, RefreshDatabase, WithFaker;

    #[Test]
    public function index_displays_view(): void
    {
        $clientes = Cliente::factory()->count(3)->create();

        $response = $this->get(route('clientes.index'));

        $response->assertOk();
        $response->assertViewIs('cliente.index');
        $response->assertViewHas('clientes', $clientes);
    }


    #[Test]
    public function create_displays_view(): void
    {
        $response = $this->get(route('clientes.create'));

        $response->assertOk();
        $response->assertViewIs('cliente.create');
    }


    #[Test]
    public function store_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\ClienteController::class,
            'store',
            \App\Http\Requests\ClienteStoreRequest::class
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

        $response = $this->post(route('clientes.store'), [
            'uuid' => $uuid->id,
            'tipo_documento' => $tipo_documento,
            'numero_documento' => $numero_documento,
            'razon_social' => $razon_social,
            'estado' => $estado,
            'usuario_creacion_id' => $usuario_creacion->id,
            'user_id' => $user->id,
        ]);

        $clientes = Cliente::query()
            ->where('uuid', $uuid->id)
            ->where('tipo_documento', $tipo_documento)
            ->where('numero_documento', $numero_documento)
            ->where('razon_social', $razon_social)
            ->where('estado', $estado)
            ->where('usuario_creacion_id', $usuario_creacion->id)
            ->where('user_id', $user->id)
            ->get();
        $this->assertCount(1, $clientes);
        $cliente = $clientes->first();

        $response->assertRedirect(route('clientes.index'));
        $response->assertSessionHas('cliente.id', $cliente->id);
    }


    #[Test]
    public function show_displays_view(): void
    {
        $cliente = Cliente::factory()->create();

        $response = $this->get(route('clientes.show', $cliente));

        $response->assertOk();
        $response->assertViewIs('cliente.show');
        $response->assertViewHas('cliente', $cliente);
    }


    #[Test]
    public function edit_displays_view(): void
    {
        $cliente = Cliente::factory()->create();

        $response = $this->get(route('clientes.edit', $cliente));

        $response->assertOk();
        $response->assertViewIs('cliente.edit');
        $response->assertViewHas('cliente', $cliente);
    }


    #[Test]
    public function update_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\ClienteController::class,
            'update',
            \App\Http\Requests\ClienteUpdateRequest::class
        );
    }

    #[Test]
    public function update_redirects(): void
    {
        $cliente = Cliente::factory()->create();
        $uuid = Uuid::factory()->create();
        $tipo_documento = fake()->randomElement(/** enum_attributes **/);
        $numero_documento = fake()->word();
        $razon_social = fake()->word();
        $estado = fake()->randomElement(/** enum_attributes **/);
        $usuario_creacion = UsuarioCreacion::factory()->create();
        $user = User::factory()->create();

        $response = $this->put(route('clientes.update', $cliente), [
            'uuid' => $uuid->id,
            'tipo_documento' => $tipo_documento,
            'numero_documento' => $numero_documento,
            'razon_social' => $razon_social,
            'estado' => $estado,
            'usuario_creacion_id' => $usuario_creacion->id,
            'user_id' => $user->id,
        ]);

        $cliente->refresh();

        $response->assertRedirect(route('clientes.index'));
        $response->assertSessionHas('cliente.id', $cliente->id);

        $this->assertEquals($uuid->id, $cliente->uuid);
        $this->assertEquals($tipo_documento, $cliente->tipo_documento);
        $this->assertEquals($numero_documento, $cliente->numero_documento);
        $this->assertEquals($razon_social, $cliente->razon_social);
        $this->assertEquals($estado, $cliente->estado);
        $this->assertEquals($usuario_creacion->id, $cliente->usuario_creacion_id);
        $this->assertEquals($user->id, $cliente->user_id);
    }


    #[Test]
    public function destroy_deletes_and_redirects(): void
    {
        $cliente = Cliente::factory()->create();

        $response = $this->delete(route('clientes.destroy', $cliente));

        $response->assertRedirect(route('clientes.index'));

        $this->assertSoftDeleted($cliente);
    }
}
