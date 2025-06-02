<?php

namespace App\models;

abstract class BaseModel
{
    public $id;
    public $created_at;
    public $updated_at;

    public function __construct(array $data = [])
    {
        foreach ($data as $key => $value) {
            if (property_exists($this, $key)) {
                $this->$key = $value;
            }
        }
    }
}