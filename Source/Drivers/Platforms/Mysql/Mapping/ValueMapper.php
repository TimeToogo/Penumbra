<?php

namespace Penumbra\Drivers\Platforms\Mysql\Mapping;

use \Penumbra\Drivers\Platforms\Standard\Mapping;
use \Penumbra\Core\Object\Expressions as O;
use \Penumbra\Drivers\Base\Relational\Expressions as R;

class ValueMapper extends Mapping\ValueMapper {
    public function MapScalar($Scalar) {
        return parent::MapScalar($Scalar);
    }
}

?>