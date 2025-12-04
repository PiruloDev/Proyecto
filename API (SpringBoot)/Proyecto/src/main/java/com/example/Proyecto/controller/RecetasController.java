package com.example.Proyecto.controller;

import com.example.Proyecto.dto.RecetaRequest;
import com.example.Proyecto.model.RecetaProducto;
import com.example.Proyecto.service.Inventario.RecetasService;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.http.HttpStatus;
import org.springframework.http.ResponseEntity;
import org.springframework.web.bind.annotation.*;

import java.util.HashMap;
import java.util.List;
import java.util.Map;
//Fature
@RestController
@RequestMapping("/inventario/recetas")
public class RecetasController {

    @Autowired
    private RecetasService recetasService;

    @GetMapping
    public ResponseEntity<List<RecetaProducto>> obtenerTodasLasRecetas() {
        List<RecetaProducto> todasLasRecetas = recetasService.obtenerTodasLasRecetas();
        return ResponseEntity.ok(todasLasRecetas);
    }

    @GetMapping("/producto/{idProducto}")
    public ResponseEntity<List<RecetaProducto>> obtenerRecetaPorProducto(@PathVariable Long idProducto) {

        List<RecetaProducto> receta = recetasService.obtenerRecetaPorIdProducto(idProducto);

        if (receta.isEmpty()) {
            return ResponseEntity.notFound().build();
        }
        return ResponseEntity.ok(receta);

    }


    @PostMapping
    public ResponseEntity<Map<String, Object>> crearReceta(@RequestBody RecetaRequest request) {
        Map<String, Object> response = new HashMap<>();
        try {
            recetasService.crearReceta(request);
            response.put("mensaje", "Receta creada con éxito para el producto ID " + request.getIdProducto() + ".");
            return ResponseEntity.status(HttpStatus.CREATED).body(response);
        } catch (IllegalArgumentException e) {
            response.put("error", e.getMessage());
            return ResponseEntity.status(HttpStatus.BAD_REQUEST).body(response);
        } catch (Exception e) {
            response.put("error", "Error interno al crear la receta: " + e.getMessage());
            return ResponseEntity.status(HttpStatus.INTERNAL_SERVER_ERROR).body(response);
        }
    }

    @PutMapping("/producto/{idProducto}")
    public ResponseEntity<Map<String, Object>> actualizarReceta(@PathVariable Long idProducto, @RequestBody RecetaRequest request) {
        Map<String, Object> response = new HashMap<>();
        try {
            recetasService.actualizarReceta(idProducto, request);
            response.put("mensaje", "Receta para el producto ID " + idProducto + " actualizada con éxito.");
            return ResponseEntity.ok(response);
        } catch (IllegalArgumentException e) {
            response.put("error", e.getMessage());
            return ResponseEntity.status(HttpStatus.NOT_FOUND).body(response);
        } catch (Exception e) {
            response.put("error", "Error interno al actualizar la receta: " + e.getMessage());
            return ResponseEntity.status(HttpStatus.INTERNAL_SERVER_ERROR).body(response);
        }
    }

    @DeleteMapping("/{idProducto}")
    public ResponseEntity<Map<String, Object>> eliminarReceta(@PathVariable Long idProducto) {
        Map<String, Object> response = new HashMap<>();
        try {
            recetasService.eliminarReceta(idProducto);
            response.put("mensaje", "Receta para el producto ID " + idProducto + " eliminada con éxito.");
            return ResponseEntity.ok(response);
        } catch (IllegalArgumentException e) {
            response.put("error", e.getMessage());
            return ResponseEntity.status(HttpStatus.NOT_FOUND).body(response);
        } catch (Exception e) {
            response.put("error", "Error interno al eliminar la receta: " + e.getMessage());
            return ResponseEntity.status(HttpStatus.INTERNAL_SERVER_ERROR).body(response);
        }
    }
}