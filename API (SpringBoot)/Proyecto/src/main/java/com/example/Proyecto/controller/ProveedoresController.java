package com.example.Proyecto.controller;
import com.example.Proyecto.service.Proveedores.Proveedores;
import com.example.Proyecto.service.Proveedores.ProveedoresService;
import io.swagger.v3.oas.annotations.Operation;
import io.swagger.v3.oas.annotations.Parameter;
import io.swagger.v3.oas.annotations.responses.ApiResponse;
import io.swagger.v3.oas.annotations.responses.ApiResponses;
import io.swagger.v3.oas.annotations.tags.Tag;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.http.ResponseEntity;
import org.springframework.web.bind.annotation.*;
import java.util.List;

@RestController
@RequestMapping("/proveedores")
@Tag(name = "Proveedores", description = "Gestión de proveedores")
public class ProveedoresController {

    @Autowired
    private ProveedoresService proveedoresService;

    @Operation(summary = "Obtener todos los proveedores", description = "Retorna la lista completa de proveedores")
    @ApiResponse(responseCode = "200", description = "Lista obtenida exitosamente")
    @GetMapping
    public List<Proveedores> obtenerTodosLosProveedores() {
        return proveedoresService.obtenerTodosLosProveedores();
    }

    @Operation(summary = "Crear proveedor", description = "Registra un nuevo proveedor")
    @ApiResponse(responseCode = "200", description = "Proveedor creado exitosamente")
    @PostMapping
    public ResponseEntity<String> crearProveedor(
        @Parameter(description = "Datos del proveedor", required = true)
        @RequestBody Proveedores proveedor) {
        proveedoresService.crearProveedor(proveedor);
        return ResponseEntity.ok("Proveedor " + proveedor.getNombreProv() + " creado con éxito.");
    }

    @Operation(summary = "Actualizar proveedor", description = "Actualiza los datos de un proveedor existente")
    @ApiResponses(value = {
        @ApiResponse(responseCode = "200", description = "Proveedor actualizado"),
        @ApiResponse(responseCode = "404", description = "Proveedor no encontrado")
    })
    @PutMapping("/{id}")
    public ResponseEntity<String> editarProveedor(
        @Parameter(description = "ID del proveedor", required = true)
        @PathVariable int id, 
        @Parameter(description = "Datos actualizados", required = true)
        @RequestBody Proveedores proveedor) {
        proveedor.setIdProveedor(id);
        int filas = proveedoresService.editarProveedor(proveedor);
        if (filas > 0) {
            return ResponseEntity.ok("Proveedor con ID " + id + " actualizado correctamente.");
        } else {
            return ResponseEntity.notFound().build();
        }
    }

    @Operation(summary = "Eliminar proveedor", description = "Elimina un proveedor del sistema")
    @ApiResponses(value = {
        @ApiResponse(responseCode = "200", description = "Proveedor eliminado"),
        @ApiResponse(responseCode = "404", description = "Proveedor no encontrado")
    })
    @DeleteMapping("/{id}")
    public ResponseEntity<String> eliminarProveedor(
        @Parameter(description = "ID del proveedor", required = true)
        @PathVariable int id) {
        int filas = proveedoresService.eliminarProveedor(id);
        if (filas > 0) {
            return ResponseEntity.ok("Proveedor con ID " + id + " eliminado correctamente.");
        } else {
            return ResponseEntity.notFound().build();
        }
    }
}