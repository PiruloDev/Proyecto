<?php

namespace Tests\Feature\Inventario;

use Tests\TestCase;
use Illuminate\Support\Facades\Http;
use PHPUnit\Framework\Attributes\Test;

class IngredientesControllerTest extends TestCase
{
    // Mock completo de todos los endpoints que usa index()
    private function mockIndexApis(): void
    {
        Http::fake([
            '*/ingredientes/lista' => Http::response([
                [
                    'idIngrediente'         => 1,
                    'nombreIngrediente'     => 'Harina',
                    'referenciaIngrediente' => 'HAR-001',
                    'idProveedor'           => 1,
                    'idCategoria'           => 1,
                    'abreviaturaUnidad'     => 'kg'
                ]
            ], 200),
            '*/proveedores' => Http::response([
                ['idProveedor' => 1, 'nombreProv' => 'Harina Dorada']
            ], 200),
            '*/categorias/ingredientes' => Http::response([
                ['idCategoriaIngrediente' => 1, 'nombreCategoria' => 'Harinas']
            ], 200),
            '*/unidades-medida' => Http::response([
                ['idUnidad' => 1, 'nombreUnidad' => 'Kilogramo', 'abreviaturaUnidad' => 'kg']
            ], 200),
        ]);
    }

    // =========================================================
    // index()
    // =========================================================

    #[Test]
    public function index_retorna_200_con_vista_correcta(): void
    {
        $this->mockIndexApis();

        $response = $this->get('/inventario/ingredientes');

        $response->assertStatus(200);
        $response->assertViewIs('inventarioviews.ingredientes.index');
    }

    #[Test]
    public function index_pasa_las_4_variables_a_la_vista(): void
    {
        $this->mockIndexApis();

        $response = $this->get('/inventario/ingredientes');

        $response->assertViewHas('ingredientes');
        $response->assertViewHas('proveedores');
        $response->assertViewHas('categorias');
        $response->assertViewHas('unidades');
    }

    #[Test]
    public function index_carga_aunque_spring_falle_en_proveedores(): void
    {
        // Si Spring falla en proveedores/categorias/unidades,
        // el try/catch del controller devuelve arrays vacíos
        // y la vista igual debe cargar sin explotar
        Http::fake([
            '*/ingredientes/lista' => Http::response([], 200),
            '*/proveedores'        => Http::response([], 500),
            '*/categorias/*'       => Http::response([], 500),
            '*/unidades-medida'    => Http::response([], 500),
        ]);

        $response = $this->get('/inventario/ingredientes');

        // No debe dar 500 — el catch garantiza arrays vacíos
        $response->assertStatus(200);
    }

    // =========================================================
    // inventario() — stock
    // =========================================================

    #[Test]
    public function inventario_retorna_vista_con_ingredientes(): void
    {
        Http::fake([
            '*/ingredientes/cantidad' => Http::response([
                ['idIngrediente' => 1, 'nombreIngrediente' => 'Harina', 'cantidadIngrediente' => 50.0]
            ], 200)
        ]);

        $response = $this->get('/inventario/ingredientes/stock');

        $response->assertStatus(200);
        $response->assertViewIs('inventarioviews.ingredientes.inventario');
        $response->assertViewHas('ingredientes');
    }

    #[Test]
public function inventario_muestra_error_cuando_api_falla(): void
{
    Http::fake([
        '*/ingredientes/cantidad' => Http::response([], 500)
    ]);

    $response = $this->get('/inventario/ingredientes/stock');

    $response->assertStatus(200);
    $response->assertViewIs('inventarioviews.ingredientes.inventario'); 
    $response->assertViewHas('ingredientes', []);                       
    $response->assertViewHas('error');                                  
}

    // =========================================================
    // store()
    // =========================================================

    #[Test]
    public function store_con_datos_validos_redirige_con_success(): void
    {
        Http::fake([
            '*/crearingrediente' => Http::response('Creado con éxito', 200)
        ]);

        $response = $this->post('/inventario/ingredientes/store', [
            'idProveedor'           => 1,
            'idCategoria'           => 1,
            'idUnidadMedida'        => 1,
            'nombreIngrediente'     => 'Harina Test',
            'referenciaIngrediente' => 'HAR-TEST',
        ]);

        $response->assertRedirect(route('ingredientes.index'));
        $response->assertSessionHas('success', 'Ingrediente creado con éxito.');
    }

    #[Test]
    public function store_falla_si_falta_idProveedor(): void
    {
        $response = $this->post('/inventario/ingredientes/store', [
            'idCategoria'           => 1,
            'idUnidadMedida'        => 1,
            'nombreIngrediente'     => 'Harina',
            'referenciaIngrediente' => 'HAR-001',
            // idProveedor ausente
        ]);

        $response->assertSessionHasErrors(['idProveedor']);
    }

