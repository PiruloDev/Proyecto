package com.example.demo.Model;

import java.util.Date;

public class Ingredientes {
    private int ID_INGREDIENTE;
    private Long ID_PROVEEDOR;
    private Long ID_CATEGORIA;
    private String NOMBRE_INGREDIENTE;
    private int CANTIDAD_INGREDIENTE;
    private Date FECHA_VENCIMIENTO;
    private String REFERENCIA_INGREDIENTE;
    private Date FECHA_ENTREGA_INGREDIENTE;

    public Ingredientes(int ID_INGREDIENTE, Long ID_PROVEEDOR, Long ID_CATEGORIA, String NOMBRE_INGREDIENTE, int CANTIDAD_INGREDIENTE, Date FECHA_VENCIMIENTO, String REFERENCIA_INGREDIENTE, Date FECHA_ENTREGA_INGREDIENTE) {
        this.ID_INGREDIENTE = ID_INGREDIENTE;
        this.ID_PROVEEDOR = ID_PROVEEDOR;
        this.ID_CATEGORIA = ID_CATEGORIA;
        this.NOMBRE_INGREDIENTE = NOMBRE_INGREDIENTE;
        this.CANTIDAD_INGREDIENTE = CANTIDAD_INGREDIENTE;
        this.FECHA_VENCIMIENTO = FECHA_VENCIMIENTO;
        this.REFERENCIA_INGREDIENTE = REFERENCIA_INGREDIENTE;
        this.FECHA_ENTREGA_INGREDIENTE = FECHA_ENTREGA_INGREDIENTE;
    }

    public int getID_INGREDIENTE() {
        return ID_INGREDIENTE;
    }

    public void setID_INGREDIENTE(int ID_INGREDIENTE) {
        this.ID_INGREDIENTE = ID_INGREDIENTE;
    }

    public Long getID_PROVEEDOR() {
        return ID_PROVEEDOR;
    }

    public void setID_PROVEEDOR(Long ID_PROVEEDOR) {
        this.ID_PROVEEDOR = ID_PROVEEDOR;
    }

    public Long getID_CATEGORIA() {
        return ID_CATEGORIA;
    }

    public void setID_CATEGORIA(Long ID_CATEGORIA) {
        this.ID_CATEGORIA = ID_CATEGORIA;
    }

    public String getNOMBRE_INGREDIENTE() {
        return NOMBRE_INGREDIENTE;
    }

    public void setNOMBRE_INGREDIENTE(String NOMBRE_INGREDIENTE) {
        this.NOMBRE_INGREDIENTE = NOMBRE_INGREDIENTE;
    }

    public int getCANTIDAD_INGREDIENTE() {
        return CANTIDAD_INGREDIENTE;
    }

    public void setCANTIDAD_INGREDIENTE(int CANTIDAD_INGREDIENTE) {
        this.CANTIDAD_INGREDIENTE = CANTIDAD_INGREDIENTE;
    }

    public Date getFECHA_VENCIMIENTO() {
        return FECHA_VENCIMIENTO;
    }

    public void setFECHA_VENCIMIENTO(Date FECHA_VENCIMIENTO) {
        this.FECHA_VENCIMIENTO = FECHA_VENCIMIENTO;
    }

    public String getREFERENCIA_INGREDIENTE() {
        return REFERENCIA_INGREDIENTE;
    }

    public void setREFERENCIA_INGREDIENTE(String REFERENCIA_INGREDIENTE) {
        this.REFERENCIA_INGREDIENTE = REFERENCIA_INGREDIENTE;
    }

    public Date getFECHA_ENTREGA_INGREDIENTE() {
        return FECHA_ENTREGA_INGREDIENTE;
    }

    public void setFECHA_ENTREGA_INGREDIENTE(Date FECHA_ENTREGA_INGREDIENTE) {
        this.FECHA_ENTREGA_INGREDIENTE = FECHA_ENTREGA_INGREDIENTE;
    }
}
