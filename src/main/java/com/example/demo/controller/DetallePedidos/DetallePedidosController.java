package com.example.demo.controller.DetallePedidos;

import com.example.demo.Service.DetallePedidos.DetallePedidos;
import com.example.demo.Service.DetallePedidos.DetallePedidosService;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.http.ResponseEntity;
import org.springframework.web.bind.annotation.*;
import java.util.List;

@RestController
@RequestMapping("/detalles-pedidos")
public class DetallePedidosController {

    @Autowired
    private DetallePedidosService detallePedidosService;

    // GET - Obtener todos los detalles de pedidos
    @GetMapping
    public List<DetallePedidos> obtenerTodosLosDetallesPedidos() {
        return detallePedidosService.obtenerTodosLosDetallesPedidos();
    }

    // POST - Crear un nuevo detalle de pedido
    @PostMapping
    public ResponseEntity<String> crearDetallePedido(@RequestBody DetallePedidos detalle) {
        detallePedidosService.crearDetallePedido(detalle);
        return ResponseEntity.ok("Detalle de pedido " + detalle.getIdDetalle() + " creado con éxito.");
    }

    // PUT - Actualizar un detalle de pedido existente
    @PutMapping("/{id}")
    public ResponseEntity<String> editarDetallePedido(@PathVariable int id, @RequestBody DetallePedidos detalle) {
        detalle.setIdDetalle(id);
        int filas = detallePedidosService.editarDetallePedido(detalle);
        if (filas > 0) {
            return ResponseEntity.ok("Detalle de pedido con ID " + id + " actualizado correctamente.");
        } else {
            return ResponseEntity.notFound().build();
        }
    }

    // DELETE - Eliminar un detalle de pedido por ID
    @DeleteMapping("/{id}")
    public ResponseEntity<String> eliminarDetallePedido(@PathVariable int id) {
        int filas = detallePedidosService.eliminarDetallePedido(id);
        if (filas > 0) {
            return ResponseEntity.ok("Detalle de pedido con ID " + id + " eliminado correctamente.");
        } else {
            return ResponseEntity.notFound().build();
        }
    }
}