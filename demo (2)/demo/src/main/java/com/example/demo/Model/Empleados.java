package com.example.demo.Model;

import java.util.Date;

public class Empleados {
    private String ID_EMPLEADO;
    private String NOMBRE_EMPLEADO;
    private String EMAIL_EMPLEADO;
    private String CONTRASEÑA_EMPLEADO;
    private Boolean ACTIVO_EMPLEADO;
    private String SALT_EMPLEADO;
    private Date FECHA_REGISTRO;

    public Empleados(String ID_EMPLEADO, String NOMBRE_EMPLEADO, String EMAIL_EMPLEADO, String CONTRASEÑA_EMPLEADO, Boolean ACTIVO_EMPLEADO, String SALT_EMPLEADO, Date FECHA_REGISTRO) {
        this.ID_EMPLEADO = ID_EMPLEADO;
        this.NOMBRE_EMPLEADO = NOMBRE_EMPLEADO;
        this.EMAIL_EMPLEADO = EMAIL_EMPLEADO;
        this.CONTRASEÑA_EMPLEADO = CONTRASEÑA_EMPLEADO;
        this.ACTIVO_EMPLEADO = ACTIVO_EMPLEADO;
        this.SALT_EMPLEADO = SALT_EMPLEADO;
        this.FECHA_REGISTRO = FECHA_REGISTRO;
    }

    public String getID_EMPLEADO() {
        return ID_EMPLEADO;
    }

    public void setID_EMPLEADO(String ID_EMPLEADO) {
        this.ID_EMPLEADO = ID_EMPLEADO;
    }

    public String getNOMBRE_EMPLEADO() {
        return NOMBRE_EMPLEADO;
    }

    public void setNOMBRE_EMPLEADO(String NOMBRE_EMPLEADO) {
        this.NOMBRE_EMPLEADO = NOMBRE_EMPLEADO;
    }

    public String getEMAIL_EMPLEADO() {
        return EMAIL_EMPLEADO;
    }

    public void setEMAIL_EMPLEADO(String EMAIL_EMPLEADO) {
        this.EMAIL_EMPLEADO = EMAIL_EMPLEADO;
    }

    public String getCONTRASEÑA_EMPLEADO() {
        return CONTRASEÑA_EMPLEADO;
    }

    public void setCONTRASEÑA_EMPLEADO(String CONTRASEÑA_EMPLEADO) {
        this.CONTRASEÑA_EMPLEADO = CONTRASEÑA_EMPLEADO;
    }

    public Boolean getACTIVO_EMPLEADO() {
        return ACTIVO_EMPLEADO;
    }

    public void setACTIVO_EMPLEADO(Boolean ACTIVO_EMPLEADO) {
        this.ACTIVO_EMPLEADO = ACTIVO_EMPLEADO;
    }

    public String getSALT_EMPLEADO() {
        return SALT_EMPLEADO;
    }

    public void setSALT_EMPLEADO(String SALT_EMPLEADO) {
        this.SALT_EMPLEADO = SALT_EMPLEADO;
    }

    public Date getFECHA_REGISTRO() {
        return FECHA_REGISTRO;
    }

    public void setFECHA_REGISTRO(Date FECHA_REGISTRO) {
        this.FECHA_REGISTRO = FECHA_REGISTRO;
    }
}
