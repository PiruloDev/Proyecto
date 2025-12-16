package com.example.Proyecto.controller;
import com.example.Proyecto.service.Clientes.ConexionClienteService;
import com.example.Proyecto.service.Administrador.ConexionAdminService;
import com.example.Proyecto.model.PojoAdmin;
import com.example.Proyecto.model.PojoCliente;
import com.example.Proyecto.service.Empleados.ConexionEmpleadoService;
import com.example.Proyecto.model.PojoDeleteEmpleado;
import com.example.Proyecto.model.PojoEmpleado;
import io.swagger.v3.oas.annotations.Operation;
import io.swagger.v3.oas.annotations.Parameter;
import io.swagger.v3.oas.annotations.responses.ApiResponse;
import io.swagger.v3.oas.annotations.responses.ApiResponses;
import io.swagger.v3.oas.annotations.tags.Tag;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.http.HttpStatus;
import org.springframework.http.ResponseEntity;
import org.springframework.web.bind.annotation.*;
import java.util.List;
import java.util.Map;


@RequestMapping("/")
@RestController
@Tag(name = "Gestión de Usuarios", description = "CRUD de administradores, empleados y clientes")
public class ConexionControllerUsers {
    @Autowired
    private ConexionAdminService conexionAdminService;
    @Autowired
    private ConexionEmpleadoService conexionEmpleadoService;
    @Autowired
    private ConexionClienteService conexionClienteService;


    @Operation(summary = "Obtener detalles de administradores", description = "Retorna la información detallada de todos los administradores")
    @ApiResponse(responseCode = "200", description = "Lista obtenida exitosamente")
    @GetMapping("/detalle/administrador")
    public List<Map<String, Object>> obtenerDetallesAdministrador() {
        return conexionAdminService.obtenerDetallesAdministrador();
    }

    @Operation(summary = "Crear administrador", description = "Registra un nuevo administrador en el sistema")
    @ApiResponse(responseCode = "200", description = "Administrador creado exitosamente")
    @PostMapping("/crear/administrador")
    public String crearAdmin(
        @Parameter(description = "Datos del administrador", required = true)
        @RequestBody PojoAdmin pojoAdmin) {
        boolean creado = conexionAdminService.crearAdmin(pojoAdmin);
        if (creado) {
            return "Nuevo Administrador creado exitosamente";
        } else {
            return "Error al crear un nuevo Administrador";
        }
    }

    @Operation(summary = "Actualizar administrador", description = "Actualiza los datos de un administrador")
    @ApiResponses(value = {
        @ApiResponse(responseCode = "200", description = "Administrador actualizado"),
        @ApiResponse(responseCode = "500", description = "Error al actualizar")
    })
    @PatchMapping("/actualizar/administrador")
    public ResponseEntity<String> actualizarAdministrador(
        @Parameter(description = "Datos a actualizar", required = true)
        @RequestBody PojoAdmin pojoAdmin) {
        boolean actualizado = conexionAdminService.actualizarAdministrador(pojoAdmin);
        if (actualizado) {
            return ResponseEntity.ok("El Administrador ha sido actualizado correctamente");
        } else {
            return ResponseEntity.status(HttpStatus.INTERNAL_SERVER_ERROR)
                    .body("Error al actualizar el Administrador");
        }
    }

    @Operation(summary = "Obtener detalles de clientes", description = "Retorna la información detallada de todos los clientes")
    @ApiResponse(responseCode = "200", description = "Lista obtenida exitosamente")
    @GetMapping("/detalle/cliente")
    public List<Map<String, Object>> obtenerDetallesCliente() {
        return conexionClienteService.obtenerDetallesCliente();
    }

