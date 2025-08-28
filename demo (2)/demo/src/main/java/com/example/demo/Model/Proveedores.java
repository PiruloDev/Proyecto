package com.example.demo.Model;

public class Proveedores {
    private int ID_PROVEEDOR;
    private String NOMBRE_PROV;
    private String TELEFONO_PROV;
    private boolean ACTIVO_PROV;
    private String EMAIL_PROV;
    private String DIRECCION_PROV;

    public Proveedores(int ID_PROVEEDOR, String NOMBRE_PROV, String TELEFONO_PROV, boolean ACTIVO_PROV, String EMAIL_PROV, String DIRECCION_PROV) {
        this.ID_PROVEEDOR = ID_PROVEEDOR;
        this.NOMBRE_PROV = NOMBRE_PROV;
        this.TELEFONO_PROV = TELEFONO_PROV;
        this.ACTIVO_PROV = ACTIVO_PROV;
        this.EMAIL_PROV = EMAIL_PROV;
        this.DIRECCION_PROV = DIRECCION_PROV;
    }

    public int getID_PROVEEDOR() {
        return ID_PROVEEDOR;
    }

    public void setID_PROVEEDOR(int ID_PROVEEDOR) {
        this.ID_PROVEEDOR = ID_PROVEEDOR;
    }

    public String getNOMBRE_PROV() {
        return NOMBRE_PROV;
    }

    public void setNOMBRE_PROV(String NOMBRE_PROV) {
        this.NOMBRE_PROV = NOMBRE_PROV;
    }

    public String getTELEFONO_PROV() {
        return TELEFONO_PROV;
    }

    public void setTELEFONO_PROV(String TELEFONO_PROV) {
        this.TELEFONO_PROV = TELEFONO_PROV;
    }

    public boolean isACTIVO_PROV() {
        return ACTIVO_PROV;
    }

    public void setACTIVO_PROV(boolean ACTIVO_PROV) {
        this.ACTIVO_PROV = ACTIVO_PROV;
    }

    public String getEMAIL_PROV() {
        return EMAIL_PROV;
    }

    public void setEMAIL_PROV(String EMAIL_PROV) {
        this.EMAIL_PROV = EMAIL_PROV;
    }

    public String getDIRECCION_PROV() {
        return DIRECCION_PROV;
    }

    public void setDIRECCION_PROV(String DIRECCION_PROV) {
        this.DIRECCION_PROV = DIRECCION_PROV;
    }
}
