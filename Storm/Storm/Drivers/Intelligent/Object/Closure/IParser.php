<?php

namespace Storm\Drivers\Intelligent\Object\Closure;

use \Storm\Core\Object;
use \Storm\Core\Object\Expressions\Expression;

interface IParser {
    
    /**
     * @return IAST
     */
    public function Parse(
            $ClosureBodySource,
            Object\EntityMap $EntityMap,
            $EntityVariableName);
}

?>
