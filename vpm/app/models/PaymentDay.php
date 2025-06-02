<?php

namespace App\models;

class PaymentDay extends BaseModel
{
    public $id;
    public $semana_id;
    public $dia;
    public $moneda;
    public $monto;
}