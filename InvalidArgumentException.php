<?php

namespace RNW;
require_once('Psr/SimpleCache/InvalidArgumentException.php');
/**
 * Exception interface for invalid cache arguments.
 *
 * When an invalid argument is passed, it must throw an exception which implements
 * this interface.
 */
class InvalidArgumentException implements \Psr\SimpleCache\InvalidArgumentException
{
    private $arguments;
    
    function __construct($arguments, $type){
        die("{$arguments} не соответствуют типу {$type}");
    }
}