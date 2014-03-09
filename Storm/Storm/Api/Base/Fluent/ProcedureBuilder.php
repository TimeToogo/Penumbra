<?php

namespace Storm\Api\Base\Fluent;

use \Storm\Api\Base\Repository;
use \Storm\Core\Object;
use \Storm\Drivers\Pinq\Object\Procedure;
use \Storm\Drivers\Pinq\Object\Functional\ExpressionTree;
use \Storm\Drivers\Pinq\Object\IFunctionToExpressionTreeConverter;

/**
 * The ProcedureBuilder provides a fluent interface for building procedures
 * 
 * @author Elliot Levin <elliot@aanet.com.au>
 */
class ProcedureBuilder extends CriterionBuilder {
    private $Repository;
    private $ProcedureFunction;
    
    public function __construct(
            Repository $Repository,
            Object\IEntityMap $EntityMap, 
            IFunctionToExpressionTreeConverter $FunctionToExpressionTreeConverter,
            callable $ProcedureFunction) {
        parent::__construct($EntityMap, $FunctionToExpressionTreeConverter);
        
        $this->Repository = $Repository;
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
    
    /**
     * @return void
     */
    final public function Execute() {
        $this->Repository->Execute($this->BuildProcedure());
    }
}

?>