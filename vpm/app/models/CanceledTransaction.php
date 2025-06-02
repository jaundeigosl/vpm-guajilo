<?php

namespace App\models;

class CanceledTransaction extends BaseModel
{
    public $id;
    public $tipo;
    public $cliente_id;
    public $motivo;
    public $comentarios;
}