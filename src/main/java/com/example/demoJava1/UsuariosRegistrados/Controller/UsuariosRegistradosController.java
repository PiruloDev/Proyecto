package com.example.demoJava1.UsuariosRegistradosController;

import com.example.demoJava1.dto.UsuarioReporteDTO;
import com.example.demoJava1.service.ReporteUsuariosService;
import org.springframework.web.bind.annotation.GetMapping;
import org.springframework.web.bind.annotation.RestController;

import java.util.List;

@RestController
public class UsuariosRegistradosController {

    private final UsuariosRegistradosService reporteService;

    public UsuariosRegistradosController(UsuariosRegistradosService reporteService) {
        this.reporteService = reporteService;
    }

    @GetMapping("/reporte/usuarios")
    public List<UsuariosRegistradosDTO> obtenerReporteUsuarios() {
        return reporteService.generarUsuariosRegistrados();
    }
}
