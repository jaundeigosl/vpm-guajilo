<?php

namespace App\models;

class AccumulatedCreditNote extends BaseModel
{
    public $id;
    public $nc;
    public $cliente_id;
    public $monto;
    public $moneda;
    public $responsable;
    public $motivo;
}