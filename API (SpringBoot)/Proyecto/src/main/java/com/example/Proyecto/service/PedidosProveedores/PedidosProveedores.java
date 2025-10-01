package com.example.Proyecto.service.PedidosProveedores;

import java.sql.Date;

public class PedidosProveedores {
    private int ID_PEDIDO_PROV;

    private Long ID_PROVEEDOR;
    private long NUMERO_PEDIDO;
    private Date FECHA_PEDIDO;
    private String ESTADO_PEDIDO;

    public PedidosProveedores() {
        this.ID_PEDIDO_PROV = ID_PEDIDO_PROV;
        this.ID_PROVEEDOR = ID_PROVEEDOR;
        this.NUMERO_PEDIDO = NUMERO_PEDIDO;
        this.FECHA_PEDIDO = FECHA_PEDIDO;
        this.ESTADO_PEDIDO = ESTADO_PEDIDO;
    }

    public int getID_PEDIDO_PROV() {
        return ID_PEDIDO_PROV;
    }

    public void setID_PEDIDO_PROV(int ID_PEDIDO_PROV) {
        this.ID_PEDIDO_PROV = ID_PEDIDO_PROV;
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

    public Date getFECHA_PEDIDO() {
        return FECHA_PEDIDO;
    }

    public void setFECHA_PEDIDO(Date FECHA_PEDIDO) {
        this.FECHA_PEDIDO = FECHA_PEDIDO;
    }

    // Aquí está el cambio: el setter debe recibir un String
    public String getESTADO_PEDIDO() {
        return ESTADO_PEDIDO;
    }

    public void setESTADO_PEDIDO(String ESTADO_PEDIDO) {
        this.ESTADO_PEDIDO = ESTADO_PEDIDO;
    }
}