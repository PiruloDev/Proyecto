<?php

namespace Tests\Feature\Pedidos;

use Tests\TestCase;
use Illuminate\Support\Facades\Http;
use PHPUnit\Framework\Attributes\Test;

class CarritoControllerTest extends TestCase
{
    // =========================================================
    // Helpers
    // =========================================================

    /**
     * Simula un producto válido en el API de productos.
     * Copia la estructura de claves que usa CarritoController::agregar().
     */
    private function mockProductosApi(array $override = []): void
    {
        $producto = array_merge([
            'Id Producto:'    => 1,
            'Nombre Producto:' => 'Pan Artesanal',
            'Descripcion:'    => 'Pan de masa madre',
            'Nombre Categoria:' => 'Panadería',
            'Stock:'          => 50,
            'Precio:'         => '5.500',
        ], $override);

        Http::fake([
            '*/productos*' => Http::response([$producto], 200),
        ]);
    }

    /**
     * Precarga el carrito en sesión para los tests que lo necesitan.
     */
    private function withCarrito(array $items = []): static
    {
        $carrito = $items ?: [
            1 => [
                'id'          => 1,
                'nombre'      => 'Pan Artesanal',
                'descripcion' => 'Pan de masa madre',
                'imagen'      => 'pan.jpg',
                'precio'      => 5500,
                'cantidad'    => 2,
                'stock'       => 50,
            ],
        ];

        return $this->withSession(['carrito' => $carrito]);
    }

    // =========================================================
    // index()  —  GET /carrito
    // =========================================================

    #[Test]
    public function index_retorna_200_con_vista_del_carrito(): void
    {
        $response = $this->get('/carrito');

        $response->assertStatus(200);
        $response->assertViewIs('cart.cart');
    }

    #[Test]
    public function index_pasa_cartItems_y_subtotal_a_la_vista(): void
    {
        $response = $this->get('/carrito');

        $response->assertViewHas('cartItems');
        $response->assertViewHas('subtotal');
    }

    #[Test]
    public function index_muestra_carrito_vacio_cuando_no_hay_sesion(): void
    {
        $response = $this->get('/carrito');

        $response->assertStatus(200);
        $response->assertViewHas('subtotal', 0.0);
    }

    #[Test]
    public function index_calcula_subtotal_correctamente_con_items_en_sesion(): void
    {
        // precio 5500 × cantidad 2 = 11000
        $response = $this->withCarrito()->get('/carrito');

        $response->assertStatus(200);
        $response->assertViewHas('subtotal', 11000.0);
    }

    // =========================================================
    // obtenerCarrito()  —  GET /api/carrito
    // =========================================================

