package com.example.Proyecto.controller;

import com.example.Proyecto.model.Usuario;
import com.example.Proyecto.model.PojoAdmin;
import com.example.Proyecto.model.PojoEmpleado;
import com.example.Proyecto.model.PojoCliente;
import com.example.Proyecto.security.JwtUtilidad;
import com.example.Proyecto.service.Auth.AuthService;
import io.swagger.v3.oas.annotations.Operation;
import io.swagger.v3.oas.annotations.Parameter;
import io.swagger.v3.oas.annotations.media.Content;
import io.swagger.v3.oas.annotations.media.Schema;
import io.swagger.v3.oas.annotations.responses.ApiResponse;
import io.swagger.v3.oas.annotations.responses.ApiResponses;
import io.swagger.v3.oas.annotations.tags.Tag;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.http.HttpStatus;
import org.springframework.http.ResponseEntity;
import org.springframework.web.bind.annotation.*;

import java.util.HashMap;
import java.util.Map;

@RestController
@RequestMapping("/auth")
@Tag(name = "Autenticación", description = "Endpoints para autenticación y registro de usuarios (Admin, Empleados y Clientes)")
public class AuthController {

    @Autowired
    private JwtUtilidad jwtUtilidad;
    
    @Autowired
    private AuthService authService;

        @Operation(summary = "Test del controlador", description = "Endpoint de prueba para verificar que el controlador de autenticación está funcionando correctamente")
    @ApiResponses(value = {
        @ApiResponse(responseCode = "200", description = "Controlador funcionando correctamente")
    })
    @GetMapping("/test")
    public ResponseEntity<Map<String, Object>> test() {
        Map<String, Object> response = new HashMap<>();
        response.put("mensaje", "AuthController funcionando correctamente");
        response.put("timestamp", System.currentTimeMillis());
        return ResponseEntity.ok(response);
    }
    @Operation(summary = "Iniciar sesión", description = "Autentica a un usuario (administrador, empleado o cliente) y devuelve un token JWT")
    @ApiResponses(value = {
        @ApiResponse(responseCode = "200", description = "Login exitoso, devuelve token JWT y datos del usuario"),
        @ApiResponse(responseCode = "401", description = "Credenciales inválidas"),
        @ApiResponse(responseCode = "500", description = "Error interno del servidor")
    })
    @PostMapping("/login")
    public ResponseEntity<Map<String, Object>> login(
        @Parameter(description = "Credenciales del usuario (username/email y password)", required = true)
        @RequestBody Usuario usuario) {
        Map<String, Object> response = new HashMap<>();
        
        try {
            // Usuario administrador hardcodeado
            if ("admin".equals(usuario.getUsername()) && "password".equals(usuario.getPassword())) {
                Map<String, Object> adminInfo = new HashMap<>();
                adminInfo.put("id", 1);
                adminInfo.put("nombre", "Admin Hardcoded");
                adminInfo.put("email", "admin");
                adminInfo.put("tipoUsuario", "ADMIN");
                adminInfo.put("rol", "ADMINISTRADOR");
                
                String token = jwtUtilidad.generarToken(adminInfo);
                response.put("token", token);
                response.put("usuario", adminInfo);
                response.put("mensaje", "Login exitoso");
                return ResponseEntity.ok(response);
            }
            // Usuario cliente hardcodeado para pruebas
            if ("cliente".equals(usuario.getUsername()) && "123456".equals(usuario.getPassword())) {
                Map<String, Object> clienteInfo = new HashMap<>();
                clienteInfo.put("id", 2);
                clienteInfo.put("nombre", "Cliente Prueba");
                clienteInfo.put("email", "cliente@test.com");
                clienteInfo.put("tipoUsuario", "CLIENTE");
                clienteInfo.put("rol", "CLIENTE");
                
                String token = jwtUtilidad.generarToken(clienteInfo);
                response.put("token", token);
                response.put("usuario", clienteInfo);
                response.put("mensaje", "Login exitoso");
                return ResponseEntity.ok(response);
            }
            // Autenticación real con base de datos
            Map<String, Object> usuarioAutenticado = authService.autenticarUsuario(usuario.getUsername(), usuario.getPassword());
            if (usuarioAutenticado != null) {
                String token = jwtUtilidad.generarToken(usuarioAutenticado);
                response.put("token", token);
                response.put("mensaje", "Login exitoso");
                
                return ResponseEntity.ok(response);
            } else {
                response.put("error", "Credenciales inválidas");
                response.put("mensaje", "Email o contraseña incorrectos");
                return ResponseEntity.status(HttpStatus.UNAUTHORIZED).body(response);
            }
            
        } catch (Exception e) {
            e.printStackTrace();
            response.put("error", "Error en el servidor");
            response.put("mensaje", "Error interno del servidor: " + e.getMessage());
            return ResponseEntity.status(HttpStatus.INTERNAL_SERVER_ERROR).body(response);
        }
    }

