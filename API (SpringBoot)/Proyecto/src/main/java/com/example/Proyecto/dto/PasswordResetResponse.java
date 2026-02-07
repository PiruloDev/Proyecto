package com.example.Proyecto.dto;

/**
 * DTO para respuesta de restablecimiento de contraseña
 */
public class PasswordResetResponse {

    private boolean success;
    private String mensaje;
    private String email;
    private String tipoUsuario;

    public PasswordResetResponse() {}

    public PasswordResetResponse(boolean success, String mensaje) {
        this.success = success;
        this.mensaje = mensaje;
    }

    public PasswordResetResponse(boolean success, String mensaje, String email, String tipoUsuario) {
        this.success = success;
        this.mensaje = mensaje;
        this.email = email;
        this.tipoUsuario = tipoUsuario;
    }

    public boolean isSuccess() {
        return success;
    }

    public void setSuccess(boolean success) {
        this.success = success;
    }

    public String getMensaje() {
        return mensaje;
    }

    public void setMensaje(String mensaje) {
        this.mensaje = mensaje;
    }

    public String getEmail() {
        return email;
    }

    public void setEmail(String email) {
        this.email = email;
    }

    public String getTipoUsuario() {
        return tipoUsuario;
    }

    public void setTipoUsuario(String tipoUsuario) {
        this.tipoUsuario = tipoUsuario;
    }

    @Override
    public String toString() {
        return "PasswordResetResponse{" +
                "success=" + success +
                ", mensaje='" + mensaje + '\'' +
                ", email='" + email + '\'' +
                ", tipoUsuario='" + tipoUsuario + '\'' +
                '}';
    }
}
