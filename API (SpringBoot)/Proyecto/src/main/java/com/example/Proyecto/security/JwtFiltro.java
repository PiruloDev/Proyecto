package com.example.Proyecto.security;

import org.springframework.web.filter.OncePerRequestFilter;

import jakarta.servlet.FilterChain;
import jakarta.servlet.ServletException;
import jakarta.servlet.http.HttpServletRequest;
import jakarta.servlet.http.HttpServletResponse;
import java.io.IOException;
import java.util.Map;

public class JwtFiltro extends OncePerRequestFilter {

    private JwtUtilidad jwtUtilidad;

    public JwtFiltro() {
        this.jwtUtilidad = new JwtUtilidad();
    }

    public void setJwtUtilidad(JwtUtilidad jwtUtilidad) {
        this.jwtUtilidad = jwtUtilidad;
    }

    @Override
    protected void doFilterInternal(HttpServletRequest solicitud,
                                    HttpServletResponse respuesta,
                                    FilterChain cadenaFiltro) throws ServletException, IOException {

        String authHeader = solicitud.getHeader("Authorization");

        if (authHeader != null && authHeader.startsWith("Bearer ")) {
            String token = authHeader.substring(7);

            if (!jwtUtilidad.validarToken(token)) {
                respuesta.setStatus(HttpServletResponse.SC_UNAUTHORIZED);
                respuesta.getWriter().write("{\"error\": \"Token inválido o expirado\"}");
                return;
            }


            try {
                Map<String, Object> infoUsuario = jwtUtilidad.obtenerInfoUsuario(token);
                solicitud.setAttribute("usuarioAutenticado", infoUsuario);
                solicitud.setAttribute("tipoUsuario", infoUsuario.get("tipoUsuario"));
                solicitud.setAttribute("rolUsuario", infoUsuario.get("rol"));
                solicitud.setAttribute("userEmail", infoUsuario.get("email"));
                solicitud.setAttribute("userId", infoUsuario.get("userId"));
                
                System.out.println("=== FILTRO JWT ===");
                System.out.println("Usuario autenticado: " + infoUsuario.get("email"));
                System.out.println("Tipo: " + infoUsuario.get("tipoUsuario"));
                System.out.println("Rol: " + infoUsuario.get("rol"));
                
            } catch (Exception e) {
                String usuario = jwtUtilidad.obtenerUsuario(token);
                solicitud.setAttribute("userEmail", usuario);
                System.out.println("Usuario autenticado (simple): " + usuario);
            }
            
        } else {
            respuesta.setStatus(HttpServletResponse.SC_UNAUTHORIZED);
            respuesta.getWriter().write("{\"error\": \"Token de autorización requerido\"}");
            return;
        }

        cadenaFiltro.doFilter(solicitud, respuesta);
    }
}