    #[Test]
    public function store_falla_si_todos_los_campos_estan_vacios(): void
    {
        $response = $this->post('/inventario/ingredientes/store', []);

        $response->assertSessionHasErrors([
            'idProveedor',
            'idCategoria',
            'idUnidadMedida',
            'nombreIngrediente',
            'referenciaIngrediente',
        ]);
    }

    #[Test]
    public function store_falla_si_referencia_supera_50_caracteres(): void
    {
        $response = $this->post('/inventario/ingredientes/store', [
            'idProveedor'           => 1,
            'idCategoria'           => 1,
            'idUnidadMedida'        => 1,
            'nombreIngrediente'     => 'Harina',
            'referenciaIngrediente' => str_repeat('X', 51), // 51 chars > max:50
        ]);

        $response->assertSessionHasErrors(['referenciaIngrediente']);
    }

    #[Test]
    public function store_redirige_con_error_cuando_spring_rechaza(): void
    {
        Http::fake([
            '*/crearingrediente' => Http::response('Error en Spring', 400)
        ]);

        $response = $this->post('/inventario/ingredientes/store', [
            'idProveedor'           => 1,
            'idCategoria'           => 1,
            'idUnidadMedida'        => 1,
            'nombreIngrediente'     => 'Harina',
            'referenciaIngrediente' => 'HAR-001',
        ]);

        $response->assertSessionHas('error');
        $response->assertRedirect(); // vuelve atrás
    }

    // =========================================================
    // update()
    // =========================================================

    #[Test]
    public function update_con_datos_validos_redirige_con_success(): void
    {
        Http::fake([
            '*/ingrediente/1' => Http::response('Actualizado', 200)
        ]);

        $response = $this->put('/inventario/ingredientes/update/1', [
            'idProveedor'           => 1,
            'idCategoria'           => 1,
            'idUnidadMedida'        => 1,
            'nombreIngrediente'     => 'Harina Premium',
            'referenciaIngrediente' => 'HAR-002',
        ]);

        $response->assertRedirect(route('ingredientes.index'));
        $response->assertSessionHas('success', 'Ingrediente actualizado con éxito.');
    }

    #[Test]
    public function update_falla_validacion_con_datos_vacios(): void
    {
        $response = $this->put('/inventario/ingredientes/update/1', []);

        $response->assertSessionHasErrors([
            'idProveedor',
            'idCategoria',
            'idUnidadMedida',
            'nombreIngrediente',
            'referenciaIngrediente',
        ]);
    }

    // =========================================================
    // destroy()
    // =========================================================

    #[Test]
    public function destroy_elimina_y_redirige_con_success(): void
    {
        Http::fake([
            '*/ingrediente/1' => Http::response('Eliminado', 200)
        ]);

        $response = $this->delete('/inventario/ingredientes/delete/1');

        $response->assertRedirect(route('ingredientes.index'));
        $response->assertSessionHas('success', 'Ingrediente eliminado con éxito.');
    }

    #[Test]
    public function destroy_redirige_con_error_cuando_spring_falla(): void
    {
        Http::fake([
            '*/ingrediente/1' => Http::response('Error', 500)
        ]);

        $response = $this->delete('/inventario/ingredientes/delete/1');

        $response->assertSessionHas('error');
    }

    // =========================================================
    // ingresarStock() — responde JSON
    // =========================================================

    #[Test]
    public function ingresar_stock_retorna_json_success_true(): void
    {
        Http::fake([
            '*/ingredientes/1/ingreso' => Http::response('Stock actualizado', 200)
        ]);

        $response = $this->post('/ingredientes/1/ingresar-stock', [
            'cantidadIngresada' => 10.5
        ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);
    }

    #[Test]
    public function ingresar_stock_falla_con_cantidad_cero(): void
    {
        $response = $this->post('/ingredientes/1/ingresar-stock', [
            'cantidadIngresada' => 0 // min:0.01
        ]);

        $response->assertSessionHasErrors(['cantidadIngresada']);
    }

    #[Test]
    public function ingresar_stock_retorna_json_error_cuando_spring_falla(): void
    {
        Http::fake([
            '*/ingredientes/1/ingreso' => Http::response('Error stock', 500)
        ]);

        $response = $this->post('/ingredientes/1/ingresar-stock', [
            'cantidadIngresada' => 10.0
        ]);

        $response->assertStatus(500);
        $response->assertJson(['success' => false]);
    }
}