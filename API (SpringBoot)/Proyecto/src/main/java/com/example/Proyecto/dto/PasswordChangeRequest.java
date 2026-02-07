package com.example.Proyecto.dto;

public class PasswordChangeRequest {

    private String email;
    private String nuevaContrasena;

    public PasswordChangeRequest() {}

    public PasswordChangeRequest(String email, String nuevaContrasena) {
        this.email = email;
        this.nuevaContrasena = nuevaContrasena;
    }

    public String getEmail() {
        return email;
    }

    public void setEmail(String email) {
        this.email = email;
    }

    public String getNuevaContrasena() {
        return nuevaContrasena;
    }

    public void setNuevaContrasena(String nuevaContrasena) {
        this.nuevaContrasena = nuevaContrasena;
    }
}