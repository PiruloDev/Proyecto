package com.example.Proyecto.controller;
import com.example.Proyecto.service.Proveedores.Proveedores;
import com.example.Proyecto.service.Proveedores.ProveedoresService;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.http.ResponseEntity;
import org.springframework.web.bind.annotation.*;
import java.util.List;

@RestController
@RequestMapping("/proveedores")
public class ProveedoresController {

    @Autowired
    private ProveedoresService proveedoresService;

    // GET - Obtener todos los proveedores
    @GetMapping
    public List<Proveedores> obtenerTodosLosProveedores() {
        return proveedoresService.obtenerTodosLosProveedores();
    }

    // POST - Crear un nuevo proveedor
    @PostMapping
    public ResponseEntity<String> crearProveedor(@RequestBody Proveedores proveedor) {
        proveedoresService.crearProveedor(proveedor);
        return ResponseEntity.ok("Proveedor " + proveedor.getNombreProv() + " creado con éxito.");
    }

    // PUT - Actualizar un proveedor existente
    @PutMapping("/{id}")
    public ResponseEntity<String> editarProveedor(@PathVariable int id, @RequestBody Proveedores proveedor) {
        proveedor.setIdProveedor(id);
        int filas = proveedoresService.editarProveedor(proveedor);
        if (filas > 0) {
            return ResponseEntity.ok("Proveedor con ID " + id + " actualizado correctamente.");
        } else {
            return ResponseEntity.notFound().build();
        }
    }

    // DELETE - Eliminar un proveedor por ID
    @DeleteMapping("/{id}")
    public ResponseEntity<String> eliminarProveedor(@PathVariable int id) {
        int filas = proveedoresService.eliminarProveedor(id);
        if (filas > 0) {
            return ResponseEntity.ok("Proveedor con ID " + id + " eliminado correctamente.");
        } else {
            return ResponseEntity.notFound().build();
        }
    }
}