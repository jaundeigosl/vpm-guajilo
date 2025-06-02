<?php

namespace App\models;

class CreditNote extends BaseModel
{
    public $id;
    public $cliente_id;
    public $nc;
    public $concepto;
    public $fecha;
    public $moneda;
    public $subtotal;
    public $iva;
    public $total;
    public $proyecto;
    public $comentario;
    public $estatus;
    public $comentarios_2;
}