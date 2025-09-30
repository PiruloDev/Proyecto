package com.example.Proyecto.dto;

public class UsuariosRegistradosDTO {
        private String nombre;
        private String email;
        private String telefono;
        private String rol;

        public UsuariosRegistradosDTO(String nombre, String email, String telefono, String rol) {
            this.nombre = nombre;
            this.email = email;
            this.telefono = telefono;
            this.rol = rol;
        }
        // Getters y Setters
        public String getNombre() { return nombre; }
        public void setNombre(String nombre) { this.nombre = nombre; }

        public String getEmail() { return email; }
        public void setEmail(String email) { this.email = email; }
        public String getTelefono() { return telefono; }
        public void setTelefono(String telefono) { this.telefono = telefono; }
        public String getRol() { return rol; }
        public void setRol(String rol) { this.rol = rol; }

    }

