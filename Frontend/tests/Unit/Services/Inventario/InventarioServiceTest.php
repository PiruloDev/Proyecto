<?php

namespace Tests\Unit\Services\Inventario;

use Tests\TestCase;
use Illuminate\Support\Facades\Http;
use App\Services\Inventario\InventarioService;

class InventarioServiceTest extends TestCase
{
    private InventarioService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new InventarioService();
    }

    // ===========================================
    // obtenerIngredientes()
    // ===========================================

    /** @test */
    public function obtener_ingredientes_retorna_lista_cuando_api_responde_exitosamente(): void
    {
        Http::fake([
            '*/ingredientes/lista' => Http::response([
                ['idIngrediente' => 1, 'nombreIngrediente' => 'Harina', 'cantidadIngrediente' => 50],
                ['idIngrediente' => 2, 'nombreIngrediente' => 'Azúcar', 'cantidadIngrediente' => 30],
            ], 200),
        ]);

        $resultado = $this->service->obtenerIngredientes();

        $this->assertCount(2, $resultado);
        $this->assertEquals('Harina', $resultado[0]['nombreIngrediente']);
    }

    /** @test */
    public function obtener_ingredientes_retorna_array_vacio_cuando_api_falla(): void
    {
        Http::fake([
            '*/ingredientes/lista' => Http::response([], 500),
        ]);

        $resultado = $this->service->obtenerIngredientes();

        $this->assertIsArray($resultado);
        $this->assertEmpty($resultado);
    }

    // ===========================================
    // agregarIngredientes()
    // ===========================================

    /** @test */
    public function agregar_ingrediente_retorna_success_true_cuando_api_responde_200(): void
    {
        Http::fake([
            '*/crearingrediente' => Http::response('Ingrediente creado exitosamente', 200),
        ]);

        $data = [
            'idProveedor'           => 1,
            'idCategoria'           => 2,
            'nombreIngrediente'     => 'Levadura',
            'cantidadIngrediente'   => 10,
            'referenciaIngrediente' => 'LEV-001',
        ];

        $resultado = $this->service->agregarIngredientes($data);

        $this->assertTrue($resultado['success']);
    }

    /** @test */
    public function agregar_ingrediente_retorna_success_false_cuando_api_falla(): void
    {
        Http::fake([
            '*/crearingrediente' => Http::response('Error al crear', 400),
        ]);

        $resultado = $this->service->agregarIngredientes(['nombreIngrediente' => 'Sal']);

        $this->assertFalse($resultado['success']);
        $this->assertArrayHasKey('error', $resultado);
    }

    // ===========================================
    // actualizarIngrediente()
    // ===========================================

    /** @test */
    public function actualizar_ingrediente_retorna_success_true_cuando_api_responde_exitosamente(): void
    {
        Http::fake([
            '*/ingrediente/1' => Http::response('Actualizado', 200),
        ]);

        $resultado = $this->service->actualizarIngrediente(1, [
            'nombreIngrediente'   => 'Harina Integral',
            'cantidadIngrediente' => 25,
        ]);

        $this->assertTrue($resultado['success']);
    }

    /** @test */
    public function actualizar_ingrediente_retorna_success_false_cuando_api_falla(): void
    {
        Http::fake([
            '*/ingrediente/99' => Http::response('No encontrado', 404),
        ]);

        $resultado = $this->service->actualizarIngrediente(99, ['nombreIngrediente' => 'X']);

        $this->assertFalse($resultado['success']);
        $this->assertArrayHasKey('error', $resultado);
    }

    // ===========================================
    // actualizarCantidadIngrediente()
    // ===========================================

    /** @test */
    public function actualizar_cantidad_retorna_success_true_cuando_api_responde_exitosamente(): void
    {
        Http::fake([
            '*/1/cantidad' => Http::response('Cantidad actualizada', 200),
        ]);

        $resultado = $this->service->actualizarCantidadIngrediente(1, [
            'cantidadIngrediente' => 100,
        ]);

        $this->assertTrue($resultado['success']);
    }

    /** @test */
    public function actualizar_cantidad_retorna_success_false_cuando_api_falla(): void
    {
        Http::fake([
            '*/5/cantidad' => Http::response('Error', 500),
        ]);

        $resultado = $this->service->actualizarCantidadIngrediente(5, [
            'cantidadIngrediente' => 50,
        ]);

        $this->assertFalse($resultado['success']);
        $this->assertArrayHasKey('error', $resultado);
    }

    // ===========================================
    // eliminarIngrediente()
    // ===========================================

    /** @test */
    public function eliminar_ingrediente_retorna_success_true_cuando_api_responde_exitosamente(): void
    {
        Http::fake([
            '*/ingrediente/3' => Http::response('Eliminado', 200),
        ]);

        $resultado = $this->service->eliminarIngrediente(3);

        $this->assertTrue($resultado['success']);
    }

    /** @test */
    public function eliminar_ingrediente_retorna_success_false_cuando_api_falla(): void
    {
        Http::fake([
            '*/ingrediente/99' => Http::response('No encontrado', 404),
        ]);

        $resultado = $this->service->eliminarIngrediente(99);

        $this->assertFalse($resultado['success']);
        $this->assertArrayHasKey('error', $resultado);
    }
}