<?php

namespace Tests\Feature\Inventario;

use Tests\TestCase;
use Illuminate\Support\Facades\Http;
use PHPUnit\Framework\Attributes\Test;

class CategoriaIngredientesControllerTest extends TestCase
{
    private function mockApiOk(): void
    {
        Http::fake([
            '*/categorias/ingredientes' => Http::response([
                ['idCategoriaIngrediente' => 1, 'nombreCategoria' => 'Harinas'],
                ['idCategoriaIngrediente' => 2, 'nombreCategoria' => 'Lácteos'],
            ], 200)
        ]);
    }

    // =========================================================
    // index()
    // =========================================================

    #[Test]
    public function index_retorna_200_con_vista_correcta(): void
    {
        $this->mockApiOk();

        $response = $this->get('/inventario/categorias-ingredientes');

        $response->assertStatus(200);
        $response->assertViewIs('inventarioviews.categorias.index');
    }

    #[Test]
    public function index_pasa_variable_categorias_a_la_vista(): void
    {
        $this->mockApiOk();

        $response = $this->get('/inventario/categorias-ingredientes');

        $response->assertViewHas('categorias');
    }

    #[Test]
    public function index_carga_con_categorias_vacias_cuando_api_falla(): void
    {
        Http::fake([
            '*/categorias/ingredientes' => Http::response([], 500)
        ]);

        $response = $this->get('/inventario/categorias-ingredientes');

        // No da 500 — el controller devuelve vista con array vacío
        $response->assertStatus(200);
        $response->assertViewIs('inventarioviews.categorias.index');
        $response->assertViewHas('categorias', []);
        // El error va como variable de vista con ->with()
        $response->assertViewHas('error');
    }

    // =========================================================
    // store()
    // =========================================================

    #[Test]
    public function store_con_datos_validos_redirige_con_success(): void
    {
        Http::fake([
            '*/nuevacategoriaingrediente' => Http::response('Creada', 200)
        ]);

        $response = $this->post('/inventario/categorias-ingredientes/store', [
            'nombreCategoria' => 'Frutas Tropicales'
        ]);

        $response->assertRedirect(route('categorias-ingredientes.index'));
        $response->assertSessionHas('success', 'Categoría creada con éxito.');
    }

    #[Test]
    public function store_falla_si_nombreCategoria_esta_vacio(): void
    {
        $response = $this->post('/inventario/categorias-ingredientes/store', [
            'nombreCategoria' => ''
        ]);

        $response->assertSessionHasErrors(['nombreCategoria']);
    }

    #[Test]
    public function store_falla_si_nombreCategoria_supera_255_caracteres(): void
    {
        $response = $this->post('/inventario/categorias-ingredientes/store', [
            'nombreCategoria' => str_repeat('A', 256)
        ]);

        $response->assertSessionHasErrors(['nombreCategoria']);
    }

    #[Test]
    public function store_redirige_con_error_cuando_spring_rechaza(): void
    {
        Http::fake([
            '*/nuevacategoriaingrediente' => Http::response('Error', 400)
        ]);

        $response = $this->post('/inventario/categorias-ingredientes/store', [
            'nombreCategoria' => 'Frutas'
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('error');
    }

    // =========================================================
    // update()
    // =========================================================

    #[Test]
    public function update_con_datos_validos_redirige_con_success(): void
    {
        Http::fake([
            '*/categoriaingrediente/1' => Http::response('Actualizada', 200)
        ]);

        $response = $this->put('/inventario/categorias-ingredientes/update/1', [
            'nombreCategoria' => 'Harinas Premium'
        ]);

        $response->assertRedirect(route('categorias-ingredientes.index'));
        $response->assertSessionHas('success', 'Categoría actualizada con éxito.');
    }

    #[Test]
    public function update_falla_validacion_con_nombre_vacio(): void
    {
        $response = $this->put('/inventario/categorias-ingredientes/update/1', [
            'nombreCategoria' => ''
        ]);

        $response->assertSessionHasErrors(['nombreCategoria']);
    }

    // =========================================================
    // destroy()
    // =========================================================

    #[Test]
    public function destroy_elimina_y_redirige_con_success(): void
    {
        Http::fake([
            '*/eliminarcategoria/1' => Http::response('Eliminada', 200)
        ]);

        $response = $this->delete('/inventario/categorias-ingredientes/delete/1');

        $response->assertRedirect(route('categorias-ingredientes.index'));
        $response->assertSessionHas('success', 'Categoría eliminada con éxito.');
    }

    #[Test]
    public function destroy_redirige_con_error_cuando_spring_falla(): void
    {
        Http::fake([
            '*/eliminarcategoria/1' => Http::response('Error', 500)
        ]);

        $response = $this->delete('/inventario/categorias-ingredientes/delete/1');

        $response->assertRedirect();
        $response->assertSessionHas('error');
    }
}