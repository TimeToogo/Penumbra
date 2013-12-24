<?php

namespace Storm\Core\Object;

interface IProcedure extends IRequest {
    const IProcedureType = __CLASS__;
    
    /**
     * @return Expressions\AssignmentExpression[]
     */
    public function GetExpressions();
}

?>