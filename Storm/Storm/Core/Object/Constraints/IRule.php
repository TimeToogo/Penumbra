<?php

namespace Storm\Core\Object\Constraints;

interface IRule {
    /**
     * @return Expressions\Expression[]
     */
    public function GetExpression();
}

?>