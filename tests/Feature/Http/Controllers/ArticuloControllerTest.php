<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Articulo;
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
 * @see \App\Http\Controllers\ArticuloController
 */
final class ArticuloControllerTest extends TestCase
{
    use AdditionalAssertions, RefreshDatabase, WithFaker;

    #[Test]
    public function index_displays_view(): void
    {
        $articulos = Articulo::factory()->count(3)->create();

        $response = $this->get(route('articulos.index'));

        $response->assertOk();
        $response->assertViewIs('articulo.index');
        $response->assertViewHas('articulos', $articulos);
    }


    #[Test]
    public function create_displays_view(): void
    {
        $response = $this->get(route('articulos.create'));

        $response->assertOk();
        $response->assertViewIs('articulo.create');
    }


    #[Test]
    public function store_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\ArticuloController::class,
            'store',
            \App\Http\Requests\ArticuloStoreRequest::class
        );
    }

    #[Test]
    public function store_saves_and_redirects(): void
    {
        $uuid = Uuid::factory()->create();
        $codigo = fake()->word();
        $descripcion = fake()->word();
        $unidad_medida = fake()->word();
        $proveedor = Proveedor::factory()->create();
        $precio_compra = fake()->randomFloat(/** decimal_attributes **/);
        $precio_venta = fake()->randomFloat(/** decimal_attributes **/);
        $stock = fake()->randomFloat(/** decimal_attributes **/);
        $estado = fake()->randomElement(/** enum_attributes **/);
        $usuario_creacion = UsuarioCreacion::factory()->create();
        $user = User::factory()->create();

        $response = $this->post(route('articulos.store'), [
            'uuid' => $uuid->id,
            'codigo' => $codigo,
            'descripcion' => $descripcion,
            'unidad_medida' => $unidad_medida,
            'proveedor_id' => $proveedor->id,
            'precio_compra' => $precio_compra,
            'precio_venta' => $precio_venta,
            'stock' => $stock,
            'estado' => $estado,
            'usuario_creacion_id' => $usuario_creacion->id,
            'user_id' => $user->id,
        ]);

        $articulos = Articulo::query()
            ->where('uuid', $uuid->id)
            ->where('codigo', $codigo)
            ->where('descripcion', $descripcion)
            ->where('unidad_medida', $unidad_medida)
            ->where('proveedor_id', $proveedor->id)
            ->where('precio_compra', $precio_compra)
            ->where('precio_venta', $precio_venta)
            ->where('stock', $stock)
            ->where('estado', $estado)
            ->where('usuario_creacion_id', $usuario_creacion->id)
            ->where('user_id', $user->id)
            ->get();
        $this->assertCount(1, $articulos);
        $articulo = $articulos->first();

        $response->assertRedirect(route('articulos.index'));
        $response->assertSessionHas('articulo.id', $articulo->id);
    }


    #[Test]
    public function show_displays_view(): void
    {
        $articulo = Articulo::factory()->create();

        $response = $this->get(route('articulos.show', $articulo));

        $response->assertOk();
        $response->assertViewIs('articulo.show');
        $response->assertViewHas('articulo', $articulo);
    }


    #[Test]
    public function edit_displays_view(): void
    {
        $articulo = Articulo::factory()->create();

        $response = $this->get(route('articulos.edit', $articulo));

        $response->assertOk();
        $response->assertViewIs('articulo.edit');
        $response->assertViewHas('articulo', $articulo);
    }


    #[Test]
    public function update_uses_form_request_validation(): void
    {
        $this->assertActionUsesFormRequest(
            \App\Http\Controllers\ArticuloController::class,
            'update',
            \App\Http\Requests\ArticuloUpdateRequest::class
        );
    }

    #[Test]
    public function update_redirects(): void
    {
        $articulo = Articulo::factory()->create();
        $uuid = Uuid::factory()->create();
        $codigo = fake()->word();
        $descripcion = fake()->word();
        $unidad_medida = fake()->word();
        $proveedor = Proveedor::factory()->create();
        $precio_compra = fake()->randomFloat(/** decimal_attributes **/);
        $precio_venta = fake()->randomFloat(/** decimal_attributes **/);
        $stock = fake()->randomFloat(/** decimal_attributes **/);
        $estado = fake()->randomElement(/** enum_attributes **/);
        $usuario_creacion = UsuarioCreacion::factory()->create();
        $user = User::factory()->create();

        $response = $this->put(route('articulos.update', $articulo), [
            'uuid' => $uuid->id,
            'codigo' => $codigo,
            'descripcion' => $descripcion,
            'unidad_medida' => $unidad_medida,
            'proveedor_id' => $proveedor->id,
            'precio_compra' => $precio_compra,
            'precio_venta' => $precio_venta,
            'stock' => $stock,
            'estado' => $estado,
            'usuario_creacion_id' => $usuario_creacion->id,
            'user_id' => $user->id,
        ]);

        $articulo->refresh();

        $response->assertRedirect(route('articulos.index'));
        $response->assertSessionHas('articulo.id', $articulo->id);

        $this->assertEquals($uuid->id, $articulo->uuid);
        $this->assertEquals($codigo, $articulo->codigo);
        $this->assertEquals($descripcion, $articulo->descripcion);
        $this->assertEquals($unidad_medida, $articulo->unidad_medida);
        $this->assertEquals($proveedor->id, $articulo->proveedor_id);
        $this->assertEquals($precio_compra, $articulo->precio_compra);
        $this->assertEquals($precio_venta, $articulo->precio_venta);
        $this->assertEquals($stock, $articulo->stock);
        $this->assertEquals($estado, $articulo->estado);
        $this->assertEquals($usuario_creacion->id, $articulo->usuario_creacion_id);
        $this->assertEquals($user->id, $articulo->user_id);
    }


    #[Test]
    public function destroy_deletes_and_redirects(): void
    {
        $articulo = Articulo::factory()->create();

        $response = $this->delete(route('articulos.destroy', $articulo));

        $response->assertRedirect(route('articulos.index'));

        $this->assertSoftDeleted($articulo);
    }
}
