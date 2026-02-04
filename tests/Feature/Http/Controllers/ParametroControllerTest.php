<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Parametro;
use App\Models\Uuid;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use JMac\Testing\Traits\AdditionalAssertions;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\ParametroController
 */
final class ParametroControllerTest extends TestCase
{
    use AdditionalAssertions, RefreshDatabase, WithFaker;

    #[Test]
    public function index_displays_view(): void
    {
        $parametros = Parametro::factory()->count(3)->create();

        $response = $this->get(route('parametros.index'));

        $response->assertOk();
        $response->assertViewIs('parametro.index');
        $response->assertViewHas('parametros', $parametros);
    }


    #[Test]
    public function create_displays_view(): void
    {
        $response = $this->get(route('parametros.create'));

        $response->assertOk();
        $response->assertViewIs('parametro.create');
    }


    #[Test]
    public function store_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\ParametroController::class,
            'store',
            \App\Http\Requests\ParametroStoreRequest::class
        );
    }

    #[Test]
    public function store_saves_and_redirects(): void
    {
        $uuid = Uuid::factory()->create();
        $clave = fake()->word();
        $valor = fake()->text();
        $tipo_dato = fake()->randomElement(/** enum_attributes **/);

        $response = $this->post(route('parametros.store'), [
            'uuid' => $uuid->id,
            'clave' => $clave,
            'valor' => $valor,
            'tipo_dato' => $tipo_dato,
        ]);

        $parametros = Parametro::query()
            ->where('uuid', $uuid->id)
            ->where('clave', $clave)
            ->where('valor', $valor)
            ->where('tipo_dato', $tipo_dato)
            ->get();
        $this->assertCount(1, $parametros);
        $parametro = $parametros->first();

        $response->assertRedirect(route('parametros.index'));
        $response->assertSessionHas('parametro.id', $parametro->id);
    }


    #[Test]
    public function show_displays_view(): void
    {
        $parametro = Parametro::factory()->create();

        $response = $this->get(route('parametros.show', $parametro));

        $response->assertOk();
        $response->assertViewIs('parametro.show');
        $response->assertViewHas('parametro', $parametro);
    }


    #[Test]
    public function edit_displays_view(): void
    {
        $parametro = Parametro::factory()->create();

        $response = $this->get(route('parametros.edit', $parametro));

        $response->assertOk();
        $response->assertViewIs('parametro.edit');
        $response->assertViewHas('parametro', $parametro);
    }


    #[Test]
    public function update_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\ParametroController::class,
            'update',
            \App\Http\Requests\ParametroUpdateRequest::class
        );
    }

    #[Test]
    public function update_redirects(): void
    {
        $parametro = Parametro::factory()->create();
        $uuid = Uuid::factory()->create();
        $clave = fake()->word();
        $valor = fake()->text();
        $tipo_dato = fake()->randomElement(/** enum_attributes **/);

        $response = $this->put(route('parametros.update', $parametro), [
            'uuid' => $uuid->id,
            'clave' => $clave,
            'valor' => $valor,
            'tipo_dato' => $tipo_dato,
        ]);

        $parametro->refresh();

        $response->assertRedirect(route('parametros.index'));
        $response->assertSessionHas('parametro.id', $parametro->id);

        $this->assertEquals($uuid->id, $parametro->uuid);
        $this->assertEquals($clave, $parametro->clave);
        $this->assertEquals($valor, $parametro->valor);
        $this->assertEquals($tipo_dato, $parametro->tipo_dato);
    }


    #[Test]
    public function destroy_deletes_and_redirects(): void
    {
        $parametro = Parametro::factory()->create();

        $response = $this->delete(route('parametros.destroy', $parametro));

        $response->assertRedirect(route('parametros.index'));

        $this->assertModelMissing($parametro);
    }
}
