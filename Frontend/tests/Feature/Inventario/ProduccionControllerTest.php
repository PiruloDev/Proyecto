<?php

namespace Tests\Feature\Inventario;

use Tests\TestCase;
use Illuminate\Support\Facades\Http;
use PHPUnit\Framework\Attributes\Test;

class ProduccionControllerTest extends TestCase
{
    // ===========================================
    // GET /inventario/produccion -> index
    // ===========================================

    #[Test]
    public function index_retorna_200_con_historial_y_recetas(): void
    {
        Http::fake([
            '*/inventario/produccion' => Http::response([
                [
                    'idProduccion' => 1,
                    'idProducto' => 1,
                    'cantidadProducida' => 10,
                    'fechaProduccion' => '2023-10-01'
                ]
            ], 200),
            '*/inventario/recetas/optimizadas' => Http::response([
                [
                    'idProducto' => 1,
                    'nombreProducto' => 'Pan Frances',
                    'idIngrediente' => 2,
                    'cantidadRequerida' => 500,
                    'idUnidad' => 1
                ]
            ], 200)
        ]);

        $response = $this->get(route('produccion.index'));

        $response->assertStatus(200);
        $response->assertViewIs('inventarioviews.produccion.index');
        $response->assertViewHas('historial');
        $response->assertViewHas('productosConReceta');
        $response->assertViewHas('recetas');
    }

    #[Test]
    public function index_maneja_error_y_devuelve_vista_con_array_vacio(): void
    {
        Http::fake([
            '*/inventario/produccion' => Http::response('Server Error', 500),
            '*/inventario/recetas/optimizadas' => Http::response('Server Error', 500)
        ]);

        $response = $this->get(route('produccion.index'));

        $response->assertStatus(200);
        $response->assertViewIs('inventarioviews.produccion.index');
        $response->assertViewHas('historial', []);
        $response->assertViewHas('recetas', []);
        $response->assertViewHas('error');
    }

    // ===========================================
    // POST /inventario/produccion/store -> store
    // ===========================================

    #[Test]
    public function store_falla_validacion_si_faltan_campos_requeridos(): void
    {
        $response = $this->post(route('produccion.store'), []);

        $response->assertSessionHasErrors([
            'idProducto',
            'cantidadProducida'
        ]);
    }

    #[Test]
    public function store_registra_produccion_exitosamente(): void
    {
        Http::fake([
            '*/inventario/produccion' => Http::response([
                'mensaje' => 'Producción registrada',
                'idProduccion' => 100
            ], 200)
        ]);

        $response = $this->post(route('produccion.store'), [
            'idProducto' => 1,
            'cantidadProducida' => 20.5
        ]);

        $response->assertRedirect(route('produccion.index'));
        $response->assertSessionHas('success');
    }

    #[Test]
    public function store_falla_cuando_api_rechaza(): void
    {
        Http::fake([
            '*/inventario/produccion' => Http::response(['error' => 'Sin ingredientes'], 400)
        ]);

        $response = $this->post(route('produccion.store'), [
            'idProducto' => 1,
            'cantidadProducida' => 20.5
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('error');
    }

    // ===========================================
    // DELETE /inventario/produccion/delete/{id} -> destroy
    // ===========================================

    #[Test]
    public function destroy_elimina_produccion_exitosamente(): void
    {
        Http::fake([
            '*/inventario/produccion/1' => Http::response('', 204)
        ]);

        $response = $this->delete(route('produccion.destroy', ['id' => 1]));

        $response->assertRedirect(route('produccion.index'));
        $response->assertSessionHas('success');
    }

    #[Test]
    public function destroy_falla_cuando_api_retorna_error(): void
    {
        Http::fake([
            '*/inventario/produccion/99' => Http::response(['error' => 'No encontrado'], 404)
        ]);

        $response = $this->delete(route('produccion.destroy', ['id' => 99]));

        $response->assertRedirect();
        $response->assertSessionHas('error');
    }
}
