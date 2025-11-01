package com.example.Proyecto.controller;
import com.example.Proyecto.service.Clientes.ConexionClienteService;
import com.example.Proyecto.service.Administrador.ConexionAdminService;
import com.example.Proyecto.model.PojoAdmin;
import com.example.Proyecto.model.PojoCliente;
import com.example.Proyecto.service.Empleados.ConexionEmpleadoService;
import com.example.Proyecto.model.PojoDeleteEmpleado;
import com.example.Proyecto.model.PojoEmpleado;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.http.HttpStatus;
import org.springframework.http.ResponseEntity;
import org.springframework.web.bind.annotation.*;
import java.util.List;
import java.util.Map;


@RequestMapping("/")
@RestController
public class ConexionControllerUsers {
    @Autowired
    private ConexionAdminService conexionAdminService;
    @Autowired
    private ConexionEmpleadoService conexionEmpleadoService;
    @Autowired
    private ConexionClienteService conexionClienteService;


    // ----> Administradores GET
    @GetMapping("/detalle/administrador")
    public List<Map<String, Object>> obtenerDetallesAdministrador() {
        return conexionAdminService.obtenerDetallesAdministrador();
    }

    // ----> Administradores POST
    @PostMapping("/crear/administrador")
    public String crearAdmin(@RequestBody PojoAdmin pojoAdmin) {
        boolean creado = conexionAdminService.crearAdmin(pojoAdmin);
        if (creado) {
            return "Nuevo Administrador creado exitosamente";
        } else {
            return "Error al crear un nuevo Administrador";
        }
    }

    // ----> Administradores PATCH
    @PatchMapping("/actualizar/administrador")
    public ResponseEntity<String> actualizarAdministrador(@RequestBody PojoAdmin pojoAdmin) {
        boolean actualizado = conexionAdminService.actualizarAdministrador(pojoAdmin);
        if (actualizado) {
            return ResponseEntity.ok("El Administrador ha sido actualizado correctamente");
        } else {
            return ResponseEntity.status(HttpStatus.INTERNAL_SERVER_ERROR)
                    .body("Error al actualizar el Administrador");
        }
    }

    // ----> Clientes GET
    @GetMapping("/detalle/cliente/{id}")
    public List<Map<String, Object>> obtenerDetallesCliente(@PathVariable Long id) {
        return conexionClienteService.obtenerDetallesCliente(id);
    }

    // ----> Clientes POST
    @PostMapping("/crear/cliente")
    public String crearCliente(@RequestBody PojoCliente pojoCliente) {
        boolean creado = conexionClienteService.crearCliente(pojoCliente);
        if (creado) {
            return "Nuevo Cliente creado exitosamente";
        } else {
            return "Error al crear un nuevo Cliente";
        }
    }

    // ----> Clientes PATCH
    @PatchMapping("/actualizar/cliente/{id}")
    public ResponseEntity<String> actualizarCliente(@PathVariable int id, @RequestBody Map<String, Object> campos) {
        boolean actualizado = conexionClienteService.actualizarCliente(id, campos);
        if (actualizado) {
            return ResponseEntity.ok("El Cliente ha sido actualizado correctamente");
        } else {
            return ResponseEntity.status(HttpStatus.INTERNAL_SERVER_ERROR)
                    .body("Error al actualizar el Cliente");
        }
    }

    // ----> Empleados GET
    @GetMapping("/detalle/empleado")
    public List<Map<String, Object>> obtenerDetallesEmpleado() {
        return conexionEmpleadoService.obtenerDetallesEmpleado();
    }

    // ----> Empleados POST
    @PostMapping("/crear/empleado")
    public String crearEmpleado(@RequestBody PojoEmpleado pojoEmpleado) {
        boolean creado = conexionEmpleadoService.crearEmpleado(pojoEmpleado);
        if (creado) {
            return "Nuevo Empleado creado exitosamente";
        } else {
            return "Error al crear un nuevo Empleado";
        }
    }

    // ----> Empleados UPDATE
    @PatchMapping("/actualizar/empleado/{id}")
    public ResponseEntity<String> actualizarEmpleado(@PathVariable int id, @RequestBody Map<String, Object> campos) {
        boolean actualizado = conexionEmpleadoService.actualizarEmpleado(id, campos);
        if (actualizado) {
            return ResponseEntity.ok("El Empleado ha sido actualizado correctamente");
        } else {
            return ResponseEntity.status(HttpStatus.INTERNAL_SERVER_ERROR)
                    .body("Error al actualizar el Empleado");
        }
    }

    // ----> Empleados DELETE
    @DeleteMapping("/eliminar/empleado/{id}")
    public String eliminarEmpleado(@PathVariable int id) {
        boolean eliminado = conexionEmpleadoService.eliminarEmpleado((long) id);
        if (eliminado) {
            return "Empleado eliminado exitosamente";
        } else {
            return "Error al eliminar el Empleado";
        }
    }
}
