<?php

namespace Tests\Feature\Inventario;

use Tests\TestCase;
use Illuminate\Support\Facades\Http;
use PHPUnit\Framework\Attributes\Test;

class PedidosProveedoresControllerTest extends TestCase
{
    // ===========================================
    // GET /inventario/pedidos-proveedores -> index
    // ===========================================

    #[Test]
    public function index_maneja_error_de_api_pedidos_correctamente(): void
    {
        Http::fake([
            '*/pedido/proveedores' => Http::response(['error' => 'API Error'], 500),
            '*/proveedores' => Http::response([], 200),
            '*/ingredientes/lista' => Http::response([], 200)
        ]);

        $response = $this->get(route('pedidoproveedores.index'));

        $response->assertStatus(200);
        $response->assertViewIs('inventarioviews.pedidoproveedores.index');
        $response->assertViewHas('pedidos', []);
        $response->assertViewHas('error');
    }

    // ===========================================
    // POST /inventario/pedidos-proveedores/store -> store
    // ===========================================

    #[Test]
    public function store_falla_validacion_si_faltan_campos_requeridos(): void
    {
        $response = $this->post(route('pedidoproveedores.store'), []);

        $response->assertSessionHasErrors([
            'idProveedor',
            'numeroPedido',
            'estadoPedido',
            'detalles'
        ]);
    }

    // ===========================================
    // GET /inventario/pedidos-proveedores/show/{id} -> show
    // ===========================================

    #[Test]
    public function show_redirige_si_falla_la_api(): void
    {
        Http::fake([
            '*/pedido/proveedores/99' => Http::response('Not Found', 404)
        ]);

        $response = $this->get(route('pedidoproveedores.show', ['id' => 99]));

        $response->assertRedirect(route('pedidoproveedores.index'));
        $response->assertSessionHas('error');
    }

    // ===========================================
    // PUT /inventario/pedidos-proveedores/update/{id} -> update
    // ===========================================

    #[Test]
    public function update_actualiza_encabezado_y_redirige_con_success(): void
    {
        Http::fake([
            '*/pedido/proveedores/1' => Http::response('Actualizado', 200)
        ]);

        $response = $this->put(route('pedidoproveedores.update', ['id' => 1]), [
            'idProveedor' => 2,
            'numeroPedido' => 1005,
            'estadoPedido' => 'Enviado'
        ]);

        $response->assertRedirect(route('pedidoproveedores.index'));
    }

    // ===========================================
    // DELETE /inventario/pedidos-proveedores/delete/{id} -> destroy
    // ===========================================

    #[Test]
    public function destroy_elimina_pedido_y_redirige_con_success(): void
    {
        Http::fake([
            '*/pedido/proveedores/1' => Http::response('Eliminado', 200)
        ]);

        $response = $this->delete(route('pedidoproveedores.destroy', ['id' => 1]));

        $response->assertRedirect(route('pedidoproveedores.index'));
    }

    // ===========================================
    // PATCH /inventario/pedidos-proveedores/{id}/entregar -> entregar
    // ===========================================

    #[Test]
    public function entregar_marca_pedido_como_entregado_y_redirige_con_success(): void
    {
        Http::fake([
            '*/pedido/proveedores/1/entregar' => Http::response('Entregado', 200)
        ]);

        $response = $this->patch(route('pedidoproveedores.entregar', ['id' => 1]));

        $response->assertRedirect(route('pedidoproveedores.index'));
        $response->assertSessionHas('success');
    }
}
