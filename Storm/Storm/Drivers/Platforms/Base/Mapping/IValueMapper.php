<?php

namespace Storm\Drivers\Base\Mapping\Expressions;

use \Storm\Core\Relational;
use \Storm\Core\Object\Expressions as O;
use \Storm\Core\Relational\Expression;

interface IValueMapper {
    
    public function MapNull();
    
    public function MapScalar($Scalar);
}

?>