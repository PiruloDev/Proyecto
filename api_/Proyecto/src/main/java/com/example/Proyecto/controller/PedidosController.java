package com.example.Proyecto.controller;
import com.example.Proyecto.model.Pedidos;
import com.example.Proyecto.service.Pedidos.pedidosService;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.http.HttpStatus;
import org.springframework.http.ResponseEntity;
import org.springframework.transaction.annotation.Transactional;
import org.springframework.web.bind.annotation.*;
import java.util.List;

@Transactional
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
    @PostMapping
    public String crearPedidos(@RequestBody Pedidos pedidos) {
        int pedidoId = pedidosService.crearPedido(pedidos);
        System.out.println("Pedido recibido con ID: " + pedidoId);
        return "Pedido " + pedidoId + " creado con éxito.";
    }
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