    #[Test]
    public function obtener_carrito_retorna_json_con_estructura_correcta(): void
    {
        $response = $this->withCarrito()->getJson('/api/carrito');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'items',
            'total',
            'count',
            'success',
        ]);
        $response->assertJson(['success' => true]);
    }

    #[Test]
    public function obtener_carrito_retorna_count_cero_cuando_esta_vacio(): void
    {
        $response = $this->getJson('/api/carrito');

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'count'   => 0,
        ]);
    }

    #[Test]
    public function obtener_carrito_retorna_items_formateados(): void
    {
        $response = $this->withCarrito()->getJson('/api/carrito');

        $response->assertStatus(200);
        $response->assertJsonCount(1, 'items');

        $item = $response->json('items.0');
        $this->assertArrayHasKey('id', $item);
        $this->assertArrayHasKey('producto', $item);
        $this->assertArrayHasKey('precio', $item);
        $this->assertArrayHasKey('cantidad', $item);
        $this->assertArrayHasKey('subtotal', $item);
    }

    // =========================================================
    // agregar()  —  POST /api/carrito/agregar/{id}
    // =========================================================

    #[Test]
    public function agregar_retorna_json_success_true_con_producto_valido(): void
    {
        $this->mockProductosApi();

        $response = $this->postJson('/api/carrito/agregar/1');

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);
    }

    #[Test]
    public function agregar_incrementa_cantidad_si_producto_ya_existe_en_carrito(): void
    {
        $this->mockProductosApi();

        // Primera adición
        $this->withCarrito()->postJson('/api/carrito/agregar/1');

        // Segunda adición — el controlador lee de sesión, no hace un nuevo mock
        $this->mockProductosApi();
        $response = $this->withCarrito()->postJson('/api/carrito/agregar/1');

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);
    }

    #[Test]
    public function agregar_retorna_503_cuando_api_de_productos_falla(): void
    {
        Http::fake([
            '*/productos*' => Http::response([], 503),
        ]);

        // Forzamos que el servicio lance excepción simulando timeout/error
        $response = $this->postJson('/api/carrito/agregar/1');

        // El controller captura Exception y devuelve 503
        $response->assertStatus(503);
        $response->assertJson(['success' => false]);
    }

    #[Test]
    public function agregar_retorna_404_cuando_id_no_existe_en_la_api(): void
    {
        // La API responde OK pero sin el producto solicitado (ID 999)
        Http::fake([
            '*/productos*' => Http::response([
                [
                    'Id Producto:'     => 1,
                    'Nombre Producto:' => 'Otro producto',
                    'Precio:'         => '3.000',
                    'Stock:'          => 10,
                ],
            ], 200),
        ]);

        $response = $this->postJson('/api/carrito/agregar/999');

        $response->assertStatus(404);
        $response->assertJson(['success' => false]);
    }

    // =========================================================
    // actualizar()  —  PATCH /api/carrito/actualizar
    // =========================================================

    #[Test]
    public function actualizar_cambia_la_cantidad_de_un_item_existente(): void
    {
        $response = $this->withCarrito()
            ->patchJson('/api/carrito/actualizar', [
                'id'       => 1,
                'cantidad' => 5,
            ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);
    }

    #[Test]
    public function actualizar_retorna_404_si_id_no_existe_en_carrito(): void
    {
        $response = $this->withCarrito()
            ->patchJson('/api/carrito/actualizar', [
                'id'       => 99,   // no está en el carrito
                'cantidad' => 3,
            ]);

        $response->assertStatus(404);
        $response->assertJson(['success' => false]);
    }

    #[Test]
    public function actualizar_con_carrito_vacio_retorna_404(): void
    {
        $response = $this->patchJson('/api/carrito/actualizar', [
            'id'       => 1,
            'cantidad' => 2,
        ]);

        $response->assertStatus(404);
        $response->assertJson(['success' => false]);
    }

    // =========================================================
    // remover()  —  DELETE /api/carrito/remover/{id}
    // =========================================================

    #[Test]
    public function remover_elimina_item_existente_del_carrito(): void
    {
        $response = $this->withCarrito()
            ->deleteJson('/api/carrito/remover/1');

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);
    }

    #[Test]
    public function remover_retorna_404_si_producto_no_estaba_en_carrito(): void
    {
        $response = $this->withCarrito()
            ->deleteJson('/api/carrito/remover/99');

        $response->assertStatus(404);
        $response->assertJson(['success' => false]);
    }

    #[Test]
    public function remover_con_carrito_vacio_retorna_404(): void
    {
        $response = $this->deleteJson('/api/carrito/remover/1');

        $response->assertStatus(404);
        $response->assertJson(['success' => false]);
    }

    // =========================================================
    // vaciar()  —  DELETE /api/carrito/vaciar
    // =========================================================

    #[Test]
    public function vaciar_limpia_el_carrito_y_retorna_success(): void
    {
        $response = $this->withCarrito()
            ->deleteJson('/api/carrito/vaciar');

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);
    }

    #[Test]
    public function vaciar_cuando_carrito_ya_esta_vacio_retorna_success(): void
    {
        // No debe explotar aunque no haya nada que vaciar
        $response = $this->deleteJson('/api/carrito/vaciar');

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);
    }

    // =========================================================
    // checkout()  —  POST /api/carrito/checkout
    // =========================================================

    #[Test]
    public function checkout_crea_pedido_y_limpia_carrito(): void
    {
        Http::fake([
            '*/pedidos*' => Http::response(['idPedido' => 42], 200),
        ]);

        $response = $this->withCarrito()
            ->withSession(['usuario' => ['id' => 7]])
            ->postJson('/api/carrito/checkout');

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);
    }

    #[Test]
    public function checkout_retorna_400_si_carrito_esta_vacio(): void
    {
        $response = $this->withSession(['usuario' => ['id' => 7]])
            ->postJson('/api/carrito/checkout');

        $response->assertStatus(400);
        $response->assertJson(['success' => false]);
    }

    #[Test]
    public function checkout_retorna_401_si_no_hay_sesion_de_usuario(): void
    {
        $response = $this->withCarrito()
            ->postJson('/api/carrito/checkout');

        $response->assertStatus(401);
        $response->assertJson(['success' => false]);
    }

    #[Test]
    public function checkout_retorna_500_cuando_api_de_pedidos_falla(): void
    {
        Http::fake([
            '*/pedidos*' => Http::response('Error', 500),
        ]);

        $response = $this->withCarrito()
            ->withSession(['usuario' => ['id' => 7]])
            ->postJson('/api/carrito/checkout');

        $response->assertStatus(500);
        $response->assertJson(['success' => false]);
    }
   
}