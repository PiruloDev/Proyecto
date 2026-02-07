package com.example.Proyecto.controller;

import com.example.Proyecto.dto.PasswordChangeRequest;
import com.example.Proyecto.dto.PasswordResetRequest;
import com.example.Proyecto.service.PasswordReset.PasswordResetService;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.http.HttpStatus;
import org.springframework.http.ResponseEntity;
import org.springframework.web.bind.annotation.*;

import java.util.HashMap;
import java.util.Map;

@RestController
@RequestMapping("")
@CrossOrigin(origins = "*")
public class PasswordResetController {

    @Autowired
    private PasswordResetService passwordResetService;

    @PostMapping("/reset-pass")
    public ResponseEntity<Map<String, Object>> resetPassword(@RequestBody PasswordResetRequest request) {
        Map<String, Object> response = new HashMap<>();

        try {
            // Validar que el email no esté vacío
            if (request.getEmail() == null || request.getEmail().trim().isEmpty()) {
                response.put("success", false);
                response.put("mensaje", "El email es obligatorio");
                return ResponseEntity.badRequest().body(response);
            }

            // Validar formato básico de email
            if (!isValidEmail(request.getEmail())) {
                response.put("success", false);
                response.put("mensaje", "El formato del email no es válido");
                return ResponseEntity.badRequest().body(response);
            }

            // Procesar el restablecimiento
            Map<String, Object> resultado = passwordResetService.procesarRestablecimiento(request.getEmail());

            boolean success = (boolean) resultado.get("success");
            response.put("success", success);
            response.put("mensaje", resultado.get("mensaje"));

            if (success) {
                response.put("email", resultado.get("email"));
                response.put("tipoUsuario", resultado.get("tipoUsuario"));
                return ResponseEntity.ok(response);
            } else {
                return ResponseEntity.badRequest().body(response);
            }

        } catch (Exception e) {
            e.printStackTrace();
            response.put("success", false);
            response.put("mensaje", "Error interno del servidor: " + e.getMessage());
            return ResponseEntity.status(HttpStatus.INTERNAL_SERVER_ERROR).body(response);
        }
    }

    @GetMapping("/reset-pass/health")
    public ResponseEntity<Map<String, Object>> healthCheck() {
        return ResponseEntity.ok(Map.of(
                "status", "OK",
                "mensaje", "Reset Password OK",
                "timestamp", System.currentTimeMillis()
        ));
    }

    @PatchMapping("/change-password")
    public ResponseEntity<Map<String, Object>> changePassword(@RequestBody PasswordChangeRequest request) {
        Map<String, Object> response = new HashMap<>();

        try {
            if (request.getEmail() == null || request.getEmail().trim().isEmpty()) {
                response.put("success", false);
                response.put("mensaje", "El email es obligatorio");
                return ResponseEntity.badRequest().body(response);
            }

            if (!isValidEmail(request.getEmail())) {
                response.put("success", false);
                response.put("mensaje", "El formato del email no es válido");
                return ResponseEntity.badRequest().body(response);
            }

            if (request.getNuevaContrasena() == null || request.getNuevaContrasena().trim().isEmpty()) {
                response.put("success", false);
                response.put("mensaje", "La nueva contraseña es obligatoria");
                return ResponseEntity.badRequest().body(response);
            }

            Map<String, Object> resultado = passwordResetService.cambiarContrasena(
                    request.getEmail(),
                    request.getNuevaContrasena()
            );

            boolean success = (boolean) resultado.get("success");
            response.put("success", success);
            response.put("mensaje", resultado.get("mensaje"));

            if (success) {
                response.put("email", resultado.get("email"));
                return ResponseEntity.ok(response);
            } else {
                return ResponseEntity.badRequest().body(response);
            }

        } catch (Exception e) {
            e.printStackTrace();
            response.put("success", false);
            response.put("mensaje", "Error interno del servidor: " + e.getMessage());
            return ResponseEntity.status(HttpStatus.INTERNAL_SERVER_ERROR).body(response);
        }
    }

    private boolean isValidEmail(String email) {
        if (email == null || email.trim().isEmpty()) {
            return false;
        }
        // Expresión regular simple para validar email
        String emailRegex = "^[A-Za-z0-9+_.-]+@[A-Za-z0-9.-]+\\.[A-Za-z]{2,}$";
        return email.matches(emailRegex);
    }
}
