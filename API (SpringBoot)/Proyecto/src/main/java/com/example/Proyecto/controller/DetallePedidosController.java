package com.example.Proyecto.controller;

import com.example.Proyecto.service.DetallePedidos.DetallePedidos;
import com.example.Proyecto.service.DetallePedidos.DetallePedidosService;
import io.swagger.v3.oas.annotations.Operation;
import io.swagger.v3.oas.annotations.Parameter;
import io.swagger.v3.oas.annotations.responses.ApiResponse;
import io.swagger.v3.oas.annotations.responses.ApiResponses;
import io.swagger.v3.oas.annotations.tags.Tag;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.http.ResponseEntity;
import org.springframework.web.bind.annotation.*;
import java.util.List;

@RestController
@RequestMapping("/detalles-pedidos")
@Tag(name = "Detalle de Pedidos", description = "Gestión de detalles de pedidos")
public class DetallePedidosController {

    @Autowired
    private DetallePedidosService detallePedidosService;

    @Operation(summary = "Obtener todos los detalles de pedidos", description = "Retorna la lista completa de detalles de pedidos")
    @ApiResponse(responseCode = "200", description = "Lista obtenida exitosamente")
    @GetMapping
    public List<DetallePedidos> obtenerTodosLosDetallesPedidos() {
        return detallePedidosService.obtenerTodosLosDetallesPedidos();
    }

    @Operation(summary = "Crear detalle de pedido", description = "Registra un nuevo detalle de pedido")
    @ApiResponse(responseCode = "200", description = "Detalle creado exitosamente")
    @PostMapping
    public ResponseEntity<String> crearDetallePedido(
        @Parameter(description = "Datos del detalle", required = true)
        @RequestBody DetallePedidos detalle) {
        detallePedidosService.crearDetallePedido(detalle);
        return ResponseEntity.ok("Detalle de pedido " + detalle.getIdDetalle() + " creado con éxito.");
    }

    @Operation(summary = "Actualizar detalle de pedido", description = "Actualiza un detalle existente")
    @ApiResponses(value = {
        @ApiResponse(responseCode = "200", description = "Detalle actualizado"),
        @ApiResponse(responseCode = "404", description = "Detalle no encontrado")
    })
    @PutMapping("/{id}")
    public ResponseEntity<String> editarDetallePedido(
        @Parameter(description = "ID del detalle", required = true)
        @PathVariable int id, 
        @Parameter(description = "Datos actualizados", required = true)
        @RequestBody DetallePedidos detalle) {
        detalle.setIdDetalle(id);
        int filas = detallePedidosService.editarDetallePedido(detalle);
        if (filas > 0) {
            return ResponseEntity.ok("Detalle de pedido con ID " + id + " actualizado correctamente.");
        } else {
            return ResponseEntity.notFound().build();
        }
    }

    @Operation(summary = "Eliminar detalle de pedido", description = "Elimina un detalle de pedido")
    @ApiResponses(value = {
        @ApiResponse(responseCode = "200", description = "Detalle eliminado"),
        @ApiResponse(responseCode = "404", description = "Detalle no encontrado")
    })
    @DeleteMapping("/{id}")
    public ResponseEntity<String> eliminarDetallePedido(
        @Parameter(description = "ID del detalle", required = true)
        @PathVariable int id) {
        int filas = detallePedidosService.eliminarDetallePedido(id);
        if (filas > 0) {
            return ResponseEntity.ok("Detalle de pedido con ID " + id + " eliminado correctamente.");
        } else {
            return ResponseEntity.notFound().build();
        }
    }
}