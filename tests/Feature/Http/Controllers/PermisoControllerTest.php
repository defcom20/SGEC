<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Permiso;
use App\Models\Uuid;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use JMac\Testing\Traits\AdditionalAssertions;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\PermisoController
 */
final class PermisoControllerTest extends TestCase
{
    use AdditionalAssertions, RefreshDatabase, WithFaker;

    #[Test]
    public function index_displays_view(): void
    {
        $permisos = Permiso::factory()->count(3)->create();

        $response = $this->get(route('permisos.index'));

        $response->assertOk();
        $response->assertViewIs('permiso.index');
        $response->assertViewHas('permisos', $permisos);
    }


    #[Test]
    public function create_displays_view(): void
    {
        $response = $this->get(route('permisos.create'));

        $response->assertOk();
        $response->assertViewIs('permiso.create');
    }


    #[Test]
    public function store_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\PermisoController::class,
            'store',
            \App\Http\Requests\PermisoStoreRequest::class
        );
    }

    #[Test]
    public function store_saves_and_redirects(): void
    {
        $uuid = Uuid::factory()->create();
        $modulo = fake()->word();
        $accion = fake()->word();

        $response = $this->post(route('permisos.store'), [
            'uuid' => $uuid->id,
            'modulo' => $modulo,
            'accion' => $accion,
        ]);

        $permisos = Permiso::query()
            ->where('uuid', $uuid->id)
            ->where('modulo', $modulo)
            ->where('accion', $accion)
            ->get();
        $this->assertCount(1, $permisos);
        $permiso = $permisos->first();

        $response->assertRedirect(route('permisos.index'));
        $response->assertSessionHas('permiso.id', $permiso->id);
    }


    #[Test]
    public function show_displays_view(): void
    {
        $permiso = Permiso::factory()->create();

        $response = $this->get(route('permisos.show', $permiso));

        $response->assertOk();
        $response->assertViewIs('permiso.show');
        $response->assertViewHas('permiso', $permiso);
    }


    #[Test]
    public function edit_displays_view(): void
    {
        $permiso = Permiso::factory()->create();

        $response = $this->get(route('permisos.edit', $permiso));

        $response->assertOk();
        $response->assertViewIs('permiso.edit');
        $response->assertViewHas('permiso', $permiso);
    }


    #[Test]
    public function update_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\PermisoController::class,
            'update',
            \App\Http\Requests\PermisoUpdateRequest::class
        );
    }

    #[Test]
    public function update_redirects(): void
    {
        $permiso = Permiso::factory()->create();
        $uuid = Uuid::factory()->create();
        $modulo = fake()->word();
        $accion = fake()->word();

        $response = $this->put(route('permisos.update', $permiso), [
            'uuid' => $uuid->id,
            'modulo' => $modulo,
            'accion' => $accion,
        ]);

        $permiso->refresh();

        $response->assertRedirect(route('permisos.index'));
        $response->assertSessionHas('permiso.id', $permiso->id);

        $this->assertEquals($uuid->id, $permiso->uuid);
        $this->assertEquals($modulo, $permiso->modulo);
        $this->assertEquals($accion, $permiso->accion);
    }


    #[Test]
    public function destroy_deletes_and_redirects(): void
    {
        $permiso = Permiso::factory()->create();

        $response = $this->delete(route('permisos.destroy', $permiso));

        $response->assertRedirect(route('permisos.index'));

        $this->assertModelMissing($permiso);
    }
}
