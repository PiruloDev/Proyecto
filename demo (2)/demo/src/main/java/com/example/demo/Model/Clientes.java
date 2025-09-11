package com.example.demo.Model;

public class Clientes {
    private int ID_CLIENTE;
    private String NOMBRE_CLI;
    private String TELEFONO_CLI;
    private String EMAIL_CLI;

    public Clientes() {
        this.ID_CLIENTE = ID_CLIENTE;
        this.NOMBRE_CLI = NOMBRE_CLI;
        this.TELEFONO_CLI = TELEFONO_CLI;
        this.EMAIL_CLI = EMAIL_CLI;
    }

    public int getID_CLIENTE() {
        return ID_CLIENTE;
    }

    public void setID_CLIENTE(int ID_CLIENTE) {
        this.ID_CLIENTE = ID_CLIENTE;
    }

    public String getNOMBRE_CLI() {
        return NOMBRE_CLI;
    }

    public void setNOMBRE_CLI(String NOMBRE_CLI) {
        this.NOMBRE_CLI = NOMBRE_CLI;
    }

    public String getTELEFONO_CLI() {
        return TELEFONO_CLI;
    }

    public void setTELEFONO_CLI(String TELEFONO_CLI) {
        this.TELEFONO_CLI = TELEFONO_CLI;
    }

    public String getEMAIL_CLI() {
        return EMAIL_CLI;
    }

    public void setEMAIL_CLI(String EMAIL_CLI) {
        this.EMAIL_CLI = EMAIL_CLI;
    }

    public void setID_CLIENTE(String idCliente) {
    }
}

