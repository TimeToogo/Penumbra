<?php

namespace Storm\Api\Base;

use \Storm\Drivers\Fluent\Object\Functional;

class InvalidFunctionException extends \Storm\Core\StormException {    
    public function __construct(Functional\IData $FunctionData, $Message) {
        $Reflection = $FunctionData->GetReflection();
        
        parent::__construct(
                'Invalid function defined in %s (%d-%d): %s', 
                $Reflection->getFileName(),
                $Reflection->getStartLine(),
                $Reflection->getEndLine(),
                $Message);
    }
}

?>