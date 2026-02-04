<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Rol;
use App\Models\Uuid;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use JMac\Testing\Traits\AdditionalAssertions;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\RolController
 */
final class RolControllerTest extends TestCase
{
    use AdditionalAssertions, RefreshDatabase, WithFaker;

    #[Test]
    public function index_displays_view(): void
    {
        $rols = Rol::factory()->count(3)->create();

        $response = $this->get(route('rols.index'));

        $response->assertOk();
        $response->assertViewIs('rol.index');
        $response->assertViewHas('rols', $rols);
    }


    #[Test]
    public function create_displays_view(): void
    {
        $response = $this->get(route('rols.create'));

        $response->assertOk();
        $response->assertViewIs('rol.create');
    }


    #[Test]
    public function store_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\RolController::class,
            'store',
            \App\Http\Requests\RolStoreRequest::class
        );
    }

    #[Test]
    public function store_saves_and_redirects(): void
    {
        $uuid = Uuid::factory()->create();
        $nombre = fake()->word();

        $response = $this->post(route('rols.store'), [
            'uuid' => $uuid->id,
            'nombre' => $nombre,
        ]);

        $rols = Rol::query()
            ->where('uuid', $uuid->id)
            ->where('nombre', $nombre)
            ->get();
        $this->assertCount(1, $rols);
        $rol = $rols->first();

        $response->assertRedirect(route('rols.index'));
        $response->assertSessionHas('rol.id', $rol->id);
    }


    #[Test]
    public function show_displays_view(): void
    {
        $rol = Rol::factory()->create();

        $response = $this->get(route('rols.show', $rol));

        $response->assertOk();
        $response->assertViewIs('rol.show');
        $response->assertViewHas('rol', $rol);
    }


    #[Test]
    public function edit_displays_view(): void
    {
        $rol = Rol::factory()->create();

        $response = $this->get(route('rols.edit', $rol));

        $response->assertOk();
        $response->assertViewIs('rol.edit');
        $response->assertViewHas('rol', $rol);
    }


    #[Test]
    public function update_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\RolController::class,
            'update',
            \App\Http\Requests\RolUpdateRequest::class
        );
    }

    #[Test]
    public function update_redirects(): void
    {
        $rol = Rol::factory()->create();
        $uuid = Uuid::factory()->create();
        $nombre = fake()->word();

        $response = $this->put(route('rols.update', $rol), [
            'uuid' => $uuid->id,
            'nombre' => $nombre,
        ]);

        $rol->refresh();

        $response->assertRedirect(route('rols.index'));
        $response->assertSessionHas('rol.id', $rol->id);

        $this->assertEquals($uuid->id, $rol->uuid);
        $this->assertEquals($nombre, $rol->nombre);
    }


    #[Test]
    public function destroy_deletes_and_redirects(): void
    {
        $rol = Rol::factory()->create();

        $response = $this->delete(route('rols.destroy', $rol));

        $response->assertRedirect(route('rols.index'));

        $this->assertModelMissing($rol);
    }
}
