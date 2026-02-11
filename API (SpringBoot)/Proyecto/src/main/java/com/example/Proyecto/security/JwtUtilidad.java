package com.example.Proyecto.security;

import io.jsonwebtoken.Claims;
import io.jsonwebtoken.Jwts;
import io.jsonwebtoken.security.Keys;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.stereotype.Component;

import javax.crypto.SecretKey;
import java.util.Date;
import java.util.Map;

@Component
public class JwtUtilidad {

    private final String CLAVE_SECRETA = "1234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890";
    private final SecretKey key = Keys.hmacShaKeyFor(CLAVE_SECRETA.getBytes());

    @Autowired
    private TokenBlacklist tokenBlacklist;


    public String generarToken(Map<String, Object> userInfo) {
        return Jwts.builder()
                .subject((String) userInfo.get("email"))
                .claim("userId", userInfo.get("id"))
                .claim("nombre", userInfo.get("nombre"))
                .claim("tipoUsuario", userInfo.get("tipoUsuario"))
                .claim("rol", userInfo.get("rol"))
                .issuedAt(new Date())
                .expiration(new Date(System.currentTimeMillis() + 3600000)) // 1 hora
                .signWith(key)
                .compact();
    }

    /**
     * Genera un token simple con solo username
     */
    public String generarToken(String username) {
        return Jwts.builder()
                .subject(username)
                .issuedAt(new Date())
                .expiration(new Date(System.currentTimeMillis() + 3600000)) // 1 hora
                .signWith(key)
                .compact();
    }

    public boolean validarToken(String token) {
        try {
            // Verificar si el token está en la blacklist
            if (tokenBlacklist != null && tokenBlacklist.isBlacklisted(token)) {
                System.out.println("[JwtUtilidad] Token rechazado: está en blacklist");
                return false;
            }
            Jwts.parser().verifyWith(key).build().parseSignedClaims(token);
            return true;
        } catch (Exception e) {
            return false;
        }
    }

    /**
     * Invalida un token añadiéndolo a la blacklist.
     */
    public void invalidarToken(String token) {
        if (tokenBlacklist != null) {
            try {
                Claims claims = Jwts.parser().verifyWith(key).build()
                        .parseSignedClaims(token).getPayload();
                long expiration = claims.getExpiration().getTime();
                tokenBlacklist.addToBlacklist(token, expiration);
            } catch (Exception e) {
                // Si no se puede parsear, añadir con expiración por defecto
                tokenBlacklist.addToBlacklist(token);
            }
        }
    }

    public String obtenerUsuario(String token) {
        return Jwts.parser()
                .verifyWith(key)
                .build()
                .parseSignedClaims(token)
                .getPayload()
                .getSubject();
    }

    /**
     * Obtiene información completa del usuario desde el token
     */
    public Map<String, Object> obtenerInfoUsuario(String token) {
        Claims claims = Jwts.parser()
                .verifyWith(key)
                .build()
                .parseSignedClaims(token)
                .getPayload();
        
        return Map.of(
            "email", claims.getSubject(),
            "userId", claims.get("userId", Integer.class),
            "nombre", claims.get("nombre", String.class),
            "tipoUsuario", claims.get("tipoUsuario", String.class),
            "rol", claims.get("rol", String.class)
        );
    }


    public String obtenerRol(String token) {
        Claims claims = Jwts.parser()
                .verifyWith(key)
                .build()
                .parseSignedClaims(token)
                .getPayload();
        
        return claims.get("rol", String.class);
    }


    public String obtenerTipoUsuario(String token) {
        Claims claims = Jwts.parser()
                .verifyWith(key)
                .build()
                .parseSignedClaims(token)
                .getPayload();
        
        return claims.get("tipoUsuario", String.class);
    }
}