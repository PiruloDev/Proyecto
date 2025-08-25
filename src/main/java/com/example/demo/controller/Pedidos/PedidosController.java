package com.example.demo.controller.Pedidos;

import com.example.demo.Pedidos;
import com.example.demo.Service.Pedidos.PedidosService;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.web.bind.annotation.*;

import java.util.List;

@RestController
public class PedidosController {

    @Autowired
    private PedidosService pedidosService;

    // GET → obtener todos los pedidos
    @GetMapping("/ListaPedidos")
    public List<Pedidos> obtenerPedidos() {
        return pedidosService.obtenerTodosLosPedidos();
    }

    // POST → crear un nuevo pedido
    @PostMapping("/ListaPedidos")
    public String crearPedido(@RequestBody Pedidos pedido) {
        pedidosService.crearPedido(pedido);
        System.out.println("Pedido recibido con ID Cliente: " + pedido.getIdCliente());
        return "Pedido creado con éxito para Cliente ID " + pedido.getIdCliente();
    }

    // PATCH → actualizar parcialmente un pedido
    @PatchMapping("/ListaPedidos/{idPedido}")
    public String actualizarParcialPedido(@PathVariable int idPedido, @RequestBody Pedidos cambios) {
        boolean actualizado = pedidosService.actualizarParcialPedido(idPedido, cambios);

        if (actualizado) {
            return "Pedido con ID " + idPedido + " actualizado parcialmente.";
        } else {
            return "No se encontró el pedido con ID " + idPedido;
        }
    }

}
