<?php

namespace Storm\Drivers\Platforms\Base;

use \Storm\Drivers\Platforms;
use \Storm\Drivers\Base\Relational;

class UnsupportedKeyGeneratorTypeException extends Relational\PlatformException {
    public function __construct($PlatformName, $KeyGeneratorType) {
        parent::__construct('%s does not support %s key generators', $PlatformName, $KeyGeneratorType);
    }
}

?>