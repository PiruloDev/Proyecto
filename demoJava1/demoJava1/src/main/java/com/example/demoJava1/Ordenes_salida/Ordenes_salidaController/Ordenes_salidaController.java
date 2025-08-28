package com.example.demoJava1.Ordenes_salida.Ordenes_salidaController;

import com.example.demoJava1.Ordenes_salida.Ordenes_salida;
import com.example.demoJava1.Ordenes_salida.Service.Ordenes_salidaService;
import org.springframework.web.bind.annotation.GetMapping;
import org.springframework.web.bind.annotation.RestController;

import java.util.List;

@RestController
public class Ordenes_salidaController {
    private final Ordenes_salidaService ordenes_salidaService;

    public Ordenes_salidaController(Ordenes_salidaService ordenes_salidaService) {
        this.ordenes_salidaService = ordenes_salidaService;
    }

    @GetMapping("/ventas")
    public List<Ordenes_salida>obtenerOrdenesSalida() {
        return ordenes_salidaService.obtenerOrdenesSalida();
    }
}
