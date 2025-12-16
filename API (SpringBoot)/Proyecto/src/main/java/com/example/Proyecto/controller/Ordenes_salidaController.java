package com.example.Proyecto.controller;

import com.example.Proyecto.model.Ordenes_salida;
import com.example.Proyecto.service.Ordenes_salida.Ordenes_salidaService;
import io.swagger.v3.oas.annotations.Operation;
import io.swagger.v3.oas.annotations.Parameter;
import io.swagger.v3.oas.annotations.responses.ApiResponse;
import io.swagger.v3.oas.annotations.tags.Tag;
import org.springframework.web.bind.annotation.*;

import java.util.List;

@RestController
@Tag(name = "Reportes - Ventas", description = "Gestión de órdenes de salida y ventas")
public class Ordenes_salidaController {
    private final Ordenes_salidaService ordenes_salidaService;

    public Ordenes_salidaController(Ordenes_salidaService ordenes_salidaService) {
        this.ordenes_salidaService = ordenes_salidaService;
    }

    @Operation(summary = "Obtener reporte de ventas", description = "Retorna todas las órdenes de salida (ventas) registradas")
    @ApiResponse(responseCode = "200", description = "Reporte obtenido exitosamente")
    @GetMapping("/reporte/ventas")
    public List<Ordenes_salida>obtenerOrdenesSalida() {
        return ordenes_salidaService.obtenerOrdenesSalida();
    }
    
    @Operation(summary = "Agregar venta", description = "Registra una nueva orden de salida (venta)")
    @ApiResponse(responseCode = "200", description = "Venta registrada exitosamente")
    @PostMapping("/agregar/venta")
    public String agregarVenta(
        @Parameter(description = "Datos de la venta", required = true)
        @RequestBody Ordenes_salida ordenesSalida) {
        boolean creado = ordenes_salidaService.agregarVenta(ordenesSalida);
        if (creado) {
            return "Nueva venta agregada exitosamente";
        } else {
            return "Error al agregar una nueva venta";
        }
    }
    
    @Operation(summary = "Actualizar venta", description = "Actualiza una orden de salida existente")
    @ApiResponse(responseCode = "200", description = "Venta actualizada exitosamente")
    @PatchMapping("/actualizar/venta/{id}")
    public String actualizarVenta(
        @Parameter(description = "ID de la venta", required = true)
        @PathVariable int id, 
        @Parameter(description = "Datos actualizados", required = true)
        @RequestBody Ordenes_salida ordenesSalida) {
        ordenesSalida.setIdFactura(id);
        int result = ordenes_salidaService.actualizarVenta(ordenesSalida);
        return result > 0 ? "Venta actualizada." : "Error al actualizar.";
    }
    
    @Operation(summary = "Eliminar venta", description = "Elimina una orden de salida")
    @ApiResponse(responseCode = "200", description = "Venta eliminada exitosamente")
    @DeleteMapping("/eliminar/venta/{id}")
    public String eliminarVenta(
        @Parameter(description = "ID de la venta", required = true)
        @PathVariable int id) {
        int result = ordenes_salidaService.eliminarVenta(id);
        return result > 0 ? "Venta eliminada." : "Error al eliminar la venta.";
    }
}
