<?php

namespace Tests\Unit\Services\Inventario;

use Tests\TestCase;
use App\Services\Inventario\RecetasService;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class RecetasServiceTest extends TestCase
{
    protected $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new RecetasService();
        Log::shouldReceive('info')->andReturnNull();
        Log::shouldReceive('error')->andReturnNull();
    }

    public function test_obtener_todas_las_recetas_agrupa_correctamente()
    {
        Http::fake([
            '*/inventario/recetas/optimizadas' => Http::response([
                [
                    'idProducto' => 1,
                    'nombreProducto' => 'Pan',
                    'idIngrediente' => 10,
                    'cantidadRequerida' => 100
                ],
                [
                    'idProducto' => 1,
                    'nombreProducto' => 'Pan',
                    'idIngrediente' => 20,
                    'cantidadRequerida' => 200
                ],
                [
                    'idProducto' => 2,
                    'nombreProducto' => 'Torta',
                    'idIngrediente' => 10,
                    'cantidadRequerida' => 500
                ]
            ], 200)
        ]);

        $result = $this->service->obtenerTodasLasRecetas();

        $this->assertTrue($result['success']);
        $this->assertCount(2, $result['data']); // 2 productos únicos
        $this->assertEquals(1, $result['data'][0]['idProducto']);
        $this->assertCount(2, $result['data'][0]['detalles']); // 2 ingredientes para pan
        $this->assertEquals(2, $result['data'][1]['idProducto']);
        $this->assertCount(1, $result['data'][1]['detalles']); // 1 para torta
    }

    public function test_obtener_todas_las_recetas_fallido()
    {
        Http::fake([
            '*/inventario/recetas/optimizadas' => Http::response('Server Error', 500)
        ]);

        $result = $this->service->obtenerTodasLasRecetas();

        $this->assertFalse($result['success']);
        $this->assertEquals('Error al obtener recetas del servidor', $result['error']);
    }

    public function test_obtener_receta_por_producto_exitoso()
    {
        Http::fake([
            '*/inventario/recetas/optimizadas/producto/1' => Http::response([
                ['idIngrediente' => 5, 'cantidadRequerida' => 10, 'idUnidad' => 1]
            ], 200)
        ]);

        $result = $this->service->obtenerRecetaPorIdProducto(1);

        $this->assertTrue($result['success']);
        $this->assertCount(1, $result['data']);
    }

    public function test_crear_receta_exitoso()
    {
        Http::fake([
            '*/inventario/recetas' => Http::response(['mensaje' => 'Receta creada con éxito'], 201)
        ]);

        $payload = [
            'idProducto' => 1,
            'detalles' => [
                ['idIngrediente' => 1, 'cantidadRequerida' => 10.5, 'idUnidad' => 1]
            ]
        ];

        $result = $this->service->crearReceta($payload);

        $this->assertTrue($result['success']);
        $this->assertEquals('Receta creada con éxito', $result['mensaje']);
    }

    public function test_actualizar_receta_exitoso()
    {
        Http::fake([
            '*/inventario/recetas/producto/1' => Http::response(['mensaje' => 'Modificada'], 200)
        ]);

        $payload = [
            'detalles' => [
                ['idIngrediente' => 2, 'cantidadRequerida' => 20, 'idUnidad' => 1]
            ]
        ];

        $result = $this->service->actualizarReceta(1, $payload);

        $this->assertTrue($result['success']);
        $this->assertEquals('Modificada', $result['mensaje']);
    }

    public function test_eliminar_receta_exitoso()
    {
        Http::fake([
            '*/inventario/recetas/1' => Http::response('', 204)
        ]);

        $result = $this->service->eliminarReceta(1);

        $this->assertTrue($result['success']);
    }
}
