<?php

namespace Storm\Drivers\Platforms\Base;

use \Storm\Drivers\Base\Relational;

class UnsupportedColumnTypeException extends Relational\PlatformException {
    public function __construct($PlatformName, $ColumnType) {
        parent::__construct('%s does not support the given column type: %s', $PlatformName, $ColumnType);
    }
}

?>