    @Operation(summary = "Registrar administrador", description = "Crea una nueva cuenta de administrador en el sistema")
    @ApiResponses(value = {
        @ApiResponse(responseCode = "201", description = "Administrador registrado exitosamente"),
        @ApiResponse(responseCode = "400", description = "Error en los datos proporcionados"),
        @ApiResponse(responseCode = "500", description = "Error al registrar administrador")
    })
    @PostMapping("/registro/admin")
    public ResponseEntity<Map<String, Object>> registrarAdmin(
        @Parameter(description = "Datos del administrador a registrar", required = true)
        @RequestBody PojoAdmin admin) {
        Map<String, Object> response = new HashMap<>();
        
        try {
            boolean registrado = authService.registrarAdmin(admin);
            if (registrado) {
                response.put("mensaje", "Administrador registrado exitosamente");
                response.put("usuario", admin.getEmail());
                response.put("tipo", "ADMINISTRADOR");
                return ResponseEntity.status(HttpStatus.CREATED).body(response);
            } else {
                response.put("error", "No se pudo registrar el administrador");
                return ResponseEntity.status(HttpStatus.BAD_REQUEST).body(response);
            }
        } catch (Exception e) {
            e.printStackTrace();
            response.put("error", "Error al registrar administrador: " + e.getMessage());
            return ResponseEntity.status(HttpStatus.INTERNAL_SERVER_ERROR).body(response);
        }
    }

    @Operation(summary = "Registrar empleado", description = "Crea una nueva cuenta de empleado en el sistema")
    @ApiResponses(value = {
        @ApiResponse(responseCode = "201", description = "Empleado registrado exitosamente"),
        @ApiResponse(responseCode = "400", description = "Error en los datos proporcionados"),
        @ApiResponse(responseCode = "500", description = "Error al registrar empleado")
    })
    @PostMapping("/registro/empleado")
    public ResponseEntity<Map<String, Object>> registrarEmpleado(
        @Parameter(description = "Datos del empleado a registrar", required = true)
        @RequestBody PojoEmpleado empleado) {
        Map<String, Object> response = new HashMap<>();
        
        try {
            boolean registrado = authService.registrarEmpleado(empleado);
            if (registrado) {
                response.put("mensaje", "Empleado registrado exitosamente");
                response.put("usuario", empleado.getEmail());
                response.put("tipo", "EMPLEADO");
                return ResponseEntity.status(HttpStatus.CREATED).body(response);
            } else {
                response.put("error", "No se pudo registrar el empleado");
                return ResponseEntity.status(HttpStatus.BAD_REQUEST).body(response);
            }
        } catch (Exception e) {
            e.printStackTrace();
            response.put("error", "Error al registrar empleado: " + e.getMessage());
            return ResponseEntity.status(HttpStatus.INTERNAL_SERVER_ERROR).body(response);
        }
    }

    @Operation(summary = "Registrar cliente", description = "Crea una nueva cuenta de cliente en el sistema")
    @ApiResponses(value = {
        @ApiResponse(responseCode = "201", description = "Cliente registrado exitosamente"),
        @ApiResponse(responseCode = "400", description = "Error en los datos proporcionados"),
        @ApiResponse(responseCode = "500", description = "Error al registrar cliente")
    })
    @PostMapping("/registro/cliente")
    public ResponseEntity<Map<String, Object>> registrarCliente(
        @Parameter(description = "Datos del cliente a registrar", required = true)
        @RequestBody PojoCliente cliente) {
        Map<String, Object> response = new HashMap<>();
        
        try {
            boolean registrado = authService.registrarCliente(cliente);
            if (registrado) {
                response.put("mensaje", "Cliente registrado exitosamente");
                response.put("usuario", cliente.getEmail());
                response.put("tipo", "CLIENTE");
                return ResponseEntity.status(HttpStatus.CREATED).body(response);
            } else {
                response.put("error", "No se pudo registrar el cliente");
                return ResponseEntity.status(HttpStatus.BAD_REQUEST).body(response);
            }
        } catch (Exception e) {
            e.printStackTrace();
            response.put("error", "Error al registrar cliente: " + e.getMessage());
            return ResponseEntity.status(HttpStatus.INTERNAL_SERVER_ERROR).body(response);
        }
    }

    @Operation(summary = "Validar token JWT", description = "Verifica si un token JWT es válido y devuelve información del usuario")
    @ApiResponses(value = {
        @ApiResponse(responseCode = "200", description = "Token válido, devuelve información del usuario"),
        @ApiResponse(responseCode = "400", description = "Token no proporcionado"),
        @ApiResponse(responseCode = "401", description = "Token inválido o expirado"),
        @ApiResponse(responseCode = "500", description = "Error al validar token")
    })
    @PostMapping("/validar")
    public ResponseEntity<Map<String, Object>> validarToken(
        @Parameter(description = "Header de autorización con formato: Bearer {token}", required = true)
        @RequestHeader("Authorization") String authHeader) {
        Map<String, Object> response = new HashMap<>();
        
        try {
            if (authHeader != null && authHeader.startsWith("Bearer ")) {
                String token = authHeader.substring(7);
                boolean valido = jwtUtilidad.validarToken(token);
                
                if (valido) {
                    Map<String, Object> infoUsuario = jwtUtilidad.obtenerInfoUsuario(token);
                    response.put("valido", true);
                    response.put("usuario", infoUsuario);
                    return ResponseEntity.ok(response);
                } else {
                    response.put("valido", false);
                    response.put("mensaje", "Token inválido o expirado");
                    return ResponseEntity.status(HttpStatus.UNAUTHORIZED).body(response);
                }
            } else {
                response.put("valido", false);
                response.put("mensaje", "Token no proporcionado");
                return ResponseEntity.status(HttpStatus.BAD_REQUEST).body(response);
            }
        } catch (Exception e) {
            e.printStackTrace();
            response.put("valido", false);
            response.put("error", "Error al validar token: " + e.getMessage());
            return ResponseEntity.status(HttpStatus.INTERNAL_SERVER_ERROR).body(response);
        }
    }
}