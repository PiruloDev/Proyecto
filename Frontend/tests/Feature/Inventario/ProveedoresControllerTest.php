<?php

namespace Tests\Feature\Inventario;

use Tests\TestCase;
use Illuminate\Support\Facades\Http;
use PHPUnit\Framework\Attributes\Test;

class ProveedoresControllerTest extends TestCase
{
    // ===========================================
    // GET /inventario/proveedores -> index
    // ===========================================

    #[Test]
    public function index_retorna_200_con_vista_correcta_y_datos(): void
    {
        Http::fake([
            '*/proveedores' => Http::response([
                [
                    'idProveedor' => 1,
                    'nombreProv' => 'Harina Dorada',
                    'telefonoProv' => '12345678',
                    'activoProv' => true,
                    'emailProv' => 'contacto@harinadorada.com',
                    'direccionProv' => 'Calle Falsa 123'
                ]
            ], 200)
        ]);

        $response = $this->get(route('proveedores.index'));

        $response->assertStatus(200);
        $response->assertViewIs('inventarioviews.proveedores.index');
        $response->assertViewHas('proveedores');
    }

    #[Test]
    public function index_carga_vista_con_array_vacio_si_api_falla(): void
    {
        Http::fake([
            '*/proveedores' => Http::response('Error interno', 500)
        ]);

        $response = $this->get(route('proveedores.index'));

        $response->assertStatus(200);
        $response->assertViewIs('inventarioviews.proveedores.index');
        $response->assertViewHas('proveedores', []);
        $response->assertViewHas('error');
    }

    // ===========================================
    // GET /inventario/proveedores/create -> create
    // El view inventarioviews.proveedores.create aún no existe, omitimos este test
    // ===========================================

    // ===========================================
    // POST /inventario/proveedores/store -> store
    // ===========================================

    #[Test]
    public function store_con_datos_validos_redirige_con_success(): void
    {
        Http::fake([
            '*/proveedores' => Http::response('Creado con éxito', 200)
        ]);

        $response = $this->post(route('proveedores.store'), [
            'nombreProv' => 'Proveedor Test',
            'telefonoProv' => '987654321',
            'activoProv' => 'true',
            'emailProv' => 'test@proveedor.com',
            'direccionProv' => 'Avenida Principal 456'
        ]);

        $response->assertRedirect(route('proveedores.index'));
        $response->assertSessionHas('success', 'Proveedor creado con éxito.');
    }

    #[Test]
    public function store_falla_validacion_si_faltan_campos_requeridos(): void
    {
        $response = $this->post(route('proveedores.store'), []);

        $response->assertSessionHasErrors([
            'nombreProv',
            'telefonoProv',
            'activoProv',
            'emailProv'
        ]);
        // direccionProv es nullable, no debe tener error si falta (a menos que no envíe nada y falle otros, pero aquí no)
    }

    #[Test]
    public function store_redirige_con_error_cuando_api_rechaza(): void
    {
        Http::fake([
            '*/proveedores' => Http::response('Error en Spring', 400)
        ]);

        $response = $this->post(route('proveedores.store'), [
            'nombreProv' => 'Proveedor Test',
            'telefonoProv' => '987654321',
            'activoProv' => 'true',
            'emailProv' => 'test@proveedor.com',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('error');
    }

    // ===========================================
    // PUT /inventario/proveedores/update/{id} -> update
    // ===========================================

    #[Test]
    public function update_con_datos_validos_redirige_con_success(): void
    {
        Http::fake([
            '*/proveedores/1' => Http::response('Actualizado', 200)
        ]);

        $response = $this->put(route('proveedores.update', ['id' => 1]), [
            'nombreProv' => 'Proveedor Actualizado',
            'telefonoProv' => '111222333',
            'activoProv' => 'false',
            'emailProv' => 'actualizado@proveedor.com',
        ]);

        $response->assertRedirect(route('proveedores.index'));
        $response->assertSessionHas('success', 'Proveedor actualizado con éxito.');
    }

    #[Test]
    public function update_falla_validacion_con_datos_invalidos(): void
    {
        $response = $this->put(route('proveedores.update', ['id' => 1]), [
            'emailProv' => 'no-es-un-email-valido'
        ]);

        $response->assertSessionHasErrors([
            'nombreProv',
            'telefonoProv',
            'activoProv',
            'emailProv'
        ]);
    }

    // ===========================================
    // DELETE /inventario/proveedores/delete/{id} -> destroy
    // ===========================================

    #[Test]
    public function destroy_elimina_y_redirige_con_success(): void
    {
        Http::fake([
            '*/proveedores/1' => Http::response('Eliminado', 200)
        ]);

        $response = $this->delete(route('proveedores.destroy', ['id' => 1]));

        $response->assertRedirect(route('proveedores.index'));
        $response->assertSessionHas('success', 'Proveedor eliminado con éxito.');
    }

    #[Test]
    public function destroy_redirige_con_error_cuando_api_falla(): void
    {
        Http::fake([
            '*/proveedores/1' => Http::response('Error de API', 500)
        ]);

        $response = $this->delete(route('proveedores.destroy', ['id' => 1]));

        $response->assertRedirect();
        $response->assertSessionHas('error');
    }
}
