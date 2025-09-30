package com.example.demoJava1.Ordenes_salida.Ordenes_salidaController;

import com.example.demoJava1.Ordenes_salida.Ordenes_salida;
import com.example.demoJava1.Ordenes_salida.Service.Ordenes_salidaService;
import org.springframework.web.bind.annotation.*;

import java.util.List;

@RestController
public class Ordenes_salidaController {
    private final Ordenes_salidaService ordenes_salidaService;

    public Ordenes_salidaController(Ordenes_salidaService ordenes_salidaService) {
        this.ordenes_salidaService = ordenes_salidaService;
    }

    @GetMapping("/reporte/ventas")
    public List<Ordenes_salida>obtenerOrdenesSalida() {
        return ordenes_salidaService.obtenerOrdenesSalida();
    }
    @PostMapping("/agregar/venta")
    public String agregarVenta(@RequestBody Ordenes_salida ordenesSalida) {
        boolean creado = ordenes_salidaService.agregarVenta(ordenesSalida);
        if (creado) {
            return "Nueva venta agregada exitosamente";
        } else {
            return "Error al agregar una nueva venta";
        }
    }
    @PatchMapping("/actualizar/venta/{id}")
    public String actualizarVenta(@PathVariable int id, @RequestBody Ordenes_salida ordenesSalida) {
        ordenesSalida.setIdFactura(id);
        int result = ordenes_salidaService.actualizarVenta(ordenesSalida);
        return result > 0 ? "Venta actualizada." : "Error al actualizar.";
    }
    @DeleteMapping("/eliminar/venta/{id}")
    public String eliminarVenta(@PathVariable int id) {
        int result = ordenes_salidaService.eliminarVenta(id);
        return result > 0 ? "Venta eliminada." : "Error al eliminar la venta.";
    }
}
