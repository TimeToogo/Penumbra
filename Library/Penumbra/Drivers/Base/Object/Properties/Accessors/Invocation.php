<?php

namespace Penumbra\Drivers\Base\Object\Properties\Accessors;

class Invocation extends GetterSetter {
    public function __construct(array $ConstantArguments = []) {
        parent::__construct(
                new InvocationGetter($ConstantArguments), 
                new InvocationSetter($ConstantArguments));
    }
}

?>
