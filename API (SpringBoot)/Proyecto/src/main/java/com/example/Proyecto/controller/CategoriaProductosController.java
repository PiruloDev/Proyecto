package com.example.Proyecto.controller;

import com.example.Proyecto.model.PojoCategoria_Productos;
import com.example.Proyecto.service.CategoriaProductos.CategoriaProductosService;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.http.HttpStatus;
import org.springframework.http.ResponseEntity;
import org.springframework.web.bind.annotation.*;

import java.util.List;
import java.util.Map;

@RequestMapping("/categoria")
@RestController
public class CategoriaProductosController {
    @Autowired
    private CategoriaProductosService categoriaProductosService;

    // ----> Categorías GET
    @GetMapping("/detalle/categoria")
    public List<Map<String, Object>> obtenerCategorias() {
        return categoriaProductosService.obtenerCategorias();
    }

    // ----> Categorías POST
    @PostMapping("/crear")
    public String crearCategoria(@RequestBody PojoCategoria_Productos categoria) {
        boolean creado = categoriaProductosService.crearCategoria(categoria);
        if (creado) {
            return "Nueva Categoría creada exitosamente";
        } else {
            return "Error al crear la Categoría";
        }
    }

    // ----> Categorías PATCH
    @PatchMapping("/actualizar/{id}")
    public ResponseEntity<String> actualizarCategoria(@PathVariable("id") int id, @RequestBody PojoCategoria_Productos categoria) {
        try {
            categoria.setIdCategoria(id);
            boolean actualizado = categoriaProductosService.actualizarCategoria(categoria);
            if (actualizado) {
                return ResponseEntity.ok("Categoría " + categoria.getNombreCategoria() + " actualizada correctamente");
            } else {
                return ResponseEntity.status(HttpStatus.BAD_REQUEST).body("Error al actualizar la categoría");
            }
        } catch (Exception e) {
            e.printStackTrace();
            return ResponseEntity.status(HttpStatus.INTERNAL_SERVER_ERROR).body("Excepción: " + e.getMessage());
        }
    }

    // ----> Categorías DELETE
    @DeleteMapping("/eliminar/{id}")
    public ResponseEntity<String> eliminarCategoria(@PathVariable("id") int id) {
        try {
            boolean eliminado = categoriaProductosService.eliminarCategoria(id);
            if (eliminado) {
                return ResponseEntity.ok("Categoría con ID " + id + " eliminada correctamente");
            } else {
                return ResponseEntity.status(HttpStatus.NOT_FOUND).body("No se encontró categoría con ID " + id);
            }
        } catch (Exception e) {
            e.printStackTrace();
            return ResponseEntity.status(HttpStatus.INTERNAL_SERVER_ERROR).body("Error al eliminar la categoría: " + e.getMessage());
        }
    }
}
