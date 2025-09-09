package com.example.Proyecto.controller;

import com.example.Proyecto.service.PedidosProveedores.PedidosProveedores;
import com.example.Proyecto.service.PedidosProveedores.PedidosProveedoresService;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.http.ResponseEntity;
import org.springframework.web.bind.annotation.*;
import java.util.List;

@RestController
@RequestMapping
public class PedidosProveedoresController {

    @Autowired
    private PedidosProveedoresService pedidosProveedoresService;

    // GET - Obtener todos los pedidos de proveedores
    @GetMapping("/pedido/proveedores")
    public List<PedidosProveedores> obtenerTodosLosPedidosProveedores() {
        return pedidosProveedoresService.obtenerTodosLosPedidosProveedores();
    }

    // POST - Crear un nuevo pedido de proveedor
    @PostMapping("/pedido/proveedores")
    public ResponseEntity<String> crearPedidoProveedor(@RequestBody PedidosProveedores pedido) {
        pedidosProveedoresService.crearPedidoProveedor(pedido);
        return ResponseEntity.ok("Pedido " + pedido.getNumeroPedido() + " creado con éxito.");
    }

    // PUT - Actualizar un pedido de proveedor existente
    @PutMapping("/pedido/proveedores/{id}")
    public ResponseEntity<String> editarPedidoProveedor(@PathVariable int id, @RequestBody PedidosProveedores pedido) {
        pedido.setIdPedidoProv(id);
        int filas = pedidosProveedoresService.editarPedidoProveedor(pedido);
        if (filas > 0) {
            return ResponseEntity.ok("Pedido de proveedor con ID " + id + " actualizado correctamente.");
        } else {
            return ResponseEntity.notFound().build();
        }
    }

    // DELETE - Eliminar un pedido de proveedor por ID
    @DeleteMapping("/pedido/proveedores/{id}")
    public ResponseEntity<String> eliminarPedidoProveedor(@PathVariable int id) {
        int filas = pedidosProveedoresService.eliminarPedidoProveedor(id);
        if (filas > 0) {
            return ResponseEntity.ok("Pedido de proveedor con ID " + id + " eliminado correctamente.");
        } else {
            return ResponseEntity.notFound().build();
        }
    }
}