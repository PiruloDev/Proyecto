package com.example.demo.Model;

public class Administradores {
    private int ID_ADMIN;
    private String NOMBRE_ADMIN;
    private String TELEFONO_ADMIN;
    private String EMAIL_ADMIN;
    private String CONTRASEÑA_ADMIN;
    private String SALT_ADMIN;

    public Administradores(int ID_ADMIN, String NOMBRE_ADMIN, String TELEFONO_ADMIN, String EMAIL_ADMIN, String CONTRASEÑA_ADMIN, String SALT_ADMIN) {
        this.ID_ADMIN = ID_ADMIN;
        this.NOMBRE_ADMIN = NOMBRE_ADMIN;
        this.TELEFONO_ADMIN = TELEFONO_ADMIN;
        this.EMAIL_ADMIN = EMAIL_ADMIN;
        this.CONTRASEÑA_ADMIN = CONTRASEÑA_ADMIN;
        this.SALT_ADMIN = SALT_ADMIN;
    }

    public int getID_ADMIN() {
        return ID_ADMIN;
    }

    public void setID_ADMIN(int ID_ADMIN) {
        this.ID_ADMIN = ID_ADMIN;
    }

    public String getNOMBRE_ADMIN() {
        return NOMBRE_ADMIN;
    }

    public void setNOMBRE_ADMIN(String NOMBRE_ADMIN) {
        this.NOMBRE_ADMIN = NOMBRE_ADMIN;
    }

    public String getTELEFONO_ADMIN() {
        return TELEFONO_ADMIN;
    }

    public void setTELEFONO_ADMIN(String TELEFONO_ADMIN) {
        this.TELEFONO_ADMIN = TELEFONO_ADMIN;
    }

    public String getEMAIL_ADMIN() {
        return EMAIL_ADMIN;
    }

    public void setEMAIL_ADMIN(String EMAIL_ADMIN) {
        this.EMAIL_ADMIN = EMAIL_ADMIN;
    }

    public String getCONTRASEÑA_ADMIN() {
        return CONTRASEÑA_ADMIN;
    }

    public void setCONTRASEÑA_ADMIN(String CONTRASEÑA_ADMIN) {
        this.CONTRASEÑA_ADMIN = CONTRASEÑA_ADMIN;
    }

    public String getSALT_ADMIN() {
        return SALT_ADMIN;
    }

    public void setSALT_ADMIN(String SALT_ADMIN) {
        this.SALT_ADMIN = SALT_ADMIN;
    }
}
