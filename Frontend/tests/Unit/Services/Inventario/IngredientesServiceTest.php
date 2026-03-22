<?php

namespace Tests\Unit\Services\Inventario;

use Tests\TestCase;
use Illuminate\Support\Facades\Http;
use PHPUnit\Framework\Attributes\Test;
use App\Services\Inventario\IngredientesService;

class IngredientesServiceTest extends TestCase
{
    protected IngredientesService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new IngredientesService();
    }

    // =========================================================
    // obtenerIngredientes()
    // =========================================================

    #[Test]
    public function obtener_ingredientes_retorna_success_true_con_data(): void
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
            ], 200)
        ]);

        $resultado = $this->service->obtenerIngredientes();

        $this->assertTrue($resultado['success']);
        $this->assertIsArray($resultado['data']);
        $this->assertCount(1, $resultado['data']);
        $this->assertEquals('Harina', $resultado['data'][0]['nombreIngrediente']);
    }

    #[Test]
    public function obtener_ingredientes_retorna_success_false_cuando_api_falla(): void
    {
        Http::fake([
            '*/ingredientes/lista' => Http::response([], 500)
        ]);

        $resultado = $this->service->obtenerIngredientes();

        $this->assertFalse($resultado['success']);
        $this->assertArrayHasKey('error', $resultado);
    }

    #[Test]
    public function obtener_ingredientes_retorna_error_cuando_no_hay_conexion(): void
    {
        Http::fake([
            '*/ingredientes/lista' => function() {
                throw new \Illuminate\Http\Client\ConnectionException('timeout');
            }
        ]);

        $resultado = $this->service->obtenerIngredientes();

        $this->assertFalse($resultado['success']);
        $this->assertArrayHasKey('error', $resultado);
    }

    // =========================================================
    // obtenerIngredientesSimple()
    // =========================================================

    #[Test]
    public function obtener_ingredientes_simple_retorna_success_true(): void
    {
        Http::fake([
            '*/ingredientes/cantidad' => Http::response([
                ['idIngrediente' => 1, 'nombreIngrediente' => 'Harina', 'cantidadIngrediente' => 50.0]
            ], 200)
        ]);

        $resultado = $this->service->obtenerIngredientesSimple();

        $this->assertTrue($resultado['success']);
        $this->assertIsArray($resultado['data']);
    }

    #[Test]
    public function obtener_ingredientes_simple_retorna_error_con_status_code(): void
    {
        Http::fake([
            '*/ingredientes/cantidad' => Http::response([], 503)
        ]);

        $resultado = $this->service->obtenerIngredientesSimple();

        $this->assertFalse($resultado['success']);
        // Tu service concatena el status en el mensaje
        $this->assertStringContainsString('503', $resultado['error']);
    }

    // =========================================================
    // agregarIngredientes()
    // =========================================================

    #[Test]
    public function agregar_ingrediente_retorna_success_true_con_datos_validos(): void
    {
        Http::fake([
            '*/crearingrediente' => Http::response('Ingrediente Harina creado con éxito.', 200)
        ]);

        $resultado = $this->service->agregarIngredientes([
            'idProveedor'           => 1,
            'idCategoria'           => 1,
            'idUnidadMedida'        => 2,
            'nombreIngrediente'     => 'Harina',
            'referenciaIngrediente' => 'HAR-001',
        ]);

        $this->assertTrue($resultado['success']);
        $this->assertArrayHasKey('response', $resultado);
    }

    #[Test]
    public function agregar_ingrediente_filtra_campos_no_permitidos(): void
    {
        Http::fake([
            '*/crearingrediente' => Http::response('OK', 200)
        ]);

        $this->service->agregarIngredientes([
            'idProveedor'           => 1,
            'idCategoria'           => 1,
            'idUnidadMedida'        => 2,
            'nombreIngrediente'     => 'Harina',
            'referenciaIngrediente' => 'HAR-001',
            'campoExtraHack'        => 'malicioso', // debe ser filtrado
            'cantidadIngrediente'   => 999,          // no debe enviarse
        ]);

        Http::assertSent(function ($request) {
            $body = $request->data();
            // Verifica que el campo malicioso NO llegó a Spring Boot
            return !isset($body['campoExtraHack'])
                && !isset($body['cantidadIngrediente'])
                && isset($body['nombreIngrediente']);
        });
    }

    #[Test]
    public function agregar_ingrediente_convierte_ids_a_enteros(): void
    {
        Http::fake([
            '*/crearingrediente' => Http::response('OK', 200)
        ]);

        $this->service->agregarIngredientes([
            'idProveedor'           => '1',  // string como vendría de un form
            'idCategoria'           => '2',
            'idUnidadMedida'        => '3',
            'nombreIngrediente'     => 'Azúcar',
            'referenciaIngrediente' => 'AZU-001',
        ]);

        Http::assertSent(function ($request) {
            $body = $request->data();
            // Verifica que llegan como int, no como string
            return is_int($body['idProveedor'])
                && is_int($body['idCategoria'])
                && is_int($body['idUnidadMedida']);
        });
    }

    #[Test]
    public function agregar_ingrediente_retorna_error_cuando_spring_rechaza(): void
    {
        Http::fake([
            '*/crearingrediente' => Http::response('Error de validación en Spring', 400)
        ]);

        $resultado = $this->service->agregarIngredientes([
            'idProveedor'           => 1,
            'idCategoria'           => 1,
            'idUnidadMedida'        => 1,
            'nombreIngrediente'     => 'Harina',
            'referenciaIngrediente' => 'HAR-001',
        ]);

        $this->assertFalse($resultado['success']);
        // Tu service devuelve el body como error
        $this->assertStringContainsString('Error de validación', $resultado['error']);
    }

    #[Test]
    public function agregar_ingrediente_retorna_error_controlado_sin_conexion(): void
    {
        Http::fake([
            '*/crearingrediente' => function() {
                throw new \Illuminate\Http\Client\ConnectionException('No route to host');
            }
        ]);

        $resultado = $this->service->agregarIngredientes([
            'idProveedor'           => 1,
            'idCategoria'           => 1,
            'idUnidadMedida'        => 1,
            'nombreIngrediente'     => 'Harina',
            'referenciaIngrediente' => 'HAR-001',
        ]);

        $this->assertFalse($resultado['success']);
        // Tu service devuelve este mensaje exacto en el catch
        $this->assertEquals('Error de conexión con el servidor backend', $resultado['error']);
    }

    // =========================================================
    // actualizarIngrediente()
    // =========================================================

    #[Test]
    public function actualizar_ingrediente_retorna_success_true(): void
    {
        Http::fake([
            '*/ingrediente/1' => Http::response('Ingrediente actualizado', 200)
        ]);

        $resultado = $this->service->actualizarIngrediente(1, [
            'idProveedor'           => 1,
            'idCategoria'           => 1,
            'idUnidadMedida'        => 1,
            'nombreIngrediente'     => 'Harina Premium',
            'referenciaIngrediente' => 'HAR-002',
        ]);

        $this->assertTrue($resultado['success']);
    }

    #[Test]
    public function actualizar_ingrediente_retorna_error_cuando_no_existe(): void
    {
        Http::fake([
            '*/ingrediente/999' => Http::response('Not found', 404)
        ]);

        $resultado = $this->service->actualizarIngrediente(999, [
            'idProveedor'           => 1,
            'idCategoria'           => 1,
            'idUnidadMedida'        => 1,
            'nombreIngrediente'     => 'X',
            'referenciaIngrediente' => 'X-001',
        ]);

        $this->assertFalse($resultado['success']);
        $this->assertEquals('No se pudo actualizar', $resultado['error']);
    }

    // =========================================================
    // eliminarIngrediente()
    // =========================================================

    #[Test]
    public function eliminar_ingrediente_retorna_success_true(): void
    {
        Http::fake([
            '*/ingrediente/1' => Http::response('Eliminado', 200)
        ]);

        $resultado = $this->service->eliminarIngrediente(1);

        $this->assertTrue($resultado['success']);
    }

    #[Test]
    public function eliminar_ingrediente_retorna_error_cuando_falla(): void
    {
        Http::fake([
            '*/ingrediente/1' => Http::response('Error', 500)
        ]);

        $resultado = $this->service->eliminarIngrediente(1);

        $this->assertFalse($resultado['success']);
        $this->assertEquals('Error al eliminar', $resultado['error']);
    }

    // =========================================================
    // ingresarStock()
    // =========================================================

    #[Test]
    public function ingresar_stock_retorna_success_true(): void
    {
        Http::fake([
            '*/ingredientes/1/ingreso' => Http::response('Stock actualizado', 200)
        ]);

        $resultado = $this->service->ingresarStock(1, ['cantidadIngresada' => 25.5]);

        $this->assertTrue($resultado['success']);
    }

    #[Test]
    public function ingresar_stock_convierte_cantidad_a_float(): void
    {
        Http::fake([
            '*/ingredientes/1/ingreso' => Http::response('OK', 200)
        ]);

        $this->service->ingresarStock(1, ['cantidadIngresada' => '10']);

        Http::assertSent(function ($request) {
            // Verifica que llega como float a Spring Boot
            return is_float($request->data()['cantidadIngresada']);
        });
    }

    #[Test]
    public function ingresar_stock_retorna_error_cuando_spring_rechaza(): void
    {
        Http::fake([
            '*/ingredientes/1/ingreso' => Http::response('Cantidad inválida', 400)
        ]);

        $resultado = $this->service->ingresarStock(1, ['cantidadIngresada' => -5.0]);

        $this->assertFalse($resultado['success']);
    }
}