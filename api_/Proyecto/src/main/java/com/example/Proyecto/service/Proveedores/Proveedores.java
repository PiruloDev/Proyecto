// Archivo: Proveedores.java
package com.example.Proyecto.service.Proveedores;

public class Proveedores {
    private int idProveedor;
    private String nombreProv;
    private String telefonoProv;
    private Boolean activoProv;
    private String emailProv;
    private String direccionProv;

    public Proveedores() {}

    public int getIdProveedor() { return idProveedor; }
    public void setIdProveedor(int idProveedor) { this.idProveedor = idProveedor; }

    public String getNombreProv() { return nombreProv; }
    public void setNombreProv(String nombreProv) { this.nombreProv = nombreProv; }

    public String getTelefonoProv() { return telefonoProv; }
    public void setTelefonoProv(String telefonoProv) { this.telefonoProv = telefonoProv; }

    public Boolean getActivoProv() { return activoProv; }
    public void setActivoProv(Boolean activoProv) { this.activoProv = activoProv; }

    public String getEmailProv() { return emailProv; }
    public void setEmailProv(String emailProv) { this.emailProv = emailProv; }

    public String getDireccionProv() { return direccionProv; }
    public void setDireccionProv(String direccionProv) { this.direccionProv = direccionProv; }
}