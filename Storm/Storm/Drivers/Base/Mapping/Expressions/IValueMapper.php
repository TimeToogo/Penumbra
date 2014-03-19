<?php

namespace Storm\Drivers\Base\Mapping\Expressions;

use \Storm\Core\Relational;
use \Storm\Core\Object\Expressions as O;
use \Storm\Drivers\Base\Relational\Expressions as R;

interface IValueMapper {
    
    /**
     * @return R\Expression
     */
    public function MapNull();
    
    /**
     * @return R\Expression
     */
    public function MapScalar($Scalar);
    
    /**
     * @return R\Expression
     */
    public function MapResource($Resource);
}

?>