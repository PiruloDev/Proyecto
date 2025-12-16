package com.example.Proyecto.controller;

import com.example.Proyecto.model.UsuariosRegistradosDTO;
import com.example.Proyecto.service.UsuariosRegistradosService.UsuariosRegistradosService;
import io.swagger.v3.oas.annotations.Operation;
import io.swagger.v3.oas.annotations.responses.ApiResponse;
import io.swagger.v3.oas.annotations.tags.Tag;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.web.bind.annotation.GetMapping;
import org.springframework.web.bind.annotation.RestController;

import java.util.List;

@RestController
@Tag(name = "Reportes - Usuarios Registrados", description = "Reporte de usuarios registrados en el sistema")
public class UsuariosRegistradosController {

    @Autowired
    private UsuariosRegistradosService usuariosRegistradosService;

    @Operation(summary = "Obtener reporte de usuarios", description = "Retorna un reporte de todos los usuarios registrados en el sistema")
    @ApiResponse(responseCode = "200", description = "Reporte obtenido exitosamente")
    @GetMapping("/reporte/usuarios")
    public List<UsuariosRegistradosDTO> obtenerReporteUsuarios() {
        return usuariosRegistradosService.obtenerUsuariosRegistrados();
    }
}
