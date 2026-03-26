<?php

namespace Tests\Unit\Services\Inventario;

use Tests\TestCase;
use App\Services\Inventario\PedidosProveedoresService;
use Illuminate\Support\Facades\Http;

class PedidosProveedoresServiceTest extends TestCase
{
    protected $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new PedidosProveedoresService();
    }

    public function test_obtener_pedidos_exitoso()
    {
        Http::fake([
            '*/pedido/proveedores' => Http::response([
                ['idPedido' => 1, 'estado' => 'Pendiente']
            ], 200)
        ]);

        $result = $this->service->obtenerPedidos();

        $this->assertTrue($result['success']);
        $this->assertCount(1, $result['data']);
    }

    public function test_obtener_pedidos_fallido()
    {
        Http::fake([
            '*/pedido/proveedores' => Http::response(['error' => 'No autorizado'], 401)
        ]);

        $result = $this->service->obtenerPedidos();

        $this->assertFalse($result['success']);
        $this->assertEquals('No autorizado', $result['error']);
    }

    public function test_crear_pedido_completo_exitoso()
    {
        Http::fake([
            '*/pedido/proveedores' => Http::response('Pedido Registrado', 201)
        ]);

        $result = $this->service->crearPedidoCompleto([
            'idProveedor' => 1,
            'detalles' => []
        ]);

        $this->assertTrue($result['success']);
        $this->assertEquals('Pedido Registrado', $result['response']);
    }

    public function test_obtener_pedido_con_detalles_exitoso()
    {
        Http::fake([
            '*/pedido/proveedores/1' => Http::response([
                'idPedido' => 1, 'detalles' => []
            ], 200)
        ]);

        $result = $this->service->obtenerPedidoConDetalles(1);

        $this->assertTrue($result['success']);
        $this->assertEquals(1, $result['data']['idPedido']);
    }

    public function test_actualizar_pedido_exitoso()
    {
        Http::fake([
            '*/pedido/proveedores/1' => Http::response('Actualizado', 200)
        ]);

        $result = $this->service->actualizarPedido(1, ['estadoPedido' => 'Recibido']);

        $this->assertTrue($result['success']);
        $this->assertEquals('Actualizado', $result['response']);
    }

    public function test_eliminar_pedido_exitoso()
    {
        Http::fake([
            '*/pedido/proveedores/1' => Http::response('', 200)
        ]);

        $result = $this->service->eliminarPedido(1);

        $this->assertTrue($result['success']);
    }

    public function test_entregar_pedido_exitoso()
    {
        Http::fake([
            '*/pedido/proveedores/1/entregar' => Http::response('Inventario modificado', 200)
        ]);

        $result = $this->service->entregarPedido(1);

        $this->assertTrue($result['success']);
        $this->assertEquals('Inventario modificado', $result['response']);
    }

    public function test_entregar_pedido_conexion_excepcion()
    {
        Http::fake(function () {
            throw new \Exception('Network error');
        });

        $result = $this->service->entregarPedido(1);

        $this->assertFalse($result['success']);
        $this->assertStringContainsString('Fallo de conexión', $result['error']);
    }
}
