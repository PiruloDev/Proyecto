package com.example.Proyecto.controller;

import com.example.Proyecto.dto.RecetaDetalleDTO;
import com.example.Proyecto.dto.RecetaRequest;
import com.example.Proyecto.model.RecetaProducto;
import com.example.Proyecto.service.Inventario.RecetasService;
import io.swagger.v3.oas.annotations.Operation;
import io.swagger.v3.oas.annotations.Parameter;
import io.swagger.v3.oas.annotations.responses.ApiResponse;
import io.swagger.v3.oas.annotations.responses.ApiResponses;
import io.swagger.v3.oas.annotations.tags.Tag;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.http.HttpStatus;
import org.springframework.http.ResponseEntity;
import org.springframework.web.bind.annotation.*;

import java.util.HashMap;
import java.util.List;
import java.util.Map;

@RestController
@RequestMapping("/inventario/recetas")
@Tag(name = "Recetas", description = "Gestión de recetas de productos")
public class RecetasController {

    @Autowired
    private RecetasService recetasService;

    @Operation(summary = "Obtener todas las recetas", description = "Retorna la lista completa de recetas de productos")
    @ApiResponse(responseCode = "200", description = "Lista obtenida exitosamente")
    @GetMapping
    public ResponseEntity<List<RecetaProducto>> obtenerTodasLasRecetas() {
        List<RecetaProducto> todasLasRecetas = recetasService.obtenerTodasLasRecetas();
        return ResponseEntity.ok(todasLasRecetas);
    }

    @Operation(summary = "Obtener receta por producto", description = "Retorna la receta de un producto específico")
    @ApiResponses(value = {
        @ApiResponse(responseCode = "200", description = "Receta encontrada"),
        @ApiResponse(responseCode = "404", description = "Receta no encontrada")
    })
    @GetMapping("/producto/{idProducto}")
    public ResponseEntity<List<RecetaProducto>> obtenerRecetaPorProducto(
        @Parameter(description = "ID del producto", required = true)
        @PathVariable Long idProducto) {

        List<RecetaProducto> receta = recetasService.obtenerRecetaPorIdProducto(idProducto);

        if (receta.isEmpty()) {
            return ResponseEntity.notFound().build();
        }
        return ResponseEntity.ok(receta);

    }


    @Operation(summary = "Crear receta", description = "Registra una nueva receta para un producto")
    @ApiResponses(value = {
        @ApiResponse(responseCode = "201", description = "Receta creada exitosamente"),
        @ApiResponse(responseCode = "400", description = "Datos inválidos"),
        @ApiResponse(responseCode = "500", description = "Error interno")
    })
    @PostMapping
    public ResponseEntity<Map<String, Object>> crearReceta(
        @Parameter(description = "Datos de la receta", required = true)
        @RequestBody RecetaRequest request) {
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

    @Operation(summary = "Actualizar receta", description = "Actualiza una receta existente de un producto")
    @ApiResponses(value = {
        @ApiResponse(responseCode = "200", description = "Receta actualizada"),
        @ApiResponse(responseCode = "404", description = "Receta no encontrada"),
        @ApiResponse(responseCode = "500", description = "Error interno")
    })
    @PutMapping("/producto/{idProducto}")
    public ResponseEntity<Map<String, Object>> actualizarReceta(
        @Parameter(description = "ID del producto", required = true)
        @PathVariable Long idProducto, 
        @Parameter(description = "Datos actualizados", required = true)
        @RequestBody RecetaRequest request) {
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

    @Operation(summary = "Eliminar receta", description = "Elimina la receta de un producto")
    @ApiResponses(value = {
        @ApiResponse(responseCode = "200", description = "Receta eliminada"),
        @ApiResponse(responseCode = "404", description = "Receta no encontrada"),
        @ApiResponse(responseCode = "500", description = "Error interno")
    })
    @DeleteMapping("/{idProducto}")
    public ResponseEntity<Map<String, Object>> eliminarReceta(
        @Parameter(description = "ID del producto", required = true)
        @PathVariable Long idProducto) {
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

    // En RecetasController.java
    @Operation(summary = "Obtener todas las recetas optimizadas", description = "Retorna recetas con nombres de productos e ingredientes")
    @GetMapping("/optimizadas")
    public ResponseEntity<List<RecetaDetalleDTO>> obtenerTodasLasRecetasOptimizadas() {
        List<RecetaDetalleDTO> todasLasRecetas = recetasService.obtenerTodasLasRecetasOptimizadas();
        return ResponseEntity.ok(todasLasRecetas);
    }

    @Operation(summary = "Obtener receta optimizada por producto")
    @GetMapping("/optimizadas/producto/{idProducto}")
    public ResponseEntity<List<RecetaDetalleDTO>> obtenerRecetaOptimizadaPorProducto(
            @PathVariable Long idProducto) {
        List<RecetaDetalleDTO> receta = recetasService.obtenerRecetaOptimizadaPorProducto(idProducto);
        if (receta.isEmpty()) {
            return ResponseEntity.notFound().build();
        }
        return ResponseEntity.ok(receta);
    }   
}