<?php

namespace Tests\Unit\Services\Inventario;

use Tests\TestCase;
use App\Services\Inventario\ProduccionService;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ProduccionServiceTest extends TestCase
{
    protected $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new ProduccionService();
    }

    public function test_obtener_historial_exitoso()
    {
        Http::fake([
            '*/inventario/produccion' => Http::response([
                ['idProduccion' => 1, 'idProducto' => 1, 'cantidadProducida' => 50]
            ], 200)
        ]);

        $result = $this->service->obtenerHistorial();

        $this->assertTrue($result['success']);
        $this->assertCount(1, $result['data']);
    }

    public function test_obtener_historial_vacio_204_exitoso()
    {
        Http::fake([
            '*/inventario/produccion' => Http::response('', 204)
        ]);

        $result = $this->service->obtenerHistorial();

        $this->assertTrue($result['success']);
        $this->assertIsArray($result['data']);
        $this->assertCount(0, $result['data']);
    }

    public function test_registrar_produccion_exitoso()
    {
        Http::fake([
            '*/inventario/produccion' => Http::response([
                'idProduccion' => 10,
                'mensaje' => 'Registrado correctamente'
            ], 201)
        ]);

        $result = $this->service->registrarProduccion([
            'idProducto' => 1,
            'cantidadProducida' => 15.5
        ]);

        $this->assertTrue($result['success']);
        $this->assertEquals(10, $result['response']['idProduccion']);
    }

    public function test_registrar_produccion_falla_con_error_json()
    {
        Http::fake([
            '*/inventario/produccion' => Http::response(['error' => 'Ingredientes insuficientes'], 400)
        ]);

        $result = $this->service->registrarProduccion([
            'idProducto' => 1,
            'cantidadProducida' => 1000
        ]);

        $this->assertFalse($result['success']);
        $this->assertEquals('Ingredientes insuficientes', $result['error']);
    }

    public function test_eliminar_produccion_exitoso()
    {
        Http::fake([
            '*/inventario/produccion/1' => Http::response('', 204)
        ]);

        $result = $this->service->eliminarProduccion(1);

        $this->assertTrue($result['success']);
        $this->assertStringContainsString('revertidos con éxito', $result['response']);
    }

    public function test_eliminar_produccion_falla_con_json()
    {
        Http::fake([
            '*/inventario/produccion/99' => Http::response(['error' => 'Produccion no encontrada o ya finalizada'], 404)
        ]);

        $result = $this->service->eliminarProduccion(99);

        $this->assertFalse($result['success']);
        $this->assertEquals('Produccion no encontrada o ya finalizada', $result['error']);
    }

    public function test_obtener_recetas_exitoso()
    {
        Http::fake([
            '*/inventario/recetas/optimizadas' => Http::response([
                ['idProducto' => 1, 'nombreProducto' => 'Pan']
            ], 200)
        ]);

        $result = $this->service->obtenerRecetas();

        $this->assertTrue($result['success']);
        $this->assertCount(1, $result['data']);
        $this->assertEquals('Pan', $result['data'][0]['nombreProducto']);
    }
}
