<?php

namespace Storm\Drivers\Pinq\Object\Functional;

use \Storm\Core\Object;

interface IAST {
    public function GetEntityVariableName();
    
    /**
     * @return ExpressionTree
     */
    public function GetExpressionTree();
}

?>
