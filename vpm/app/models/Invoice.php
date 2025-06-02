<?php

namespace App\models;

class Invoice extends BaseModel
{
    public $id;
    public $cliente_id;
    public $factura;
    public $concepto;
    public $fecha;
    public $moneda;
    public $subtotal;
    public $iva;
    public $total;
    public $abono;
    public $nc;
    public $monto_nc;
    public $saldo_factura;
    public $proyecto;
    public $estatus;
    public $fecha_pago;
    public $vencimiento;
    public $vencidos;
    public $comentarios;
    public $complemento;
    public $al_corriente;
    public $rango_1_15;
    public $rango_16_30;
    public $rango_31_45;
    public $rango_46_60;
    public $rango_61_90;
    public $rango_mas_91;
}