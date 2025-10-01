package com.example.Proyecto.controller;

import com.example.Proyecto.dto.ProduccionRequest;
import com.example.Proyecto.service.Inventario.ProduccionService;
import com.example.Proyecto.service.Inventario.RecetasService;
import com.example.Proyecto.model.RecetaProducto;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.http.HttpStatus;
import org.springframework.http.ResponseEntity;
import org.springframework.web.bind.annotation.*;

import java.util.HashMap;
import java.util.List;
import java.util.Map;

@RestController
@RequestMapping("/inventario/produccion")
public class InventarioController {

    @Autowired
    private RecetasService recetasService;

    @Autowired
    private ProduccionService produccionService;

    // 1. GET: Endpoint para que el frontend obtenga los ingredientes de un producto (receta)
    @GetMapping("/receta/{idProducto}")
    public ResponseEntity<List<RecetaProducto>> obtenerReceta(@PathVariable int idProducto) {
        List<RecetaProducto> receta = recetasService.obtenerRecetaPorProducto(idProducto);

        if (receta.isEmpty()) {
            return ResponseEntity.notFound().build();
        }
        return ResponseEntity.ok(receta);
    }

    // 2. POST: Registrar la producción y actualizar inventario
    @PostMapping
    public ResponseEntity<Map<String, Object>> registrarProduccion(@RequestBody ProduccionRequest request) {
        Map<String, Object> response = new HashMap<>();

        // Validaciones mínimas
        if (request.getIdProducto() == null || request.getIdProducto() <= 0 ||
                request.getCantidadProducida() == null || request.getCantidadProducida() <= 0) {
            response.put("error", "Faltan datos obligatorios: idProducto y cantidadProducida.");
            return ResponseEntity.status(HttpStatus.BAD_REQUEST).body(response);
        }

        try {
            produccionService.registrarProduccion(request);

            response.put("mensaje", "Producción de " + request.getCantidadProducida() +
                    " unidades registrada con éxito.");
            response.put("status", HttpStatus.OK.value());
            return ResponseEntity.ok(response);

        } catch (IllegalArgumentException | IllegalStateException e) {
            response.put("error", e.getMessage());
            response.put("status", HttpStatus.BAD_REQUEST.value());
            return ResponseEntity.status(HttpStatus.BAD_REQUEST).body(response);

        } catch (Exception e) {
            response.put("error", "Error inesperado: " + e.getMessage());
            response.put("status", HttpStatus.INTERNAL_SERVER_ERROR.value());
            return ResponseEntity.status(HttpStatus.INTERNAL_SERVER_ERROR).body(response);
        }
    }

    // PUT: actualizar producción completa
    @PutMapping("/{idProduccion}")
    public ResponseEntity<Map<String, Object>> actualizarProduccion(
            @PathVariable Long idProduccion,
            @RequestBody ProduccionRequest request) {

        Map<String, Object> response = new HashMap<>();

        try {
            produccionService.actualizarProduccion(idProduccion, request);
            response.put("mensaje", "Producción con ID " + idProduccion + " actualizada correctamente.");
            response.put("status", HttpStatus.OK.value());
            return ResponseEntity.ok(response);

        } catch (IllegalArgumentException e) {
            response.put("error", e.getMessage());
            response.put("status", HttpStatus.BAD_REQUEST.value());
            return ResponseEntity.status(HttpStatus.BAD_REQUEST).body(response);

        } catch (Exception e) {
            response.put("error", "Error inesperado: " + e.getMessage());
            response.put("status", HttpStatus.INTERNAL_SERVER_ERROR.value());
            return ResponseEntity.status(HttpStatus.INTERNAL_SERVER_ERROR).body(response);
        }
    }

    // PATCH: actualizar solo ciertos campos (ej: cantidad producida)
    @PatchMapping("/{idProduccion}")
    public ResponseEntity<Map<String, Object>> actualizarParcial(
            @PathVariable Long idProduccion,
            @RequestBody Map<String, Object> updates) {

        Map<String, Object> response = new HashMap<>();

        try {
            produccionService.actualizarParcial(idProduccion, updates);
            response.put("mensaje", "Producción con ID " + idProduccion + " modificada parcialmente.");
            response.put("status", HttpStatus.OK.value());
            return ResponseEntity.ok(response);

        } catch (IllegalArgumentException e) {
            response.put("error", e.getMessage());
            response.put("status", HttpStatus.BAD_REQUEST.value());
            return ResponseEntity.status(HttpStatus.BAD_REQUEST).body(response);

        } catch (Exception e) {
            response.put("error", "Error inesperado: " + e.getMessage());
            response.put("status", HttpStatus.INTERNAL_SERVER_ERROR.value());
            return ResponseEntity.status(HttpStatus.INTERNAL_SERVER_ERROR).body(response);
        }
    }

    // DELETE: eliminar producción
    @DeleteMapping("/{idProduccion}")
    public ResponseEntity<Map<String, Object>> eliminarProduccion(@PathVariable Long idProduccion) {
        Map<String, Object> response = new HashMap<>();

        try {
            produccionService.eliminarProduccion(idProduccion);
            response.put("mensaje", "Producción con ID " + idProduccion + " eliminada correctamente.");
            response.put("status", HttpStatus.OK.value());
            return ResponseEntity.ok(response);

        } catch (IllegalArgumentException e) {
            response.put("error", e.getMessage());
            response.put("status", HttpStatus.BAD_REQUEST.value());
            return ResponseEntity.status(HttpStatus.BAD_REQUEST).body(response);

        } catch (Exception e) {
            response.put("error", "Error inesperado: " + e.getMessage());
            response.put("status", HttpStatus.INTERNAL_SERVER_ERROR.value());
            return ResponseEntity.status(HttpStatus.INTERNAL_SERVER_ERROR).body(response);
        }
    }


}
