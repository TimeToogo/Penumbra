<?php

namespace Storm\Drivers\Platforms\Standard\Mapping;

use \Storm\Drivers\Platforms\Base\Mapping;
use \Storm\Core\Object\Expressions as O;
use \Storm\Drivers\Base\Relational\Expressions as R;

class ValueMapper extends Mapping\ValueMapper {
    public function MapNull() {
        return R\Expression::BoundValue(null);
    }

    public function MapScalar($Scalar) {
        return R\Expression::BoundValue($Scalar);
    }
    
    public function MapResource($Resource) {
        return R\Expression::BoundValue($Resource);
    }
}

?>