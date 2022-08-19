<?php

abstract class Car {
  public $name;
  public function __construct($name) {
    $this->name = $name;
  }

}

// Create objects from the child classes


$citroen = new Car("Citroen");
echo $citroen->name;