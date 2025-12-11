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

    @GetMapping
    public ResponseEntity<List<Pedidos>> obtenerTodosLosPedidos() {
        List<Pedidos> pedidos = pedidosService.obtenerPedidos();
        return new ResponseEntity<>(pedidos, HttpStatus.OK);
    }

    @GetMapping("/{id}")
    public ResponseEntity<Pedidos> obtenerPedidoPorId(@PathVariable("id") Long id) {
        Pedidos pedido = pedidosService.obtenerPedidoPorId(id);
        if (pedido != null) {
            return new ResponseEntity<>(pedido, HttpStatus.OK);
        } else {
            return new ResponseEntity<>(HttpStatus.NOT_FOUND);
        }
    }

    @PostMapping
    public ResponseEntity<String> crearPedido(@RequestBody Pedidos pedido) {
        pedidosService.crearPedido(pedido);
        return new ResponseEntity<>("Pedido creado con éxito.", HttpStatus.CREATED);
    }


    @PutMapping("/{id}")
    public ResponseEntity<String> actualizarPedido(@PathVariable("id") Long id, @RequestBody Pedidos pedido) {
        pedidosService.actualizarPedido(id, pedido);
        return new ResponseEntity<>("Pedido con ID " + id + " actualizado con éxito.", HttpStatus.OK);
    }


    @DeleteMapping("/{id}")
    public ResponseEntity<String> eliminarPedido(@PathVariable("id") Long id) {
        pedidosService.eliminarPedido(id);
        return new ResponseEntity<>("Pedido con ID " + id + " eliminado con éxito.", HttpStatus.OK);
    }

    @GetMapping("/pedidos/cliente/{id_cliente}")
    public List<Pedidos> obtenerPedidosPorCliente(@PathVariable long id_cliente) {
        // Implementa la lógica en tu PedidosService para hacer la consulta SQL:
        // SELECT * FROM Pedidos WHERE ID_CLIENTE = ?
        return pedidosService.obtenerPedidosPorCliente(id_cliente);
    }
}