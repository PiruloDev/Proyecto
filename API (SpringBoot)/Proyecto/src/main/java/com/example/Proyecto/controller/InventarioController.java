package com.example.Proyecto.controller;

import com.example.Proyecto.dto.ProduccionRequest;
import com.example.Proyecto.model.Produccion;
import com.example.Proyecto.service.Inventario.ProduccionService;
import com.example.Proyecto.service.Inventario.RecetasService;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.http.HttpStatus;
import org.springframework.http.ResponseEntity;
import org.springframework.web.bind.annotation.*;
import java.math.BigDecimal;
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


    @GetMapping
    public ResponseEntity<List<Produccion>> obtenerTodoElHistorial() {
        List<Produccion> historial = produccionService.obtenerTodoElHistorial();
        if (historial.isEmpty()) {
            return ResponseEntity.noContent().build();
        }
        return ResponseEntity.status(HttpStatus.OK).body(historial);
    }

    @PostMapping
    public ResponseEntity<Map<String, Object>> registrarProduccion(@RequestBody ProduccionRequest request) {
        Map<String, Object> response = new HashMap<>();

        try {
            if (request.getIdProducto() == null) {
                throw new IllegalArgumentException("El ID del producto es obligatorio.");
            }
            if (request.getCantidadProducida() == null || request.getCantidadProducida().compareTo(BigDecimal.ZERO) <= 0) {
                throw new IllegalArgumentException("La cantidad producida debe ser un valor positivo.");
            }

            Long idProduccion = produccionService.registrarProduccion(request);

            response.put("idProduccion", idProduccion);
            response.put("mensaje", "Producción registrada y stock actualizado con éxito.");
            response.put("status", HttpStatus.CREATED.value());
            return ResponseEntity.status(HttpStatus.CREATED).body(response);

        } catch (IllegalStateException e) {
            response.put("error", "Error de inventario: " + e.getMessage());
            response.put("status", HttpStatus.BAD_REQUEST.value());
            return ResponseEntity.status(HttpStatus.BAD_REQUEST).body(response);

        } catch (IllegalArgumentException e) {
            response.put("error", e.getMessage());
            response.put("status", HttpStatus.BAD_REQUEST.value());
            return ResponseEntity.status(HttpStatus.BAD_REQUEST).body(response);

        } catch (Exception e) {
            response.put("error", "Error inesperado al registrar producción: " + e.getMessage());
            response.put("status", HttpStatus.INTERNAL_SERVER_ERROR.value());
            return ResponseEntity.status(HttpStatus.INTERNAL_SERVER_ERROR).body(response);
        }
    }

    @DeleteMapping("/{idProduccion}")
    public ResponseEntity<Map<String, Object>> eliminarProduccion(@PathVariable Long idProduccion) {
        try {

            produccionService.eliminarProduccion(idProduccion);

            return ResponseEntity.noContent().build();

        } catch (IllegalArgumentException e) {
            Map<String, Object> response = new HashMap<>();
            response.put("error", e.getMessage());
            response.put("status", HttpStatus.NOT_FOUND.value());
            return ResponseEntity.status(HttpStatus.NOT_FOUND).body(response); // 404 si no existe

        } catch (IllegalStateException e) {
            Map<String, Object> response = new HashMap<>();
            response.put("error", "Error de reversión de inventario: " + e.getMessage());
            response.put("status", HttpStatus.BAD_REQUEST.value());
            return ResponseEntity.status(HttpStatus.BAD_REQUEST).body(response);

        } catch (Exception e) {
            Map<String, Object> response = new HashMap<>();
            response.put("error", "Error inesperado al eliminar producción: " + e.getMessage());
            response.put("status", HttpStatus.INTERNAL_SERVER_ERROR.value());
            return ResponseEntity.status(HttpStatus.INTERNAL_SERVER_ERROR).body(response);
        }
    }
}