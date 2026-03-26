<?php

namespace Tests\Feature\Inventario;

use Tests\TestCase;
use Illuminate\Support\Facades\Http;
use PHPUnit\Framework\Attributes\Test;

class RecetasControllerTest extends TestCase
{
    // ===========================================
    // GET /inventario/recetas -> index
    // ===========================================

    #[Test]
    public function index_retorna_vista_correcta_con_datos(): void
    {
        Http::fake([
            '*/recetas/optimizadas' => Http::response([
                [
                    'idProducto' => 1,
                    'nombreProducto' => 'Pan',
                    'idIngrediente' => 1,
                    'cantidadRequerida' => 500,
                    'idUnidad' => 1
                ]
            ], 200),
            '*/productos' => Http::response([
                ['idProducto' => 1, 'nombreProducto' => 'Pan']
            ], 200),
            '*/recetas/lista-modal' => Http::response([
                ['idIngrediente' => 1, 'nombre' => 'Harina']
            ], 200)
        ]);

        $response = $this->get(route('recetas.index'));

        $response->assertStatus(200);
        $response->assertViewIs('inventarioviews.recetas.index');
        $response->assertViewHas('recetas');
        $response->assertViewHas('productos');
        $response->assertViewHas('ingredientes');
    }

    #[Test]
    public function index_maneja_error_en_apis_correctamente(): void
    {
        Http::fake([
            '*/recetas/optimizadas' => Http::response('Error', 500),
            '*/productos' => Http::response('Error', 500),
            '*/recetas/lista-modal' => Http::response('Error', 500)
        ]);

        $response = $this->get(route('recetas.index'));

        $response->assertStatus(200);
        $response->assertViewIs('inventarioviews.recetas.index');
        $response->assertViewHas('recetas', []);
        $response->assertViewHas('productos', []);
        $response->assertViewHas('ingredientes', []);
        $response->assertViewHas('error');
    }

    // ===========================================
    // GET /inventario/recetas/show/{idProducto} -> show
    // ===========================================

    #[Test]
    public function show_retorna_una_receta_existente(): void
    {
        Http::fake([
            '*/recetas/optimizadas/producto/1' => Http::response([
                ['idIngrediente' => 1, 'cantidadRequerida' => 10, 'idUnidad' => 1]
            ], 200),
            '*/recetas/lista-modal' => Http::response([], 200)
        ]);

        $response = $this->get(route('recetas.show', ['idProducto' => 1]));

        $response->assertStatus(200);
        $response->assertViewIs('inventarioviews.recetas.show');
        $response->assertViewHas('detalles');
        $response->assertViewHas('idProducto', 1);
    }

    #[Test]
    public function show_redirige_si_no_existe_receta(): void
    {
        Http::fake([
            '*/recetas/optimizadas/producto/99' => Http::response('Not found', 404)
        ]);

        $response = $this->get(route('recetas.show', ['idProducto' => 99]));

        $response->assertRedirect(route('recetas.index'));
        $response->assertSessionHas('error');
    }

    // ===========================================
    // POST /inventario/recetas/store -> store
    // ===========================================

    #[Test]
    public function store_crea_receta_exitosamente_y_redirige(): void
    {
        Http::fake([
            '*/inventario/recetas' => Http::response(['mensaje' => 'Receta creada'], 201)
        ]);

        $response = $this->post(route('recetas.store'), [
            'idProducto' => 1,
            'detalles' => [
                ['idIngrediente' => 1, 'cantidadRequerida' => 100, 'idUnidad' => 1]
            ]
        ]);

        $response->assertRedirect(route('recetas.index'));
        $response->assertSessionHas('success');
    }

    #[Test]
    public function store_falla_si_faltan_detalles(): void
    {
        $response = $this->post(route('recetas.store'), [
            'idProducto' => 1,
            // Sin detalles
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('error');
    }

    // ===========================================
    // PUT /inventario/recetas/update/{idProducto} -> update
    // ===========================================

    #[Test]
    public function update_actualiza_receta_exitosamente(): void
    {
        Http::fake([
            '*/inventario/recetas/producto/1' => Http::response(['mensaje' => 'Actualizado'], 200)
        ]);

        $response = $this->put(route('recetas.update', ['idProducto' => 1]), [
            'detalles' => [
                ['idIngrediente' => 2, 'cantidadRequerida' => 200, 'idUnidad' => 1]
            ]
        ]);

        $response->assertRedirect(route('recetas.show', 1));
        $response->assertSessionHas('success');
    }

    // ===========================================
    // DELETE /inventario/recetas/delete/{idProducto} -> destroy
    // ===========================================

    #[Test]
    public function destroy_elimina_receta_exitosamente(): void
    {
        Http::fake([
            '*/inventario/recetas/1' => Http::response('', 200)
        ]);

        $response = $this->delete(route('recetas.destroy', ['idProducto' => 1]));

        $response->assertRedirect(route('recetas.index'));
        $response->assertSessionHas('success');
    }
}
