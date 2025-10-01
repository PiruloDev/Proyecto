package com.example.Proyecto.controller;

import com.example.Proyecto.dto.PedidoRequest;
import com.example.Proyecto.model.Pedidos;
import com.example.Proyecto.service.Pedidos.pedidosService;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.http.HttpStatus;
import org.springframework.http.ResponseEntity;
import org.springframework.transaction.TransactionSystemException; // ¡IMPORTACIÓN CLAVE!
import org.springframework.web.bind.annotation.*;
import java.util.List;

@RestController
@RequestMapping("/pedidos")
public class PedidosController {

    @Autowired
    private pedidosService pedidosService;

    // *********************************************************
    // MÉTODO POST - CREACIÓN DE PEDIDO COMPLETO (STOCK + DETALLE)
    // *********************************************************
    @PostMapping
    public ResponseEntity<?> crearPedidoCompleto(@RequestBody PedidoRequest request) {
        try {
            Pedidos nuevoPedido = pedidosService.crearPedidoConDetalle(request);

            // Éxito: 201 Created
            return new ResponseEntity<>("Pedido " + nuevoPedido.getID_PEDIDO() + " creado con éxito. Stock descontado.", HttpStatus.CREATED);

        } catch (TransactionSystemException tse) {
            // 1. Captura la excepción de la transacción (TSE) y obtiene la causa raíz.
            Throwable rootCause = tse.getRootCause();
            if (rootCause != null && rootCause.getMessage() != null) {
                String errorMessage = rootCause.getMessage();

                // Si la causa raíz es el error de Stock o de Negocio
                if (errorMessage.contains("Stock Insuficiente") || errorMessage.contains("Error de Negocio")) {
                    return new ResponseEntity<>(errorMessage, HttpStatus.BAD_REQUEST); // 400 Bad Request
                }

                // Si es un error SQL o de DB que detuvo la transacción
                return new ResponseEntity<>("Error de Base de Datos/Transacción: " + errorMessage, HttpStatus.INTERNAL_SERVER_ERROR); // 500
            }
            return new ResponseEntity<>("Error de Transacción Desconocido.", HttpStatus.INTERNAL_SERVER_ERROR);

        } catch (RuntimeException e) {
            // 2. Captura la RuntimeException de stock (si se lanza antes de iniciar la transacción)
            String errorMessage = e.getMessage();
            if (errorMessage != null && (errorMessage.contains("Stock Insuficiente") || errorMessage.contains("Error de Negocio"))) {
                return new ResponseEntity<>(errorMessage, HttpStatus.BAD_REQUEST);
            }
            // Error en la capa de controlador o problema de servidor (ej. JSON malformado)
            return new ResponseEntity<>("Error al procesar el pedido. Causa: " + errorMessage, HttpStatus.INTERNAL_SERVER_ERROR);
        }
    }

    // *********************************************************
    // MÉTODOS CRUD EXISTENTES
    // *********************************************************

    @GetMapping
    public ResponseEntity<List<Pedidos>> obtenerTodosLosPedidos() {
        List<Pedidos> pedidos = pedidosService.obtenerPedidos();
        return new ResponseEntity<>(pedidos, HttpStatus.OK);
    }

    // Nota: El método POST original (crearPedidos) fue reemplazado por crearPedidoCompleto.

    @PutMapping("/{id}")
    public String actualizarPedidos(@PathVariable("id") Long id, @RequestBody Pedidos pedidos) {
        pedidosService.actualizarPedido(id, pedidos);
        return "Pedido con ID " + id + " actualizado con éxito.";
    }

    @DeleteMapping("/{id}")
    public String eliminarPedido(@PathVariable("id") Long id) {
        pedidosService.eliminarPedido(id);
        return "Pedido con ID " + id + " eliminado con éxito.";
    }
}