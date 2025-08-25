package com.example.Proyecto.Services.Empleados;

public class PojoEmpleado {
    private String nombre;
    private String email;
    private int id;
    private String contrasena;

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

    public int getId() {
        return id;
    }
    public void setId(int id) {
        this.id = id;
    }
    public String getContrasena(){
        return contrasena;
    }
    public void setContrasena(String contrasena){
        this.contrasena = contrasena;
    }
}
