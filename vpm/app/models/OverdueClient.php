<?php

namespace App\models;

class OverdueClient extends BaseModel
{
    public $id;
    public $resumen_id;
    public $cliente_id;
    public $vencido_mxn;
    public $vencido_usd;
    public $total_mxn;
    public $recuperado_mxn;
    public $recuperado_usd;
}