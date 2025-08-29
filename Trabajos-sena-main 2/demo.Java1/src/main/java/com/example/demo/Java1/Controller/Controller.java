package com.example.demo.Java1.Controller;

import com.example.demo.Java1.Service;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.web.bind.annotation.*;

import java.util.List;
import java.util.Map;

@RestController
public class Controller {

    @Autowired
    private Service service;

    @GetMapping("/Productos")
    public List<Map<String, Object>> getProductos() {
        return service.obtenerProductos();
    }

    // ===== GET: Producto por ID =====
    @GetMapping("/{id}")
    public Map<String, Object> getProductoPorId(@PathVariable int id) {
        return service.obtenerProductoPorId(id);
    }

    // ===== POST: Crear producto =====
    @PostMapping
    public String crearProducto(@RequestBody Map<String, Object> producto) {
        int filas = service.crearProducto(producto);
        return filas > 0 ? "Producto creado correctamente" : "Error al crear producto";
    }

    // ===== PUT: Actualizar producto completo =====
    @PutMapping("/{id}")
    public String actualizarProducto(@PathVariable int id, @RequestBody Map<String, Object> producto) {
        int filas = service.actualizarProducto(id, producto);
        return filas > 0 ? "Producto actualizado correctamente" : "Error al actualizar producto";
    }

    // ===== PATCH: Actualizar solo nombre y precio =====
    @PatchMapping("/{id}")
    public String patchProducto(@PathVariable int id, @RequestParam String nombre, @RequestParam Double precio) {
        int filas = service.patchProducto(id, nombre, precio);
        return filas > 0 ? "Producto actualizado parcialmente" : "Error al actualizar producto";
    }

    // ===== DELETE: Eliminar producto =====
    @DeleteMapping("/{id}")
    public String eliminarProducto(@PathVariable int id) {
        int filas = service.eliminarProducto(id);
        return filas > 0 ? "Producto eliminado correctamente" : "Error al eliminar producto";
    }
}


