package com.example.demo.Model;

import java.math.BigDecimal;
import java.util.Date;

public class Productos {
    private int ID_PRODUCTO;
    private Long ID_ADMIN;
    private Long ID_CATEGORIA_PRODUCTO;
    private String NOMBRE_PRODUCTO;
    private String DESCRPCION_PRODUCTO;
    private int PRODUCTO_STOCK_MIN;
    private BigDecimal PRECIO_PRODUCTO;
    private Date FECHA_VENCIMIENTO_PPRODUCTO;
    private Date FECHA_INGRESO_PRODUCTO;
    private String TIPO_PRODUCTO_MARCA;
    private Boolean ACTIVO;

    public Productos(int ID_PRODUCTO, Long ID_ADMIN, Long ID_CATEGORIA_PRODUCTO, String NOMBRE_PRODUCTO, String DESCRPCION_PRODUCTO, int PRODUCTO_STOCK_MIN, BigDecimal PRECIO_PRODUCTO, Date FECHA_VENCIMIENTO_PPRODUCTO, Date FECHA_INGRESO_PRODUCTO, String TIPO_PRODUCTO_MARCA, Boolean ACTIVO) {
        this.ID_PRODUCTO = ID_PRODUCTO;
        this.ID_ADMIN = ID_ADMIN;
        this.ID_CATEGORIA_PRODUCTO = ID_CATEGORIA_PRODUCTO;
        this.NOMBRE_PRODUCTO = NOMBRE_PRODUCTO;
        this.DESCRPCION_PRODUCTO = DESCRPCION_PRODUCTO;
        this.PRODUCTO_STOCK_MIN = PRODUCTO_STOCK_MIN;
        this.PRECIO_PRODUCTO = PRECIO_PRODUCTO;
        this.FECHA_VENCIMIENTO_PPRODUCTO = FECHA_VENCIMIENTO_PPRODUCTO;
        this.FECHA_INGRESO_PRODUCTO = FECHA_INGRESO_PRODUCTO;
        this.TIPO_PRODUCTO_MARCA = TIPO_PRODUCTO_MARCA;
        this.ACTIVO = ACTIVO;
    }

    public int getID_PRODUCTO() {
        return ID_PRODUCTO;
    }

    public void setID_PRODUCTO(int ID_PRODUCTO) {
        this.ID_PRODUCTO = ID_PRODUCTO;
    }

    public Long getID_ADMIN() {
        return ID_ADMIN;
    }

    public void setID_ADMIN(Long ID_ADMIN) {
        this.ID_ADMIN = ID_ADMIN;
    }

    public Long getID_CATEGORIA_PRODUCTO() {
        return ID_CATEGORIA_PRODUCTO;
    }

    public void setID_CATEGORIA_PRODUCTO(Long ID_CATEGORIA_PRODUCTO) {
        this.ID_CATEGORIA_PRODUCTO = ID_CATEGORIA_PRODUCTO;
    }

    public String getNOMBRE_PRODUCTO() {
        return NOMBRE_PRODUCTO;
    }

    public void setNOMBRE_PRODUCTO(String NOMBRE_PRODUCTO) {
        this.NOMBRE_PRODUCTO = NOMBRE_PRODUCTO;
    }

    public String getDESCRPCION_PRODUCTO() {
        return DESCRPCION_PRODUCTO;
    }

    public void setDESCRPCION_PRODUCTO(String DESCRPCION_PRODUCTO) {
        this.DESCRPCION_PRODUCTO = DESCRPCION_PRODUCTO;
    }

    public int getPRODUCTO_STOCK_MIN() {
        return PRODUCTO_STOCK_MIN;
    }

    public void setPRODUCTO_STOCK_MIN(int PRODUCTO_STOCK_MIN) {
        this.PRODUCTO_STOCK_MIN = PRODUCTO_STOCK_MIN;
    }

    public BigDecimal getPRECIO_PRODUCTO() {
        return PRECIO_PRODUCTO;
    }

    public void setPRECIO_PRODUCTO(BigDecimal PRECIO_PRODUCTO) {
        this.PRECIO_PRODUCTO = PRECIO_PRODUCTO;
    }

    public Date getFECHA_VENCIMIENTO_PPRODUCTO() {
        return FECHA_VENCIMIENTO_PPRODUCTO;
    }

    public void setFECHA_VENCIMIENTO_PPRODUCTO(Date FECHA_VENCIMIENTO_PPRODUCTO) {
        this.FECHA_VENCIMIENTO_PPRODUCTO = FECHA_VENCIMIENTO_PPRODUCTO;
    }

    public Date getFECHA_INGRESO_PRODUCTO() {
        return FECHA_INGRESO_PRODUCTO;
    }

    public void setFECHA_INGRESO_PRODUCTO(Date FECHA_INGRESO_PRODUCTO) {
        this.FECHA_INGRESO_PRODUCTO = FECHA_INGRESO_PRODUCTO;
    }

    public String getTIPO_PRODUCTO_MARCA() {
        return TIPO_PRODUCTO_MARCA;
    }

    public void setTIPO_PRODUCTO_MARCA(String TIPO_PRODUCTO_MARCA) {
        this.TIPO_PRODUCTO_MARCA = TIPO_PRODUCTO_MARCA;
    }

    public Boolean getACTIVO() {
        return ACTIVO;
    }

    public void setACTIVO(Boolean ACTIVO) {
        this.ACTIVO = ACTIVO;
    }
}
