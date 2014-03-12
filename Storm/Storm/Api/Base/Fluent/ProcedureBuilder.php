<?php

namespace Storm\Api\Base\Fluent;

use \Storm\Api\Base\Repository;
use \Storm\Core\Object;
use \Storm\Pinq\Procedure;
use \Storm\Pinq\Functional\ExpressionTree;
use \Storm\Pinq\IFunctionToExpressionTreeConverter;

/**
 * The ProcedureBuilder provides a fluent interface for building procedures
 * 
 * @author Elliot Levin <elliot@aanet.com.au>
 */
class ProcedureBuilder extends CriteriaBuilder {
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
            $this->EntityMap,
            $this->FunctionToExpressionTreeConverter,
            $this->ProcedureFunction, 
            $this->BuildCriteria());
    }
    
    /**
     * @return void
     */
    final public function Execute() {
        $this->Repository->Execute($this->BuildProcedure());
    }
}

?>