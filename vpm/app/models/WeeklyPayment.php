<?php

namespace App\models;

class WeeklyPayment extends BaseModel
{
    public $id;
    public $fecha_inicio;
    public $fecha_fin;
    public $objetivo_semanal_mxn;
    public $objetivo_mensual_mxn;
    public $avance;
    public $pendiente;
    public $tc_prom;
}