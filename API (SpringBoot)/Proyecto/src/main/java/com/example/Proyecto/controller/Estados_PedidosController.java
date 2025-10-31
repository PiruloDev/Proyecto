package com.example.Proyecto.controller;

import com.example.Proyecto.model.Estado_Pedidos;
import com.example.Proyecto.service.Pedidos.Estados_PedidosService;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.http.HttpStatus;
import org.springframework.http.ResponseEntity;
import org.springframework.web.bind.annotation.*;

import java.util.List;

@RestController
@RequestMapping("/estadosPedidos")
public class Estados_PedidosController {

    @Autowired
    private Estados_PedidosService estados_PedidosService;

    @GetMapping
    public ResponseEntity<List<Estado_Pedidos>> obtenerTodosLosEstados_Pedidos() {
        List<Estado_Pedidos> estadoPedidos = estados_PedidosService.obtenerEstado_Pedidos();
        return new ResponseEntity<>(estadoPedidos, HttpStatus.OK);
    }

    @PostMapping
    public ResponseEntity<String> crearEstadoPedido(@RequestBody Estado_Pedidos estado) {
        Long estadoId = estados_PedidosService.crearEstadoPedido(estado);
        return new ResponseEntity<>("Estado de pedido " + estadoId + " creado con éxito.", HttpStatus.CREATED);
    }

    @PutMapping("/{id}")
    public ResponseEntity<String> actualizarEstado(@PathVariable("id") Long id, @RequestBody Estado_Pedidos estadoPedidos) {
        estados_PedidosService.actualizarEstado(id, estadoPedidos);
        return new ResponseEntity<>("Estado de pedido con ID " + id + " actualizado con éxito.", HttpStatus.OK);
    }

    @DeleteMapping("/{id}")
    public ResponseEntity<String> eliminarestadoPedidos(@PathVariable("id") Long id) {
        estados_PedidosService.eliminarestadoPedido(id);
        return new ResponseEntity<>("Estado de pedido con ID " + id + " eliminado con éxito.", HttpStatus.OK);
    }
}