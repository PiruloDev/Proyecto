package com.example.demoJava1.Clientes;

 public class clientes {
     private int id;
     private String nombre;
     private String telefono;
     private String email;

     public clientes(int id, String nombre, String telefono, String email) {
         this.id = id;
         this.nombre = nombre;
         this.telefono = telefono;
         this.email = email;
     };

     public int getId() { return id; }
     public String getNombre() { return nombre; }
     public String getTelefono() { return telefono; }
     public String getEmail() { return email; }
 }
