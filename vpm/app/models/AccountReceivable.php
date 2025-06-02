<?php

namespace App\models;

class AccountReceivable extends BaseModel
{
    public $id;
    public $cliente_id;
    public $moneda;
    public $al_corriente;
    public $rango_1_15;
    public $rango_16_30;
    public $rango_31_45;
    public $rango_46_60;
    public $rango_61_90;
    public $rango_mas_91;
    public $saldo_total;
}