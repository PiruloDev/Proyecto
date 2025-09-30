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

    // 1. GET: Endpoint para que el frontend obtenga los ingredientes necesarios (el desplegable)
    @GetMapping("/receta/{idProducto}")
    public ResponseEntity<List<RecetaProducto>> obtenerReceta(@PathVariable int idProducto) {
        List<RecetaProducto> receta = recetasService.obtenerRecetaPorProducto(idProducto);

        if (receta.isEmpty()) {
            return ResponseEntity.notFound().build();
        }
        return ResponseEntity.ok(receta);
    }

    // 2. POST: Endpoint para registrar la producción y descontar el inventario
    @PostMapping
    public ResponseEntity<Map<String, Object>> registrarProduccion(@RequestBody ProduccionRequest request) {
        Map<String, Object> response = new HashMap<>();

        if (request.getIdProducto() <= 0 || request.getCantidadProducida() <= 0 || request.getIngredientesDescontados() == null || request.getIngredientesDescontados().isEmpty()) {
            response.put("error", "Faltan datos obligatorios para registrar la producción.");
            return ResponseEntity.status(HttpStatus.BAD_REQUEST).body(response);
        }

        try {
            produccionService.registrarProduccion(request);

            response.put("mensaje", "Producción de " + request.getCantidadProducida() + " unidades registrada con éxito.");
            response.put("status", HttpStatus.OK.value());
            return ResponseEntity.ok(response);

        } catch (RuntimeException e) {
            // Captura los errores lanzados en el servicio (falta stock, ID incorrecto, etc.)
            response.put("error", "Error en la transacción de inventario: " + e.getMessage());
            response.put("status", HttpStatus.INTERNAL_SERVER_ERROR.value());
            return ResponseEntity.status(HttpStatus.INTERNAL_SERVER_ERROR).body(response);
        } catch (Exception e) {
            response.put("error", "Error inesperado al registrar la producción: " + e.getMessage());
            return ResponseEntity.status(HttpStatus.INTERNAL_SERVER_ERROR).body(response);
        }
    }
}