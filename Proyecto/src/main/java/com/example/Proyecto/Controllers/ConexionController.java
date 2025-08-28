package com.example.Proyecto.Controllers;
import com.example.Proyecto.Services.Administrador.ConexionAdminService;
import com.example.Proyecto.Services.Administrador.PojoAdmin;
import com.example.Proyecto.Services.Clientes.ConexionClienteService;
import com.example.Proyecto.Services.Clientes.PojoCliente;
import com.example.Proyecto.Services.Empleados.ConexionEmpleadoService;
import com.example.Proyecto.Services.Empleados.PojoDeleteEmpleado;
import com.example.Proyecto.Services.Empleados.PojoEmpleado;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.http.HttpStatus;
import org.springframework.http.ResponseEntity;
import org.springframework.web.bind.annotation.*;
import java.util.List;
import java.util.Map;


@RequestMapping("/")
@RestController
public class ConexionController {
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
    public ResponseEntity<String> actualizarAdministrador(@RequestBody PojoAdmin pojoAdmin){
        boolean actualizado = conexionAdminService.actualizarAdministrador(pojoAdmin);
        if(actualizado) {
            return ResponseEntity.ok("El Administrador ha sido actualizado correctamente");
        } else {
            return ResponseEntity.status(HttpStatus.INTERNAL_SERVER_ERROR)
                    .body("Error al actualizar el Administrador");
        }
    }

    // ----> Clientes GET
    @GetMapping("/detalle/cliente")
    public List<Map<String, Object>> obtenerDetallesCliente() {
        return conexionClienteService.obtenerDetallesCliente();
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
    @PatchMapping("/actualizar/cliente")
    public ResponseEntity<String> actualizarCliente(@RequestBody PojoCliente pojoCliente){
        boolean actualizado = conexionClienteService.actualizarCliente(pojoCliente);
        if(actualizado) {
            return ResponseEntity.ok("El Usuario ha sido actualizado correctamente");
        } else {
            return ResponseEntity.status(HttpStatus.INTERNAL_SERVER_ERROR)
                    .body("Error al actualizar el Usuario");
        }
    }

    // ----> Empleados GET
    @GetMapping("/detalle/empleado")
    public List<Map<String, Object>> obtenerDetallesEmpleado(){
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
    @PatchMapping("/actualizar/empleado")
    public ResponseEntity<String> actualizarEmpleado(@RequestBody PojoEmpleado pojoEmpleado){
        boolean actualizado = conexionEmpleadoService.actualizarEmpleado(pojoEmpleado);
        if(actualizado) {
            return ResponseEntity.ok("El Empleado ha sido actualizado correctamente");
        } else {
            return ResponseEntity.status(HttpStatus.INTERNAL_SERVER_ERROR)
                    .body("Error al actualizar el Empleado");
        }
    }

    // ----> Empleados DELETE
    @DeleteMapping("/eliminar/empleado")
    public String eliminarEmpleado(@RequestBody PojoDeleteEmpleado pojoDeleteEmpleado) {
        boolean eliminado = conexionEmpleadoService.eliminarEmpleado(pojoDeleteEmpleado.getId());
        if (eliminado) {
            return "Empleado eliminado exitosamente";
        } else {
            return "Error al eliminar el Empleado";
        }
    }
}
