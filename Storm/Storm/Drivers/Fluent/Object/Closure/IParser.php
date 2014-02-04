<?php

namespace Storm\Drivers\Fluent\Object\Closure;

use \Storm\Core\Object;
use \Storm\Core\Object\Expressions\Expression;

interface IParser {
    
    /**
     * @return IAST
     */
    public function Parse(
            $ClosureBodySource,
            Object\IEntityMap $EntityMap,
            $EntityVariableName);
}

?>
