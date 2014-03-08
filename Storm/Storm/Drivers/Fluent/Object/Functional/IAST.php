<?php

namespace Storm\Drivers\Fluent\Object\Functional;

use \Storm\Core\Object;
use Storm\Core\Object\Expressions\ExpressionTree;

interface IAST {
    public function GetEntityVariableName();
    
    /**
     * @return ExpressionTree
     */
    public function GetExpressionTree();
}

?>
