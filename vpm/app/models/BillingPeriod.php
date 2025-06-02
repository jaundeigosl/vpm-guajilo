<?php

namespace App\models;

class BillingPeriod extends BaseModel
{
    public $id;
    public $name;
    public $start_date;
    public $end_date;
    public $duration_days;
    public $active;
}
