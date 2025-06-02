<?php

namespace App\models;

class User extends BaseModel
{
    public $id;
    public $name;
    public $lastname;
    public $email;
    public $password;
    public $role_id;
    public $role_name;
    public $active;
}