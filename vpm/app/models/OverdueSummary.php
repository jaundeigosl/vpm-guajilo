<?php

namespace App\models;

class OverdueSummary extends BaseModel
{
    public $id;
    public $fecha;
    public $tipo_cambio;
    public $total_vencido_mxn;
    public $total_vencido_usd;
    public $objetivo_mensual;
    public $recuperado;
    public $avance;
}