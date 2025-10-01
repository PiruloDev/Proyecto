package com.example.Proyecto.controller;

import com.example.Proyecto.model.Pedidos;
import com.example.Proyecto.service.Pedidos.pedidosService;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.http.HttpStatus;
import org.springframework.http.ResponseEntity;
import org.springframework.web.bind.annotation.*;
import java.util.List;

@RestController
@RequestMapping("/pedidos")
public class PedidosController {

    @Autowired
    private pedidosService pedidosService;

    // Endpoint para obtener todos los pedidos (GET)
    @GetMapping
    public ResponseEntity<List<Pedidos>> obtenerTodosLosPedidos() {
        List<Pedidos> pedidos = pedidosService.obtenerPedidos();
        return new ResponseEntity<>(pedidos, HttpStatus.OK);
    }

    // Endpoint para obtener un pedido por su ID (GET)
    @GetMapping("/{id}")
    public ResponseEntity<Pedidos> obtenerPedidoPorId(@PathVariable("id") Long id) {
        Pedidos pedido = pedidosService.obtenerPedidoPorId(id);
        if (pedido != null) {
            return new ResponseEntity<>(pedido, HttpStatus.OK);
        } else {
            return new ResponseEntity<>(HttpStatus.NOT_FOUND);
        }
    }

    // Endpoint para crear un nuevo pedido (POST)
    @PostMapping
    public ResponseEntity<String> crearPedido(@RequestBody Pedidos pedido) {
        pedidosService.crearPedido(pedido);
        return new ResponseEntity<>("Pedido creado con éxito.", HttpStatus.CREATED);
    }

    // Endpoint para actualizar un pedido (PUT)
    @PutMapping("/{id}")
    public ResponseEntity<String> actualizarPedido(@PathVariable("id") Long id, @RequestBody Pedidos pedido) {
        pedidosService.actualizarPedido(id, pedido);
        return new ResponseEntity<>("Pedido con ID " + id + " actualizado con éxito.", HttpStatus.OK);
    }

    // Endpoint para eliminar un pedido (DELETE)
    @DeleteMapping("/{id}")
    public ResponseEntity<String> eliminarPedido(@PathVariable("id") Long id) {
        pedidosService.eliminarPedido(id);
        return new ResponseEntity<>("Pedido con ID " + id + " eliminado con éxito.", HttpStatus.OK);
    }
}