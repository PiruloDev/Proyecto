package com.example.Proyecto.controller;

import com.example.Proyecto.model.Ingredientes;
import com.example.Proyecto.dto.IngresoStockRequest;
import com.example.Proyecto.service.Ingredientes.IngredientesService;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.http.HttpStatus;
import org.springframework.http.ResponseEntity;
import org.springframework.web.bind.annotation.*;
import java.util.List;
import java.util.Map;
import java.math.BigDecimal;

@RestController
public class Ingredientescontroller {

    @Autowired
    private IngredientesService ingredientesService;

    @GetMapping("/ingredientes")
    public List<String> obtenerIngredientes() {
        return ingredientesService.obtenerIngredientes();
    }

    @GetMapping("ingredientes/lista")
    public List<Ingredientes> obtenerIngredientesListas() {
        return ingredientesService.obtenerTodosLosIngredientes();
    }

    @PostMapping("/crearingrediente")
    public String crearIngrediente(@RequestBody Ingredientes ingrediente) {
        ingredientesService.crearIngrediente(ingrediente);
        System.out.println("Ingrediente recibido: " + ingrediente.getNombreIngrediente());
        return "Ingrediente " + ingrediente.getNombreIngrediente() + " creado con éxito.";
    }

    @PutMapping("ingrediente/{id}")
    public String editarIngrediente(@PathVariable Long id, @RequestBody Ingredientes ingrediente) {
        ingrediente.setIdIngrediente(id);

        int filas = ingredientesService.editarIngrediente(ingrediente);

        if (filas > 0) {
            return "Ingrediente con ID " + id + " actualizado correctamente.";
        } else {
            return "No se encontró el ingrediente con ID " + id;
        }
    }

    /**
     * MÉTODO CORREGIDO: Maneja la conversión segura a BigDecimal y excepciones.
     */
    @PatchMapping("/{id}/cantidad")
    public String patchCantidad(@PathVariable Long id, @RequestBody Map<String, Object> updates) {
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


    @DeleteMapping("ingrediente/{id}")
    public String eliminarIngrediente(@PathVariable Long id) {
        int filas = ingredientesService.eliminarIngrediente(id);
        if (filas > 0) {
            return "Ingrediente con ID " + id + " eliminado correctamente.";
        } else {
            return "No se encontró el ingrediente con ID " + id;
        }
    }


    @PostMapping("ingredientes/{id}/ingreso")
    public ResponseEntity<String> ingresarStock(@PathVariable Long id, @RequestBody IngresoStockRequest request) {
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
}