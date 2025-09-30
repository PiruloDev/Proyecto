package com.example.Proyecto.model;

import java.sql.Date;

public class Pedidos_Proveedores {

    private Long ID_PROVEEDOR;
    private long NUMERO_PEDIDO;
    private long FECHA_PEDIDO;
    private Date ESTADO_PEDIDO;

    public Pedidos_Proveedores() {
        this.ID_PROVEEDOR = ID_PROVEEDOR;
        this.NUMERO_PEDIDO = NUMERO_PEDIDO;
        this.FECHA_PEDIDO = FECHA_PEDIDO;
        this.ESTADO_PEDIDO = ESTADO_PEDIDO;
    }

    public Long getID_PROVEEDOR() {
        return ID_PROVEEDOR;
    }

    public void setID_PROVEEDOR(Long ID_PROVEEDOR) {
        this.ID_PROVEEDOR = ID_PROVEEDOR;
    }

    public long getNUMERO_PEDIDO() {
        return NUMERO_PEDIDO;
    }

    public void setNUMERO_PEDIDO(long NUMERO_PEDIDO) {
        this.NUMERO_PEDIDO = NUMERO_PEDIDO;
    }

    public long getFECHA_PEDIDO() {
        return FECHA_PEDIDO;
    }

    public void setFECHA_PEDIDO(long FECHA_PEDIDO) {
        this.FECHA_PEDIDO = FECHA_PEDIDO;
    }

    public Date getESTADO_PEDIDO() {
        return ESTADO_PEDIDO;
    }

    public void setESTADO_PEDIDO(java.sql.Date ESTADO_PEDIDO) {
        this.ESTADO_PEDIDO = ESTADO_PEDIDO;
    }
}
