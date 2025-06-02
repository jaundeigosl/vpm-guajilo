<?php

namespace App\models;

class PaymentProfile extends BaseModel
{
    public $id;
    public $cliente_id;
    public $dias_credito_molecula;
    public $dias_credito_servicio;
}