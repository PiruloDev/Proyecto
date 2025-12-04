package com.example.Proyecto.controller;

import com.example.Proyecto.model.PedidosProveedores;
import com.example.Proyecto.service.PedidosProveedores.PedidosProveedoresService;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.http.HttpStatus;
import org.springframework.http.ResponseEntity;
import org.springframework.web.bind.annotation.*;
import java.util.List;

@RestController
@RequestMapping
public class PedidosProveedoresController {

    @Autowired
    private PedidosProveedoresService pedidosProveedoresService;

    // GET - Obtener todos los pedidos de proveedores
    @GetMapping("/pedido/proveedores")
    public List<PedidosProveedores> obtenerTodosLosPedidosProveedores() {
        return pedidosProveedoresService.obtenerTodosLosPedidosProveedores();
    }

    /**
     * POST - Crea un pedido de proveedor completo (encabezado y detalles).
     * Este método llama al servicio transaccional.
     */
    @PostMapping("/pedido/proveedores")
    public ResponseEntity<String> crearPedidoProveedor(@RequestBody PedidosProveedores pedido) {
        try {
            // Llama al método transaccional que inserta el encabezado y los detalles
            int idPedidoProv = pedidosProveedoresService.crearPedidoProveedorCompleto(pedido);

            // Retorna el ID generado para confirmación con código 201 Created
            return ResponseEntity.status(HttpStatus.CREATED)
                    .body("Pedido completo (ID: " + idPedidoProv + ") creado con éxito. Número de pedido: " + pedido.getNumeroPedido());

        } catch (Exception e) {
            // Si hay un error SQL (como el error de la columna 'CANTIDAD' que corregimos)
            // se devuelve un código 500 y el mensaje de la excepción para diagnóstico.
            return ResponseEntity.status(HttpStatus.INTERNAL_SERVER_ERROR)
                    .body("Error al crear el pedido completo: " + e.getMessage());
        }
    }

    // GET - Obtener un pedido con todos sus detalles por ID
    @GetMapping("/pedido/proveedores/{id}")
    public ResponseEntity<PedidosProveedores> obtenerPedidoConDetalles(@PathVariable int id) {
        PedidosProveedores pedido = pedidosProveedoresService.obtenerPedidoConDetalles(id);
        if (pedido != null) {
            return ResponseEntity.ok(pedido);
        } else {
            return ResponseEntity.notFound().build();
        }
    }

    // PUT - Actualizar un pedido de proveedor existente
    @PutMapping("/pedido/proveedores/{id}")
    public ResponseEntity<String> editarPedidoProveedor(@PathVariable int id, @RequestBody PedidosProveedores pedido) {
        pedido.setIdPedidoProv(id);
        int filas = pedidosProveedoresService.editarPedidoProveedor(pedido);
        if (filas > 0) {
            return ResponseEntity.ok("Pedido de proveedor con ID " + id + " actualizado correctamente.");
        } else {
            return ResponseEntity.notFound().build();
        }
    }

    // DELETE - Eliminar un pedido de proveedor por ID
    @DeleteMapping("/pedido/proveedores/{id}")
    public ResponseEntity<String> eliminarPedidoProveedor(@PathVariable int id) {
        // En un escenario real, deberías manejar la eliminación de los detalles aquí o mediante CASCADE en la BD.
        int filas = pedidosProveedoresService.eliminarPedidoProveedor(id);
        if (filas > 0) {
            return ResponseEntity.ok("Pedido de proveedor con ID " + id + " eliminado correctamente.");
        } else {
            return ResponseEntity.notFound().build();
        }
    }
}