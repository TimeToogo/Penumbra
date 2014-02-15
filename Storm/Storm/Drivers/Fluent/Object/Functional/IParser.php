<?php

namespace Storm\Drivers\Fluent\Object\Functional;

use \Storm\Core\Object;
use \Storm\Core\Object\Expressions\Expression;

interface IParser {
    
    /**
     * @return IAST
     */
    public function Parse(
            $BodySource,
            Object\IEntityMap $EntityMap,
            $EntityVariableName);
}

?>
