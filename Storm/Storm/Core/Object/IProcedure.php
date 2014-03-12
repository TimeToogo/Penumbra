<?php

namespace Storm\Core\Object;

/**
 * The procedure represents a set of assigments that should be carried out upon
 * a variable amount of entities defined by a criteria.
 * 
 * @author Elliot Levin <elliot@aanet.com.au>
 */
interface IProcedure {
    const IProcedureType = __CLASS__;
    
    /**
     * @return string
     */
    public function GetEntityType();
    
    /**
     * @return Expressions\AssignmentExpression[]
     */
    public function GetExpressions();
    
    /**
     * @return ICriteria
     */
    public function GetCriteria();
}

?>