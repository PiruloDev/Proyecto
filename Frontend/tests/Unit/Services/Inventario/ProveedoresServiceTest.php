<?php

namespace Tests\Unit\Services\Inventario;

use Tests\TestCase;
use App\Services\Inventario\ProveedoresService;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ProveedoresServiceTest extends TestCase
{
    protected $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new ProveedoresService();
        // Evita que los logs ensucien la consola de pruebas
        Log::shouldReceive('info')->andReturnNull();
        Log::shouldReceive('error')->andReturnNull();
    }

    public function test_obtener_proveedores_exitoso()
    {
        Http::fake([
            '*/proveedores' => Http::response([
                ['idProveedor' => 1, 'nombreProv' => 'Test', 'activoProv' => true]
            ], 200)
        ]);

        $result = $this->service->obtenerProveedores();

        $this->assertTrue($result['success']);
        $this->assertIsArray($result['data']);
        $this->assertCount(1, $result['data']);
        $this->assertEquals('Test', $result['data'][0]['nombreProv']);
    }

    public function test_obtener_proveedores_fallido()
    {
        Http::fake([
            '*/proveedores' => Http::response('Error Servidor', 500)
        ]);

        $result = $this->service->obtenerProveedores();

        $this->assertFalse($result['success']);
        $this->assertStringContainsString('Error al obtener proveedores', $result['error']);
    }

    public function test_crear_proveedor_exitoso()
    {
        Http::fake([
            '*/proveedores' => Http::response('Creado correctamente', 201)
        ]);

        $payload = [
            'nombreProv' => 'Nuevo Prov',
            'telefonoProv' => '123456',
            'activoProv' => 'true',
            'emailProv' => 'prov@test.com',
            'direccionProv' => '' // Debe ser convertido a null por el filtro
        ];

        $result = $this->service->crearProveedor($payload);

        $this->assertTrue($result['success']);
        $this->assertEquals('Creado correctamente', $result['response']);
    }

    public function test_crear_proveedor_con_excepcion()
    {
        // Fuerza una excepción en cliente Http sin mockear correctamente
        // En Laravel 10+, Http::fake permite arrojar excepciones
        Http::fake(function () {
            throw new \Exception('Connection timeout');
        });

        $result = $this->service->crearProveedor(['nombreProv' => 'Err']);

        $this->assertFalse($result['success']);
        $this->assertStringContainsString('Fallo de conexión', $result['error']);
    }

    public function test_actualizar_proveedor_exitoso()
    {
        Http::fake([
            '*/proveedores/1' => Http::response('Actualizado', 200)
        ]);

        $payload = ['nombreProv' => 'Modificado'];

        $result = $this->service->actualizarProveedor(1, $payload);

        $this->assertTrue($result['success']);
        $this->assertEquals('Actualizado', $result['response']);
    }

    public function test_eliminar_proveedor_exitoso()
    {
        Http::fake([
            '*/proveedores/99' => Http::response('', 204)
        ]);

        $result = $this->service->eliminarProveedor(99);

        $this->assertTrue($result['success']);
    }
}
