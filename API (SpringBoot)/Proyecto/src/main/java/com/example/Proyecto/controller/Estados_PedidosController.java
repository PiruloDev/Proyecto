package com.example.Proyecto.controller;

import com.example.Proyecto.model.Estado_Pedidos;
import com.example.Proyecto.service.Pedidos.Estados_PedidosService;
import io.swagger.v3.oas.annotations.Operation;
import io.swagger.v3.oas.annotations.Parameter;
import io.swagger.v3.oas.annotations.responses.ApiResponse;
import io.swagger.v3.oas.annotations.tags.Tag;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.http.HttpStatus;
import org.springframework.http.ResponseEntity;
import org.springframework.web.bind.annotation.*;

import java.util.List;

@RestController
@RequestMapping("/estadosPedidos")
@Tag(name = "Estados de Pedidos", description = "Gestión de estados de pedidos")
public class Estados_PedidosController {

    @Autowired
    private Estados_PedidosService estados_PedidosService;

    @Operation(summary = "Obtener todos los estados", description = "Retorna la lista de todos los estados de pedidos")
    @ApiResponse(responseCode = "200", description = "Lista obtenida exitosamente")
    @GetMapping
    public ResponseEntity<List<Estado_Pedidos>> obtenerTodosLosEstados_Pedidos() {
        List<Estado_Pedidos> estadoPedidos = estados_PedidosService.obtenerEstado_Pedidos();
        return new ResponseEntity<>(estadoPedidos, HttpStatus.OK);
    }

    @Operation(summary = "Crear estado de pedido", description = "Registra un nuevo estado de pedido")
    @ApiResponse(responseCode = "201", description = "Estado creado exitosamente")
    @PostMapping
    public ResponseEntity<String> crearEstadoPedido(
        @Parameter(description = "Datos del estado", required = true)
        @RequestBody Estado_Pedidos estado) {
        Long estadoId = estados_PedidosService.crearEstadoPedido(estado);
        return new ResponseEntity<>("Estado de pedido " + estadoId + " creado con éxito.", HttpStatus.CREATED);
    }

    @Operation(summary = "Actualizar estado de pedido", description = "Actualiza un estado existente")
    @ApiResponse(responseCode = "200", description = "Estado actualizado exitosamente")
    @PutMapping("/{id}")
    public ResponseEntity<String> actualizarEstado(
        @Parameter(description = "ID del estado", required = true)
        @PathVariable("id") Long id, 
        @Parameter(description = "Datos actualizados", required = true)
        @RequestBody Estado_Pedidos estadoPedidos) {
        estados_PedidosService.actualizarEstado(id, estadoPedidos);
        return new ResponseEntity<>("Estado de pedido con ID " + id + " actualizado con éxito.", HttpStatus.OK);
    }

    @Operation(summary = "Eliminar estado de pedido", description = "Elimina un estado de pedido")
    @ApiResponse(responseCode = "200", description = "Estado eliminado exitosamente")
    @DeleteMapping("/{id}")
    public ResponseEntity<String> eliminarestadoPedidos(
        @Parameter(description = "ID del estado", required = true)
        @PathVariable("id") Long id) {
        estados_PedidosService.eliminarestadoPedido(id);
        return new ResponseEntity<>("Estado de pedido con ID " + id + " eliminado con éxito.", HttpStatus.OK);
    }
}