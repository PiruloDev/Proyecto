package com.example.Proyecto.security;

import org.springframework.scheduling.annotation.Scheduled;
import org.springframework.stereotype.Component;

import java.util.concurrent.ConcurrentHashMap;

/**
 * Servicio de blacklist para tokens JWT invalidados.
 * Almacena tokens revocados en memoria con limpieza automática de tokens expirados.
 */
@Component
public class TokenBlacklist {

    // Mapa: token -> timestamp de expiración (ms)
    private final ConcurrentHashMap<String, Long> blacklistedTokens = new ConcurrentHashMap<>();

    /**
     * Agrega un token a la blacklist.
     * @param token El token JWT a invalidar
     * @param expirationTimeMs Timestamp de expiración del token en milisegundos
     */
    public void addToBlacklist(String token, long expirationTimeMs) {
        blacklistedTokens.put(token, expirationTimeMs);
        System.out.println("[TokenBlacklist] Token añadido a blacklist. Total en blacklist: " + blacklistedTokens.size());
    }

    /**
     * Agrega un token a la blacklist con tiempo de expiración por defecto (1 hora).
     * @param token El token JWT a invalidar
     */
    public void addToBlacklist(String token) {
        // Expirar en 1 hora (igual que la duración del token)
        long expiration = System.currentTimeMillis() + 3600000;
        addToBlacklist(token, expiration);
    }

    /**
     * Verifica si un token está en la blacklist.
     * @param token El token JWT a verificar
     * @return true si el token fue revocado
     */
    public boolean isBlacklisted(String token) {
        return blacklistedTokens.containsKey(token);
    }

    /**
     * Limpieza automática de tokens expirados cada 10 minutos.
     * Los tokens que ya pasaron su tiempo de expiración se eliminan del mapa.
     */
    @Scheduled(fixedRate = 600000) // Cada 10 minutos
    public void cleanupExpiredTokens() {
        long now = System.currentTimeMillis();
        int before = blacklistedTokens.size();
        blacklistedTokens.entrySet().removeIf(entry -> entry.getValue() < now);
        int removed = before - blacklistedTokens.size();
        if (removed > 0) {
            System.out.println("[TokenBlacklist] Limpieza: " + removed + " tokens expirados eliminados. Restantes: " + blacklistedTokens.size());
        }
    }

    /**
     * Obtiene la cantidad de tokens en la blacklist.
     */
    public int size() {
        return blacklistedTokens.size();
    }
}
