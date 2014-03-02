<?php

namespace Storm\Drivers\Base\Object\Properties\Accessors;

abstract class InvocationBase extends MethodBase {
    
    public function __construct(array $ConstantArguments = []) {
        parent::__construct('__invoke', $ConstantArguments);
    }
    
    public function Identifier(&$Identifier) {
        $Identifier .= sprintf('(%s)',
                 implode(', ', array_map(function ($I) { return var_export($I, true); }, $this->ConstantArguments)));
    }
}

?>