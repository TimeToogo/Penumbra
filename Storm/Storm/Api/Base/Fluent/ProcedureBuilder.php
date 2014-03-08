<?php

namespace Storm\Api\Base\Fluent;

use \Storm\Core\Object;
use \Storm\Drivers\Fluent\Object\Procedure;
use \Storm\Core\Object\Expressions\ExpressionTree;
use \Storm\Drivers\Fluent\Object\IFunctionToExpressionTreeConverter;

/**
 * The ProcedureBuilder provides a fluent interface for building procedures
 * 
 * @author Elliot Levin <elliot@aanet.com.au>
 */
class ProcedureBuilder extends CriterionBuilder {
    private $ProcedureFunction;
    
    public function __construct(
            Object\IEntityMap $EntityMap, 
            IFunctionToExpressionTreeConverter $FunctionToExpressionTreeConverter,
            callable $ProcedureFunction) {
        parent::__construct($EntityMap, $FunctionToExpressionTreeConverter);
        
        $this->ProcedureFunction = $ProcedureFunction;
    }
    
    /**
     * Builds the procedure from specified parameters
     * 
     * @return Procedure
     */
    final public function BuildProcedure() {
        return new Procedure(
            $this->FunctionToExpressionTree($this->ProcedureFunction), 
            $this->BuildCriterion());
    }
}

?>