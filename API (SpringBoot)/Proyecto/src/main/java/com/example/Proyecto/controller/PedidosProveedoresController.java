package com.example.Proyecto.controller;

import com.example.Proyecto.model.PedidosProveedores;
import com.example.Proyecto.service.PedidosProveedores.PedidosProveedoresService;
import io.swagger.v3.oas.annotations.Operation;
import io.swagger.v3.oas.annotations.Parameter;
import io.swagger.v3.oas.annotations.responses.ApiResponse;
import io.swagger.v3.oas.annotations.responses.ApiResponses;
import io.swagger.v3.oas.annotations.tags.Tag;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.http.HttpStatus;
import org.springframework.http.ResponseEntity;
import org.springframework.web.bind.annotation.*;
import java.util.List;

@RestController
@RequestMapping
@Tag(name = "Pedidos Proveedores", description = "Gestión de pedidos a proveedores")
public class PedidosProveedoresController {

    @Autowired
    private PedidosProveedoresService pedidosProveedoresService;

    @Operation(summary = "Obtener pedidos a proveedores", description = "Retorna todos los pedidos realizados a proveedores")
    @ApiResponse(responseCode = "200", description = "Lista obtenida exitosamente")
    @GetMapping("/pedido/proveedores")
    public List<PedidosProveedores> obtenerTodosLosPedidosProveedores() {
        return pedidosProveedoresService.obtenerTodosLosPedidosProveedores();
    }

    @Operation(summary = "Crear pedido a proveedor", description = "Crea un pedido completo con encabezado y detalles")
    @ApiResponses(value = {
        @ApiResponse(responseCode = "201", description = "Pedido creado exitosamente"),
        @ApiResponse(responseCode = "500", description = "Error al crear el pedido")
    })
    @PostMapping("/pedido/proveedores")
    public ResponseEntity<String> crearPedidoProveedor(
        @Parameter(description = "Datos del pedido con detalles", required = true)
        @RequestBody PedidosProveedores pedido) {
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

    @Operation(summary = "Obtener pedido con detalles", description = "Retorna un pedido específico con todos sus detalles")
    @ApiResponses(value = {
        @ApiResponse(responseCode = "200", description = "Pedido encontrado"),
        @ApiResponse(responseCode = "404", description = "Pedido no encontrado")
    })
    @GetMapping("/pedido/proveedores/{id}")
    public ResponseEntity<PedidosProveedores> obtenerPedidoConDetalles(
        @Parameter(description = "ID del pedido", required = true)
        @PathVariable int id) {
        PedidosProveedores pedido = pedidosProveedoresService.obtenerPedidoConDetalles(id);
        if (pedido != null) {
            return ResponseEntity.ok(pedido);
        } else {
            return ResponseEntity.notFound().build();
        }
    }

    @Operation(summary = "Actualizar pedido a proveedor", description = "Actualiza los datos de un pedido existente")
    @ApiResponses(value = {
        @ApiResponse(responseCode = "200", description = "Pedido actualizado"),
        @ApiResponse(responseCode = "404", description = "Pedido no encontrado")
    })
    @PutMapping("/pedido/proveedores/{id}")
    public ResponseEntity<String> editarPedidoProveedor(
        @Parameter(description = "ID del pedido", required = true)
        @PathVariable int id, 
        @Parameter(description = "Datos actualizados", required = true)
        @RequestBody PedidosProveedores pedido) {
        pedido.setIdPedidoProv(id);
        int filas = pedidosProveedoresService.editarPedidoProveedor(pedido);
        if (filas > 0) {
            return ResponseEntity.ok("Pedido de proveedor con ID " + id + " actualizado correctamente.");
        } else {
            return ResponseEntity.notFound().build();
        }
    }

    @Operation(summary = "Eliminar pedido a proveedor", description = "Elimina un pedido a proveedor")
    @ApiResponses(value = {
        @ApiResponse(responseCode = "200", description = "Pedido eliminado"),
        @ApiResponse(responseCode = "404", description = "Pedido no encontrado")
    })
    @DeleteMapping("/pedido/proveedores/{id}")
    public ResponseEntity<String> eliminarPedidoProveedor(
        @Parameter(description = "ID del pedido", required = true)
        @PathVariable int id) {
        // En un escenario real, deberías manejar la eliminación de los detalles aquí o mediante CASCADE en la BD.
        int filas = pedidosProveedoresService.eliminarPedidoProveedor(id);
        if (filas > 0) {
            return ResponseEntity.ok("Pedido de proveedor con ID " + id + " eliminado correctamente.");
        } else {
            return ResponseEntity.notFound().build();
        }


    }

    @Operation(summary = "Marcar pedido como entregado", description = "Actualiza el estado del pedido y suma las cantidades al inventario de ingredientes")
    @PatchMapping("/pedido/proveedores/{id}/entregar")
    public ResponseEntity<String> entregarPedido(@PathVariable int id) {
        try {
            pedidosProveedoresService.marcarComoEntregado(id);

            return ResponseEntity.ok("Pedido #" + id + " recibido. El inventario ha sido actualizado correctamente.");
        } catch (IllegalStateException e) {
            return ResponseEntity.status(HttpStatus.BAD_REQUEST).body(e.getMessage());
        } catch (Exception e) {
            return ResponseEntity.status(HttpStatus.INTERNAL_SERVER_ERROR)
                    .body("Error al procesar la entrega: " + e.getMessage());
        }
    }

}
