package com.example.demoJava1.Clientes;

import jakarta.persistence.Entity;
import jakarta.persistence.GeneratedValue;
import jakarta.persistence.GenerationType;
import jakarta.persistence.Id;

@Entity
 public class clientes {
    @Id
    @GeneratedValue(strategy = GenerationType.IDENTITY)
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
    public void setId(int id) {
        this.id = id;
    }
     public String getNombre() { return nombre; }
     public String getTelefono() { return telefono; }
     public String getEmail() { return email; }
 }
