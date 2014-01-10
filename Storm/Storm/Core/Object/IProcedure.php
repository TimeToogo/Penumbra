<?php

namespace Storm\Core\Object;

interface IProcedure {
    const IProcedureType = __CLASS__;
    
    public function GetEntityType();
    
    /**
     * @return Expressions\AssignmentExpression[]
     */
    public function GetExpressions();
    
    /**
     * @return ICriterion
     */
    public function GetCriterion();
}

?>