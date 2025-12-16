package com.example.Proyecto.controller;

import com.example.Proyecto.model.Pedidos;
import com.example.Proyecto.service.Pedidos.pedidosService;
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
@RequestMapping("/pedidos")
@Tag(name = "Pedidos", description = "Gestión de pedidos de clientes")
public class PedidosController {

    @Autowired
    private pedidosService pedidosService;

    @Operation(summary = "Obtener todos los pedidos", description = "Retorna la lista completa de pedidos")
    @ApiResponse(responseCode = "200", description = "Lista de pedidos obtenida exitosamente")
    @GetMapping
    public ResponseEntity<List<Pedidos>> obtenerTodosLosPedidos() {
        List<Pedidos> pedidos = pedidosService.obtenerPedidos();
        return new ResponseEntity<>(pedidos, HttpStatus.OK);
    }

    @Operation(summary = "Obtener pedido por ID", description = "Retorna los detalles de un pedido específico")
    @ApiResponses(value = {
        @ApiResponse(responseCode = "200", description = "Pedido encontrado"),
        @ApiResponse(responseCode = "404", description = "Pedido no encontrado")
    })
    @GetMapping("/{id}")
    public ResponseEntity<Pedidos> obtenerPedidoPorId(
        @Parameter(description = "ID del pedido", required = true)
        @PathVariable("id") Long id) {
        Pedidos pedido = pedidosService.obtenerPedidoPorId(id);
        if (pedido != null) {
            return new ResponseEntity<>(pedido, HttpStatus.OK);
        } else {
            return new ResponseEntity<>(HttpStatus.NOT_FOUND);
        }
    }

    @Operation(summary = "Crear nuevo pedido", description = "Registra un nuevo pedido en el sistema")
    @ApiResponses(value = {
        @ApiResponse(responseCode = "201", description = "Pedido creado exitosamente"),
        @ApiResponse(responseCode = "400", description = "Error en los datos del pedido")
    })
    @PostMapping
    public ResponseEntity<String> crearPedido(
        @Parameter(description = "Datos del pedido", required = true)
        @RequestBody Pedidos pedido) {

        System.out.println("CLIENTE = " + pedido.getID_CLIENTE());
        System.out.println("EMPLEADO = " + pedido.getID_EMPLEADO());
        System.out.println("TOTAL = " + pedido.getTOTAL_PRODUCTO());
        try {
            pedidosService.crearPedido(pedido);
            return ResponseEntity
                    .status(HttpStatus.CREATED)
                    .body("Pedido creado con éxito.");
        } catch (RuntimeException e) {
            return ResponseEntity
                    .status(HttpStatus.BAD_REQUEST)
                    .body(e.getMessage());
        }
    }


    @Operation(summary = "Actualizar pedido", description = "Actualiza los datos de un pedido existente")
    @ApiResponse(responseCode = "200", description = "Pedido actualizado exitosamente")
    @PutMapping("/{id}")
    public ResponseEntity<String> actualizarPedido(
        @Parameter(description = "ID del pedido", required = true)
        @PathVariable("id") Long id, 
        @Parameter(description = "Datos actualizados", required = true)
        @RequestBody Pedidos pedido) {
        pedidosService.actualizarPedido(id, pedido);
        return new ResponseEntity<>("Pedido con ID " + id + " actualizado con éxito.", HttpStatus.OK);
    }


    @Operation(summary = "Eliminar pedido", description = "Elimina un pedido del sistema")
    @ApiResponse(responseCode = "200", description = "Pedido eliminado exitosamente")
    @DeleteMapping("/{id}")
    public ResponseEntity<String> eliminarPedido(
        @Parameter(description = "ID del pedido", required = true)
        @PathVariable("id") Long id) {
        pedidosService.eliminarPedido(id);
        return new ResponseEntity<>("Pedido con ID " + id + " eliminado con éxito.", HttpStatus.OK);
    }

    @Operation(summary = "Obtener pedidos por cliente", description = "Retorna todos los pedidos de un cliente específico")
    @ApiResponse(responseCode = "200", description = "Lista de pedidos del cliente")
    @GetMapping("/cliente/{id_cliente}")
    public List<Pedidos> obtenerPedidosPorCliente(
        @Parameter(description = "ID del cliente", required = true)
        @PathVariable long id_cliente) {
        return pedidosService.obtenerPedidosPorCliente(id_cliente);
    }
}