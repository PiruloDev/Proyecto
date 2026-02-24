package com.example.Proyecto.controller;

import com.example.Proyecto.dto.IngredienteDetalleDTO;
import com.example.Proyecto.model.Ingredientes;
import com.example.Proyecto.dto.IngresoStockRequest;
import com.example.Proyecto.dto.IngredienteListadoDTO;
import com.example.Proyecto.dto.IngredientesCantidad;
import com.example.Proyecto.service.Ingredientes.IngredientesService;
import io.swagger.v3.oas.annotations.Operation;
import io.swagger.v3.oas.annotations.Parameter;
import io.swagger.v3.oas.annotations.responses.ApiResponse;
import io.swagger.v3.oas.annotations.responses.ApiResponses;
import io.swagger.v3.oas.annotations.tags.Tag;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.http.HttpStatus;
import org.springframework.http.ResponseEntity;
import org.springframework.web.bind.annotation.*;
import java.util.List;
import java.util.Map;
import java.util.stream.Collectors;
import java.math.BigDecimal;

@RestController
@Tag(name = "Ingredientes", description = "Gestión de ingredientes y su inventario")
public class Ingredientescontroller {

    @Autowired
    private IngredientesService ingredientesService;

    @Operation(summary = "Obtener nombres de ingredientes", description = "Retorna una lista con los nombres de todos los ingredientes")
    @ApiResponse(responseCode = "200", description = "Lista de nombres obtenida exitosamente")
    @GetMapping("/ingredientes")
    public List<String> obtenerIngredientes() {
        return ingredientesService.obtenerIngredientes();
    }

    @Operation(summary = "Obtener ingredientes con cantidad", description = "Retorna ingredientes con su cantidad actual en inventario")
    @ApiResponses(value = {
        @ApiResponse(responseCode = "200", description = "Lista obtenida exitosamente"),
        @ApiResponse(responseCode = "500", description = "Error interno del servidor")
    })
    @GetMapping("/ingredientes/cantidad")
    public ResponseEntity<List<IngredientesCantidad>> obtenerIngredientesCantidad() {
        try {
            List<IngredientesCantidad> ingredientes = ingredientesService.obtenerIngredientesCantidad();
            return ResponseEntity.ok(ingredientes);
        } catch (Exception e) {
            System.err.println("Error al obtener ingredientes simples: " + e.getMessage());
            return ResponseEntity.status(HttpStatus.INTERNAL_SERVER_ERROR).build();
        }
    }

    @Operation(summary = "Obtener lista completa de ingredientes", description = "Retorna todos los ingredientes con sus detalles completos")
    @ApiResponse(responseCode = "200", description = "Lista completa obtenida exitosamente")
    @GetMapping("ingredientes/lista")
    public List<IngredienteListadoDTO> obtenerIngredientesListas() {
        // ← usa el nuevo metodo con JOIN en lugar del constructor del modelo
        return ingredientesService.obtenerIngredientesParaListado();
    }

    @Operation(summary = "Crear ingrediente", description = "Registra un nuevo ingrediente en el sistema")
    @ApiResponse(responseCode = "200", description = "Ingrediente creado exitosamente")
    @PostMapping("/crearingrediente")
    public String crearIngrediente(
        @Parameter(description = "Datos del ingrediente", required = true)
        @RequestBody Ingredientes ingrediente) {
        ingredientesService.crearIngrediente(ingrediente);
        System.out.println("Ingrediente recibido: " + ingrediente.getNombreIngrediente());
        return "Ingrediente " + ingrediente.getNombreIngrediente() + " creado con éxito.";
    }

    @Operation(summary = "Editar ingrediente", description = "Actualiza los datos de un ingrediente existente")
    @ApiResponses(value = {
        @ApiResponse(responseCode = "200", description = "Ingrediente actualizado exitosamente"),
        @ApiResponse(responseCode = "404", description = "Ingrediente no encontrado")
    })
    @PutMapping("ingrediente/{id}")
    public String editarIngrediente(
        @Parameter(description = "ID del ingrediente", required = true)
        @PathVariable Long id, 
        @Parameter(description = "Datos actualizados", required = true)
        @RequestBody Ingredientes ingrediente) {
        // El Service se encarga de que solo se actualicen los 4 campos principales.
        int filas = ingredientesService.editarIngrediente(id, ingrediente);
        if (filas > 0) {
            return "Ingrediente con ID " + id + " actualizado correctamente.";
        } else {
            return "No se encontró el ingrediente con ID " + id;
        }
    }

