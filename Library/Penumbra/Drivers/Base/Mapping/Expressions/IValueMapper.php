<?php

namespace Penumbra\Drivers\Base\Mapping\Expressions;

use \Penumbra\Core\Relational;
use \Penumbra\Core\Object\Expressions as O;
use \Penumbra\Drivers\Base\Relational\Expressions as R;

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