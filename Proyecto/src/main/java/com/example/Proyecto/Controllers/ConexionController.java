package com.example.Proyecto.Controllers;
import com.example.Proyecto.Services.Administrador.ConexionAdminService;
import com.example.Proyecto.Services.Administrador.PojoAdmin;
import com.example.Proyecto.Services.Clientes.ConexionClienteService;
import com.example.Proyecto.Services.Clientes.PojoCliente;
import com.example.Proyecto.Services.Empleados.ConexionEmpleadoService;
import com.example.Proyecto.Services.Empleados.PojoDeleteEmpleado;
import com.example.Proyecto.Services.Empleados.PojoEmpleado;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.web.bind.annotation.*;

import java.util.List;
@RestController
public class ConexionController {
    @Autowired
    private ConexionAdminService conexionAdminService;
    @GetMapping("/admin")
    public List<String> obtenerAdministradores() {
        return conexionAdminService.obtenerAdministradores();
    }
    @Autowired
    private ConexionClienteService conexionClienteService;
    @GetMapping("/clientes")
    public List<String>obtenerClientes() {
        return conexionClienteService.obtenerClientes();
    }
    @Autowired
    private ConexionEmpleadoService conexionEmpleadoService;
    @GetMapping("/empleados")
    public List<String>obtenerEmpleados(){
        return conexionEmpleadoService.obtenerEmpleados();
    }
    @PostMapping("/crearadmin")
    public String crearAdmin(@RequestBody PojoAdmin pojoAdmin) {
        boolean creado = conexionAdminService.crearAdmin(pojoAdmin);
        if (creado) {
            return "Nuevo Administrador creado exitosamente";
        } else {
            return "Error al crear un nuevo Administrador";
        }
    }
    @PostMapping("/crearcliente")
    public String crearCliente(@RequestBody PojoCliente pojoCliente) {
        boolean creado = conexionClienteService.crearCliente(pojoCliente);
        if (creado) {
            return "Nuevo Cliente creado exitosamente";
        } else {
            return "Error al crear un nuevo Cliente";
        }
    }
    @PostMapping("/crearempleado")
    public String crearEmpleado(@RequestBody PojoEmpleado pojoEmpleado) {
        boolean creado = conexionEmpleadoService.crearEmpleado(pojoEmpleado);
        if (creado) {
            return "Nuevo Cliente creado exitosamente";
        } else {
            return "Error al crear un nuevo Cliente";
        }
    }
    @DeleteMapping("/empleado")
    public String eliminarEmpleado(@RequestBody PojoDeleteEmpleado pojoDeleteEmpleado) {
        boolean eliminado = conexionEmpleadoService.eliminarEmpleado(pojoDeleteEmpleado.getId());
        if (eliminado) {
            return "Empleado eliminado exitosamente";
        } else {
            return "Error al eliminar el Empleado";
        }
    }
}