    @Operation(summary = "Crear cliente", description = "Registra un nuevo cliente en el sistema")
    @ApiResponse(responseCode = "200", description = "Cliente creado exitosamente")
    @PostMapping("/crear/cliente")
    public String crearCliente(
        @Parameter(description = "Datos del cliente", required = true)
        @RequestBody PojoCliente pojoCliente) {
        boolean creado = conexionClienteService.crearCliente(pojoCliente);
        if (creado) {
            return "Nuevo Cliente creado exitosamente";
        } else {
            return "Error al crear un nuevo Cliente";
        }
    }

    @Operation(summary = "Actualizar cliente", description = "Actualiza parcialmente los datos de un cliente")
    @ApiResponses(value = {
        @ApiResponse(responseCode = "200", description = "Cliente actualizado"),
        @ApiResponse(responseCode = "500", description = "Error al actualizar")
    })
    @PatchMapping("/actualizar/cliente/{id}")
    public ResponseEntity<String> actualizarCliente(
        @Parameter(description = "ID del cliente", required = true)
        @PathVariable int id, 
        @Parameter(description = "Campos a actualizar", required = true)
        @RequestBody Map<String, Object> campos) {
        boolean actualizado = conexionClienteService.actualizarCliente(id, campos);
        if (actualizado) {
            return ResponseEntity.ok("El Cliente ha sido actualizado correctamente");
        } else {
            return ResponseEntity.status(HttpStatus.INTERNAL_SERVER_ERROR)
                    .body("Error al actualizar el Cliente");
        }
    }

    @Operation(summary = "Obtener detalles de empleados", description = "Retorna la información detallada de todos los empleados")
    @ApiResponse(responseCode = "200", description = "Lista obtenida exitosamente")
    @GetMapping("/detalle/empleado")
    public List<Map<String, Object>> obtenerDetallesEmpleado() {
        return conexionEmpleadoService.obtenerDetallesEmpleado();
    }

    @Operation(summary = "Crear empleado", description = "Registra un nuevo empleado en el sistema")
    @ApiResponse(responseCode = "200", description = "Empleado creado exitosamente")
    @PostMapping("/crear/empleado")
    public String crearEmpleado(
        @Parameter(description = "Datos del empleado", required = true)
        @RequestBody PojoEmpleado pojoEmpleado) {
        boolean creado = conexionEmpleadoService.crearEmpleado(pojoEmpleado);
        if (creado) {
            return "Nuevo Empleado creado exitosamente";
        } else {
            return "Error al crear un nuevo Empleado";
        }
    }

    @Operation(summary = "Actualizar empleado", description = "Actualiza parcialmente los datos de un empleado")
    @ApiResponses(value = {
        @ApiResponse(responseCode = "200", description = "Empleado actualizado"),
        @ApiResponse(responseCode = "500", description = "Error al actualizar")
    })
    @PatchMapping("/actualizar/empleado/{id}")
    public ResponseEntity<String> actualizarEmpleado(
        @Parameter(description = "ID del empleado", required = true)
        @PathVariable int id, 
        @Parameter(description = "Campos a actualizar", required = true)
        @RequestBody Map<String, Object> campos) {
        boolean actualizado = conexionEmpleadoService.actualizarEmpleado(id, campos);
        if (actualizado) {
            return ResponseEntity.ok("El Empleado ha sido actualizado correctamente");
        } else {
            return ResponseEntity.status(HttpStatus.INTERNAL_SERVER_ERROR)
                    .body("Error al actualizar el Empleado");
        }
    }

    @Operation(summary = "Eliminar empleado", description = "Elimina un empleado del sistema")
    @ApiResponse(responseCode = "200", description = "Empleado eliminado exitosamente")
    @DeleteMapping("/eliminar/empleado/{id}")
    public String eliminarEmpleado(
        @Parameter(description = "ID del empleado", required = true)
        @PathVariable int id) {
        boolean eliminado = conexionEmpleadoService.eliminarEmpleado((long) id);
        if (eliminado) {
            return "Empleado eliminado exitosamente";
        } else {
            return "Error al eliminar el Empleado";
        }
    }
}
