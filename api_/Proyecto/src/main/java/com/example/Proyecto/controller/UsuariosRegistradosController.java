package com.example.Proyecto.controller;

import com.example.Proyecto.dto.UsuariosRegistradosDTO;
import com.example.Proyecto.service.UsuariosRegistradosService.UsuariosRegistradosService;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.web.bind.annotation.GetMapping;
import org.springframework.web.bind.annotation.RestController;

import java.util.List;

@RestController
public class UsuariosRegistradosController {

    @Autowired
    private UsuariosRegistradosService usuariosRegistradosService;

    @GetMapping("/reporte/usuarios")
    public List<UsuariosRegistradosDTO> obtenerReporteUsuarios() {
        return usuariosRegistradosService.obtenerUsuariosRegistrados();
    }
}
