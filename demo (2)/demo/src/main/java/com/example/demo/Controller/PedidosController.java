package com.example.demo.Controller;

import com.example.demo.Model.Pedidos;
import com.example.demo.Service.pedidosService;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.http.HttpStatus;
import org.springframework.http.ResponseEntity;
import org.springframework.web.bind.annotation.*;
import java.util.List;


@RestController
@RequestMapping("/pedidos")
public class PedidosController {

    // Inyecta el servicio para interactuar con la lógica de negocio
    @Autowired
    private pedidosService pedidosService;

    // Método para obtener todos los pedidos (GET)
    @GetMapping
    public ResponseEntity<List<Pedidos>> obtenerTodosLosPedidos() {
        List<Pedidos> pedidos = pedidosService.obtenerPedidos();
        return new ResponseEntity<>(pedidos, HttpStatus.OK);
    }

    // Método para crear un nuevo pedido (POST)
    @PostMapping
    public String crearPedidos(@RequestBody Pedidos pedidos) {
        // 1. Llama al método del servicio para crear el pedido y captura el ID que devuelve.
        int pedidoId = pedidosService.crearPedido(pedidos);

        // 2. Opcional: Imprime el ID capturado para fines de depuración.
        System.out.println("Pedido recibido con ID: " + pedidoId);

        // 3. Devuelve un mensaje de éxito al cliente, incluyendo el ID real del nuevo pedido.
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