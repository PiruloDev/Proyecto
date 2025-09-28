package com.example.demoJava1.Productos.Services.Controllers;

import com.example.demoJava1.Productos.Services.Controllers.PojoProductos;
import com.example.demoJava1.Productos.Services.Controllers.ProductosServices;
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
    private ProductosServices productosServices;

    // ----> Productos GET
    @GetMapping("/detalle/producto")
    public List<Map<String, Object>> obtenerDetallesProductos() {
        return productosServices.obtenerDetallesProducto();
    }
    // ----> Productos POST
    @PostMapping("/crear/producto")
    public String crearProducto(@RequestBody PojoProductos pojoProductos) {
        boolean creado = productosServices.crearProducto(pojoProductos);
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
            boolean actualizado = productosServices.actualizarProducto(pojoProductos);
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

    @DeleteMapping("/eliminar/producto/{id}")
    public ResponseEntity<String> eliminarProducto(@PathVariable("id") int id) {
        try {
            boolean eliminado = productosServices.eliminarProducto(id);
            if (eliminado) {
                return ResponseEntity.ok("Producto con ID " + id + " eliminado correctamente");
            } else {
                return ResponseEntity.status(HttpStatus.NOT_FOUND).body("No se encontro producto con ID" + id);
            }
        } catch (Exception e) {
            e.printStackTrace();
            return ResponseEntity.status(HttpStatus.INTERNAL_SERVER_ERROR).body("Error al eliminar el producto: " + e.getMessage());
        }
    }
}