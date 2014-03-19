<?php

namespace Storm\Core\Object;

/**
 * The procedure represents a set of assigments that should be carried out upon
 * a set entities defined by the criteria.
 * 
 * @author Elliot Levin <elliot@aanet.com.au>
 */
interface IProcedure extends IQuery {
    const IProcedureType = __CLASS__;
    
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