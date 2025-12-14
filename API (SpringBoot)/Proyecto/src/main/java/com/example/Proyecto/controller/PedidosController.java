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

    @GetMapping("/cliente/{id_cliente}")
    public List<Pedidos> obtenerPedidosPorCliente(@PathVariable long id_cliente) {
        return pedidosService.obtenerPedidosPorCliente(id_cliente);
    }
}