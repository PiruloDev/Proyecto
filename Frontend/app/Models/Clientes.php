<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Clientes extends Model
{
    protected $table = 'Clientes';
    protected $primaryKey = 'ID_CLIENTE';
    public $timestamps = false;

    protected $fillable = [
        'NOMBRE_CLI',
        'TELEFONO_CLI',
        'ACTIVO_CLI',
        'EMAIL_CLI',
        'CONTRASENA_CLI'
    ];
}
