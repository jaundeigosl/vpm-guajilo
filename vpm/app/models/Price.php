<?php

namespace App\models;

class Price extends BaseModel
{
    public $id;
    public $day;
    public $month;
    public $year;
    public $daily_hsc_price;
    public $daily_exchange_rate;
}
