package com.example.Proyecto.controller;

import com.example.Proyecto.model.PojoProductos;
import com.example.Proyecto.service.Productos.ProductosService;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.http.HttpStatus;
import org.springframework.http.ResponseEntity;
import org.springframework.web.bind.annotation.*;

import java.util.List;
import java.util.Map;

@RequestMapping("/")
@RestController
public class ProductosController {
    @Autowired
    private ProductosService productosService;

    // ----> Productos GET
    @GetMapping("/detalle/producto")
    public List<Map<String, Object>> obtenerDetallesProductos() {
        return productosService.obtenerDetallesProducto();
    }
    // ----> Productos POST
    @PostMapping("/crear/producto")
    public String crearProducto(@RequestBody PojoProductos pojoProductos) {
        boolean creado = productosService.crearProducto(pojoProductos);
        if (creado) {
            return "Nuevo Producto creado e ingresado exitosamente";
        } else {
            return "Error al crear un nuevo Producto";
        }
    }
    @PatchMapping("/actualizar/producto/{id}")
    public ResponseEntity<String> actualizarProducto(@PathVariable("id") Long id, @RequestBody PojoProductos pojoProductos) {
        try {
            pojoProductos.setId(id.intValue());
            boolean actualizado = productosService.actualizarProducto(pojoProductos);
            if (actualizado) {
                return ResponseEntity.ok("El producto " + pojoProductos.getNombreProducto() + " ha sido actualizado correctamente");
            } else {
                return ResponseEntity.status(HttpStatus.BAD_REQUEST)
                        .body("Error al actualizar el producto");
            }
        } catch (Exception e) {
            e.printStackTrace();
            return ResponseEntity.status(HttpStatus.INTERNAL_SERVER_ERROR)
                        .body("Excepción: " + e.getMessage());
        }
    }
}
