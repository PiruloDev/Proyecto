package com.example.Proyecto.Services.Empleados;

public class PojoEmpleado {
        private String nombre;
        private String email;
        private String telefono;

        public PojoEmpleado() {
        }

        public String getNombre() {
            return nombre;
        }

        public void setNombre(String nombre) {
            this.nombre = nombre;
        }

        public String getEmail() {
            return email;
        }

        public void setEmail(String email) {
            this.email = email;
        }

        public String getTelefono() {
            return telefono;
        }

        public void setTelefono(String telefono) {
            this.telefono = telefono;
        }
}