    @Operation(summary = "Actualizar cantidad de ingrediente", description = "Actualiza solo la cantidad de un ingrediente")
    @ApiResponses(value = {
        @ApiResponse(responseCode = "200", description = "Cantidad actualizada"),
        @ApiResponse(responseCode = "404", description = "Ingrediente no encontrado"),
        @ApiResponse(responseCode = "500", description = "Error al procesar")
    })
    @PatchMapping("/{id}/cantidad")
    public String patchCantidad(
        @Parameter(description = "ID del ingrediente", required = true)
        @PathVariable Long id, 
        @Parameter(description = "Mapa con cantidadIngrediente", required = true)
        @RequestBody Map<String, Object> updates) {
        if (updates.containsKey("cantidadIngrediente")) {
            try {
                Object cantidadObj = updates.get("cantidadIngrediente");
                BigDecimal cantidadDecimal;

                // 1. Conversión segura del JSON a BigDecimal
                if (cantidadObj instanceof Number) {
                    cantidadDecimal = new BigDecimal(cantidadObj.toString());
                } else if (cantidadObj instanceof String) {
                    cantidadDecimal = new BigDecimal((String) cantidadObj);
                } else {
                    return "Error de formato: La cantidad enviada no es un número válido.";
                }

                // 2. Llamada al servicio con el tipo de dato corregido (BigDecimal)
                int filas = ingredientesService.actualizarCantidad(id, cantidadDecimal);

                return filas > 0 ? "Cantidad actualizada" : "No se encontró el ingrediente con ID " + id;

            } catch (Exception e) {
                // Captura y registra la excepción no controlada que causaba el 500
                System.err.println("Error al actualizar cantidad para ID " + id + ": " + e.getMessage());
                return "Error interno del servidor al procesar la actualización de cantidad.";
            }
        }
        return "No se envió la cantidad";
    }


    @Operation(summary = "Eliminar ingrediente", description = "Elimina un ingrediente del sistema")
    @ApiResponses(value = {
        @ApiResponse(responseCode = "200", description = "Ingrediente eliminado"),
        @ApiResponse(responseCode = "404", description = "Ingrediente no encontrado")
    })
    @DeleteMapping("ingrediente/{id}")
    public String eliminarIngrediente(
        @Parameter(description = "ID del ingrediente", required = true)
        @PathVariable Long id) {
        int filas = ingredientesService.eliminarIngrediente(id);
        if (filas > 0) {
            return "Ingrediente con ID " + id + " eliminado correctamente.";
        } else {
            return "No se encontró el ingrediente con ID " + id;
        }
    }


    @Operation(summary = "Ingresar stock de ingrediente", description = "Registra un ingreso de stock para reponer un ingrediente")
    @ApiResponses(value = {
        @ApiResponse(responseCode = "200", description = "Ingreso registrado exitosamente"),
        @ApiResponse(responseCode = "400", description = "Cantidad inválida"),
        @ApiResponse(responseCode = "404", description = "Ingrediente no encontrado"),
        @ApiResponse(responseCode = "500", description = "Error al ingresar stock")
    })
    @PostMapping("ingredientes/{id}/ingreso")
    public ResponseEntity<String> ingresarStock(
        @Parameter(description = "ID del ingrediente", required = true)
        @PathVariable Long id, 
        @Parameter(description = "Cantidad a ingresar", required = true)
        @RequestBody IngresoStockRequest request) {
        try {
            if (request.getCantidadIngresada() == null || request.getCantidadIngresada().compareTo(BigDecimal.ZERO) <= 0) {
                return ResponseEntity.badRequest().body("La cantidad a ingresar debe ser positiva.");
            }

            int filas = ingredientesService.reponerStock(id, request.getCantidadIngresada());

            if (filas > 0) {
                return ResponseEntity.ok("Ingreso de stock de " + request.getCantidadIngresada() + " unidades para ID " + id + " registrado con éxito.");
            } else {
                return ResponseEntity.status(HttpStatus.NOT_FOUND).body("Ingrediente con ID " + id + " no encontrado.");
            }
        } catch (Exception e) {
            return ResponseEntity.status(HttpStatus.INTERNAL_SERVER_ERROR).body("Error al ingresar stock: " + e.getMessage());
        }
    }


    @GetMapping("/recetas/lista-modal")
    public List<IngredienteDetalleDTO> ListaModal() {
        return ingredientesService.obtenerIngredientesParaModal();
    }
}