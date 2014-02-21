<?php

namespace Storm\Drivers\Base\Object\Properties\Accessors;

use \Storm\Core\Object;

class InvocationBase extends MethodBase {
    
    public function __construct(array $ConstantArguments = []) {
        parent::__construct('__invoke', $ConstantArguments);
    }
}

?>