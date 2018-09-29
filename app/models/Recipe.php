<?php

use Phalcon\Mvc\Model;

class Recipe extends Model
{
    public $id;
    public $name;
    public $time;
    public $picture; 
    public $ingredients;
    public $instructions;
    public $inserted;
    public $views;
}
