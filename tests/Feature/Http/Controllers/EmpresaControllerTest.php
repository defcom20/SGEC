<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Empresa;
use App\Models\Uuid;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use JMac\Testing\Traits\AdditionalAssertions;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\EmpresaController
 */
final class EmpresaControllerTest extends TestCase
{
    use AdditionalAssertions, RefreshDatabase, WithFaker;

    #[Test]
    public function index_displays_view(): void
    {
        $empresas = Empresa::factory()->count(3)->create();

        $response = $this->get(route('empresas.index'));

        $response->assertOk();
        $response->assertViewIs('empresa.index');
        $response->assertViewHas('empresas', $empresas);
    }


    #[Test]
    public function create_displays_view(): void
    {
        $response = $this->get(route('empresas.create'));

        $response->assertOk();
        $response->assertViewIs('empresa.create');
    }


    #[Test]
    public function store_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\EmpresaController::class,
            'store',
            \App\Http\Requests\EmpresaStoreRequest::class
        );
    }

    #[Test]
    public function store_saves_and_redirects(): void
    {
        $uuid = Uuid::factory()->create();
        $ruc = fake()->word();
        $razon_social = fake()->word();

        $response = $this->post(route('empresas.store'), [
            'uuid' => $uuid->id,
            'ruc' => $ruc,
            'razon_social' => $razon_social,
        ]);

        $empresas = Empresa::query()
            ->where('uuid', $uuid->id)
            ->where('ruc', $ruc)
            ->where('razon_social', $razon_social)
            ->get();
        $this->assertCount(1, $empresas);
        $empresa = $empresas->first();

        $response->assertRedirect(route('empresas.index'));
        $response->assertSessionHas('empresa.id', $empresa->id);
    }


    #[Test]
    public function show_displays_view(): void
    {
        $empresa = Empresa::factory()->create();

        $response = $this->get(route('empresas.show', $empresa));

        $response->assertOk();
        $response->assertViewIs('empresa.show');
        $response->assertViewHas('empresa', $empresa);
    }


    #[Test]
    public function edit_displays_view(): void
    {
        $empresa = Empresa::factory()->create();

        $response = $this->get(route('empresas.edit', $empresa));

        $response->assertOk();
        $response->assertViewIs('empresa.edit');
        $response->assertViewHas('empresa', $empresa);
    }


    #[Test]
    public function update_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\EmpresaController::class,
            'update',
            \App\Http\Requests\EmpresaUpdateRequest::class
        );
    }

    #[Test]
    public function update_redirects(): void
    {
        $empresa = Empresa::factory()->create();
        $uuid = Uuid::factory()->create();
        $ruc = fake()->word();
        $razon_social = fake()->word();

        $response = $this->put(route('empresas.update', $empresa), [
            'uuid' => $uuid->id,
            'ruc' => $ruc,
            'razon_social' => $razon_social,
        ]);

        $empresa->refresh();

        $response->assertRedirect(route('empresas.index'));
        $response->assertSessionHas('empresa.id', $empresa->id);

        $this->assertEquals($uuid->id, $empresa->uuid);
        $this->assertEquals($ruc, $empresa->ruc);
        $this->assertEquals($razon_social, $empresa->razon_social);
    }


    #[Test]
    public function destroy_deletes_and_redirects(): void
    {
        $empresa = Empresa::factory()->create();

        $response = $this->delete(route('empresas.destroy', $empresa));

        $response->assertRedirect(route('empresas.index'));

        $this->assertModelMissing($empresa);
    }
}
