<?php

namespace Tests\Unit\Services\Inventario;

use Tests\TestCase;
use Illuminate\Support\Facades\Http;
use PHPUnit\Framework\Attributes\Test;
use App\Services\Inventario\CategoriaIngredientesService;

class CategoriaIngredientesServiceTest extends TestCase
{
    protected CategoriaIngredientesService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new CategoriaIngredientesService();
    }

    // =========================================================
    // obtenerCategoriasIngredientes()
    // =========================================================

    #[Test]
    public function obtener_categorias_retorna_success_true_con_data(): void
    {
        Http::fake([
            '*/categorias/ingredientes' => Http::response([
                ['idCategoriaIngrediente' => 1, 'nombreCategoria' => 'Harinas'],
                ['idCategoriaIngrediente' => 2, 'nombreCategoria' => 'Lácteos'],
            ], 200)
        ]);

        $resultado = $this->service->obtenerCategoriasIngredientes();

        $this->assertTrue($resultado['success']);
        $this->assertIsArray($resultado['data']);
        $this->assertCount(2, $resultado['data']);
        $this->assertEquals('Harinas', $resultado['data'][0]['nombreCategoria']);
    }

    #[Test]
    public function obtener_categorias_retorna_success_false_cuando_api_falla(): void
    {
        Http::fake([
            '*/categorias/ingredientes' => Http::response([], 500)
        ]);

        $resultado = $this->service->obtenerCategoriasIngredientes();

        $this->assertFalse($resultado['success']);
        $this->assertArrayHasKey('error', $resultado);
        $this->assertStringContainsString('500', $resultado['error']);
    }

    #[Test]
    public function obtener_categorias_retorna_error_cuando_no_hay_conexion(): void
    {
        Http::fake([
            '*/categorias/ingredientes' => function() {
                throw new \Illuminate\Http\Client\ConnectionException('timeout');
            }
        ]);

        $resultado = $this->service->obtenerCategoriasIngredientes();

        $this->assertFalse($resultado['success']);
        $this->assertStringContainsString('No se pudo conectar', $resultado['error']);
    }

    // =========================================================
    // crearCategoriaIngrediente()
    // =========================================================

    #[Test]
    public function crear_categoria_retorna_success_true(): void
    {
        Http::fake([
            '*/nuevacategoriaingrediente' => Http::response('Categoría Frutas creada con éxito.', 200)
        ]);

        $resultado = $this->service->crearCategoriaIngrediente([
            'nombreCategoria' => 'Frutas'
        ]);

        $this->assertTrue($resultado['success']);
        $this->assertArrayHasKey('response', $resultado);
    }

    #[Test]
    public function crear_categoria_envia_solo_nombreCategoria(): void
    {
        Http::fake([
            '*/nuevacategoriaingrediente' => Http::response('OK', 200)
        ]);

        $this->service->crearCategoriaIngrediente([
            'nombreCategoria' => 'Semillas',
            'campoExtra'      => 'no debe enviarse',
        ]);

        Http::assertSent(function ($request) {
            $body = $request->data();
            return isset($body['nombreCategoria'])
                && !isset($body['campoExtra'])
                && $body['nombreCategoria'] === 'Semillas';
        });
    }

    #[Test]
    public function crear_categoria_retorna_error_cuando_spring_rechaza(): void
    {
        Http::fake([
            '*/nuevacategoriaingrediente' => Http::response('Error de validación', 400)
        ]);

        $resultado = $this->service->crearCategoriaIngrediente([
            'nombreCategoria' => 'Frutas'
        ]);

        $this->assertFalse($resultado['success']);
        $this->assertArrayHasKey('error', $resultado);
    }

    #[Test]
    public function crear_categoria_retorna_error_controlado_sin_conexion(): void
    {
        Http::fake([
            '*/nuevacategoriaingrediente' => function() {
                throw new \Illuminate\Http\Client\ConnectionException('No route to host');
            }
        ]);

        $resultado = $this->service->crearCategoriaIngrediente([
            'nombreCategoria' => 'Frutas'
        ]);

        $this->assertFalse($resultado['success']);
        $this->assertStringContainsString('Fallo de conexión', $resultado['error']);
    }

    // =========================================================
    // actualizarCategoriaIngrediente()
    // =========================================================

    #[Test]
    public function actualizar_categoria_retorna_success_true(): void
    {
        Http::fake([
            '*/categoriaingrediente/1' => Http::response('Categoría actualizada', 200)
        ]);

        $resultado = $this->service->actualizarCategoriaIngrediente(1, [
            'nombreCategoria' => 'Harinas Premium'
        ]);

        $this->assertTrue($resultado['success']);
    }

    #[Test]
    public function actualizar_categoria_retorna_error_cuando_no_existe(): void
    {
        Http::fake([
            '*/categoriaingrediente/999' => Http::response('Not found', 404)
        ]);

        $resultado = $this->service->actualizarCategoriaIngrediente(999, [
            'nombreCategoria' => 'X'
        ]);

        $this->assertFalse($resultado['success']);
        $this->assertArrayHasKey('error', $resultado);
    }

    // =========================================================
    // eliminarCategoriaIngrediente()
    // =========================================================

    #[Test]
    public function eliminar_categoria_retorna_success_true(): void
    {
        Http::fake([
            '*/eliminarcategoria/1' => Http::response('Eliminada', 200)
        ]);

        $resultado = $this->service->eliminarCategoriaIngrediente(1);

        $this->assertTrue($resultado['success']);
    }

    #[Test]
    public function eliminar_categoria_retorna_error_cuando_falla(): void
    {
        Http::fake([
            '*/eliminarcategoria/1' => Http::response('Error', 500)
        ]);

        $resultado = $this->service->eliminarCategoriaIngrediente(1);

        $this->assertFalse($resultado['success']);
        $this->assertArrayHasKey('error', $resultado);
    }
}