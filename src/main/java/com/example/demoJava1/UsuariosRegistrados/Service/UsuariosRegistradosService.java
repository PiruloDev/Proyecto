package com.example.demoJava1.UsuariosRegistrados;

import com.example.demoJava1.dto.UsuarioReporteDTO;
import com.example.demoJava1.model.Cliente;
import com.example.demoJava1.model.Empleado;
import com.example.demoJava1.model.Administrador;
import com.example.demoJava1.repository.ClienteRepository;
import com.example.demoJava1.repository.EmpleadoRepository;
import com.example.demoJava1.repository.AdministradorRepository;
import org.springframework.stereotype.Service;

import java.util.ArrayList;
import java.util.List;

@Service
public class ReporteUsuariosService {

    private final Cliente clienteRepo;
    private final Empleados empleadoRepo;
    private final Administradores adminRepo;

    public ReporteUsuariosService(Cliente clienteRepo,
                                  Empleados empleadoRepo,
                                  Administradores adminRepo) {
        this.clienteRepo = clienteRepo;
        this.empleadoRepo = empleadoRepo;
        this.adminRepo = adminRepo;
    }

    public List<UsuariosRegistradosDTO> generarReporteUsuarios() {
        List<UsuariosRegistradosDTO> reporte = new ArrayList<>();

        for (Cliente c : clienteRepo.findAll()) {
            reporte.add(new UsuariosRegistradosDTO(c.getNombre(), c.getEmail(), "Cliente"));
        }
        for (Empleado e : empleadoRepo.findAll()) {
            reporte.add(new UsuarioReporteDTO(e.getNombre(), e.getEmail(), "Empleado"));
        }
        for (Administrador a : adminRepo.findAll()) {
            reporte.add(new UsuarioReporteDTO(a.getNombre(), a.getEmail(), "Administrador"));
        }

        return reporte;
    }
}
