<?php

namespace Penumbra\Drivers\Platforms\Base;

use \Penumbra\Drivers\Platforms;
use \Penumbra\Drivers\Base\Relational;

class UnsupportedKeyGeneratorTypeException extends Relational\PlatformException {
    public function __construct($PlatformName, $KeyGeneratorType) {
        parent::__construct('%s does not support %s key generators', $PlatformName, $KeyGeneratorType);
    }
}

?>