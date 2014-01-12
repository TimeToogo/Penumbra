<?php

namespace Storm\Drivers\Base\Object\Properties\Accessors;

abstract class FunctionBase {
    
    final protected function Format($FunctionName, array $Arguments) {
        return sprintf('%s(%s)', $FunctionName,
                implode(', ', 
                        array_map(
                            function ($Value) { 
                                return var_export($Value, true); 
                            },
                            $Arguments)));
    }
}

?>