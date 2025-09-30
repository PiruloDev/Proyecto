package com.example.demoJava1.UsuariosRegistrados.Controller;

import com.example.demoJava1.UsuariosRegistrados.UsuariosRegistradosDTO;
import com.example.demoJava1.UsuariosRegistrados.Service.UsuariosRegistradosService;
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
