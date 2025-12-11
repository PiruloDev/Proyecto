package com.example.Proyecto.controller;

import com.example.Proyecto.model.PojoProductos;
import com.example.Proyecto.service.Productos.ProductosService;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.http.HttpStatus;
import org.springframework.http.ResponseEntity;
import org.springframework.web.bind.annotation.*;

import java.util.HashMap;
import java.util.List;
import java.util.Map;

@RequestMapping("/productos")
@RestController
@CrossOrigin(origins = {"http://localhost:8000", "http://127.0.0.1:8000"}) 
public class ProductosController {

    @Autowired
    private ProductosService productosService;

    @GetMapping
    public List<Map<String, Object>> obtenerDetallesProductos() {
        return productosService.obtenerDetallesProducto();
    }

    @GetMapping("/categoria/{idCategoria}")
    public ResponseEntity<List<Map<String, Object>>> obtenerProductosPorCategoria(@PathVariable int idCategoria) {
        try {
            List<Map<String, Object>> productos = productosService.obtenerProductosPorCategoria(idCategoria);
            if (productos != null && !productos.isEmpty()) {
                return ResponseEntity.ok(productos);
            } else {
                return ResponseEntity.status(HttpStatus.NOT_FOUND).body(productos);
            }
        } catch (Exception e) {
            e.printStackTrace();
            return ResponseEntity.status(HttpStatus.INTERNAL_SERVER_ERROR).body(null);
        }
    }

    @GetMapping("/{id}")
    public ResponseEntity<Map<String, Object>> obtenerProductoPorId(@PathVariable int id) {
        try {
            Map<String, Object> producto = productosService.obtenerProductoPorId(id);
            if (producto != null && !producto.isEmpty()) {
                return ResponseEntity.ok(producto);
            } else {
                Map<String, Object> error = new HashMap<>();
                error.put("error", "No se encontró producto con ID " + id);
                error.put("status", 404);
                return ResponseEntity.status(HttpStatus.NOT_FOUND).body(error);
            }
        } catch (Exception e) {
            e.printStackTrace();
            Map<String, Object> error = new HashMap<>();
            error.put("error", "Error al obtener el producto: " + e.getMessage());
            error.put("status", 500);
            return ResponseEntity.status(HttpStatus.INTERNAL_SERVER_ERROR).body(error);
        }
    }

    @PostMapping
    public ResponseEntity<Map<String, Object>> crearProducto(@RequestBody PojoProductos pojoProductos) {
        Map<String, Object> response = new HashMap<>();

        try {
            System.out.println("=== DEBUG POST PRODUCTOS ===");
            System.out.println("Nombre: " + pojoProductos.getNombreProducto());
            System.out.println("Stock Mínimo: " + pojoProductos.getStockMinimo());
            System.out.println("Precio: " + pojoProductos.getPrecio());

            if (pojoProductos.getNombreProducto() == null || pojoProductos.getNombreProducto().trim().isEmpty()) {
                response.put("error", "El nombre del producto es obligatorio");
                response.put("status", 400);
                return ResponseEntity.status(HttpStatus.BAD_REQUEST).body(response);
            }

            boolean creado = productosService.crearProducto(pojoProductos);

            if (creado) {
                response.put("mensaje", "Nuevo Producto creado e ingresado exitosamente");
                response.put("status", 201);
                response.put("producto", pojoProductos.getNombreProducto());
                return ResponseEntity.status(HttpStatus.CREATED).body(response);
            } else {
                response.put("error", "Error al crear un nuevo Producto en la base de datos");
                response.put("status", 500);
                return ResponseEntity.status(HttpStatus.INTERNAL_SERVER_ERROR).body(response);
            }
        } catch (Exception e) {
            e.printStackTrace();
            response.put("error", "Excepción al crear producto: " + e.getMessage());
            response.put("status", 500);
            return ResponseEntity.status(HttpStatus.INTERNAL_SERVER_ERROR).body(response);
        }
    }

    @PutMapping("/{id}")
    public ResponseEntity<Map<String, Object>> actualizarProducto(
            @PathVariable("id") Long id,
            @RequestBody PojoProductos pojoProductos) {

        Map<String, Object> response = new HashMap<>();

        try {
            System.out.println("=== DEBUG PUT PRODUCTOS ===");
            System.out.println("ID a actualizar: " + id);
            System.out.println("Nombre: " + pojoProductos.getNombreProducto());
            System.out.println("Precio: " + pojoProductos.getPrecio());
            System.out.println("Stock: " + pojoProductos.getStockMinimo());

            pojoProductos.setId(id.intValue());
            boolean actualizado = productosService.actualizarProducto(pojoProductos);

            if (actualizado) {
                response.put("mensaje", "Producto actualizado correctamente");
                response.put("status", 200);
                response.put("producto", pojoProductos.getNombreProducto());
                return ResponseEntity.ok(response);
            } else {
                response.put("error", "No se pudo actualizar el producto");
                response.put("status", 400);
                return ResponseEntity.status(HttpStatus.BAD_REQUEST).body(response);
            }
        } catch (Exception e) {
            e.printStackTrace();
            response.put("error", "Excepción al actualizar: " + e.getMessage());
            response.put("status", 500);
            return ResponseEntity.status(HttpStatus.INTERNAL_SERVER_ERROR).body(response);
        }
    }

    @DeleteMapping("/{id}")
    public ResponseEntity<Map<String, Object>> eliminarProducto(@PathVariable("id") int id) {
        Map<String, Object> response = new HashMap<>();

        try {
            System.out.println("=== DEBUG DELETE PRODUCTOS ===");
            System.out.println("ID a eliminar: " + id);

            boolean eliminado = productosService.eliminarProducto(id);

            if (eliminado) {
                response.put("mensaje", "Producto eliminado correctamente");
                response.put("status", 200);
                response.put("id", id);
                return ResponseEntity.ok(response);
            } else {
                response.put("error", "No se encontró producto con ID " + id);
                response.put("status", 404);
                return ResponseEntity.status(HttpStatus.NOT_FOUND).body(response);
            }
        } catch (Exception e) {
            e.printStackTrace();
            response.put("error", "Error al eliminar: " + e.getMessage());
            response.put("status", 500);
            return ResponseEntity.status(HttpStatus.INTERNAL_SERVER_ERROR).body(response);
        }
    }
}