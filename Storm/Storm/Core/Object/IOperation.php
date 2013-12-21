<?php

namespace Storm\Core\Object;

interface IOperation extends IRequest {
    const IOperationType = __CLASS__;
    
    /**
     * @return Expressions\AssignmentExpression[]
     */
    public function GetExpressions();
}

?>