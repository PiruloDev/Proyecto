package com.example.Proyecto.controller;

import com.example.Proyecto.model.Pedidos_Proveedores;
import com.example.Proyecto.service.PedidosProveedores.Pedidos_ProveedoresService;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.http.HttpStatus;
import org.springframework.http.ResponseEntity;
import org.springframework.web.bind.annotation.*;

import java.util.List;

@RestController
@RequestMapping("/pedidosproveedores")
public class Pedidos_ProveedoresController {

    @Autowired
    private Pedidos_ProveedoresService pedidosProveedoresService;

    // Endpoint para obtener todos los pedidos de proveedores (GET)
    @GetMapping
    public ResponseEntity<List<Pedidos_Proveedores>> obtenerTodosLosPedidosProveedores() {
        List<Pedidos_Proveedores> pedidosProveedores = pedidosProveedoresService.obtenerPedidosProveedores();
        return new ResponseEntity<>(pedidosProveedores, HttpStatus.OK);
    }

    // Endpoint para crear un nuevo pedido de proveedor (POST)
    @PostMapping
    public ResponseEntity<String> crearPedidosProveedores(@RequestBody Pedidos_Proveedores pedidosProveedores) {
        pedidosProveedoresService.crearPedidoProveedor(pedidosProveedores);
        return new ResponseEntity<>("Pedido de proveedor creado con éxito.", HttpStatus.CREATED);
    }

    // Endpoint para actualizar un pedido de proveedor (PUT)
    @PutMapping("/{id}")
    public ResponseEntity<String> actualizarPedidosProveedores(@PathVariable("id") int id, @RequestBody Pedidos_Proveedores pedidosProveedores) {
        pedidosProveedoresService.actualizarPedidosProveedores(id, pedidosProveedores);
        return new ResponseEntity<>("Pedido de proveedor con ID " + id + " actualizado con éxito.", HttpStatus.OK);
    }

    // Endpoint para eliminar un pedido de proveedor (DELETE)
    @DeleteMapping("/{id}")
    public ResponseEntity<String> eliminarPedidosProveedores(@PathVariable("id") int id) {
        pedidosProveedoresService.eliminarPedidosProveedores(id);
        return new ResponseEntity<>("Pedido de proveedor con ID " + id + " eliminado con éxito.", HttpStatus.OK);
    }
}