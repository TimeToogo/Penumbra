<?php

namespace Storm\Api\Base\Fluent;

use \Storm\Core\Object;
use \Storm\Api\Base\FunctionToASTConverter;
use \Storm\Drivers\Fluent\Object\Procedure;
use \Storm\Drivers\Fluent\Object\Functional;

/**
 * The ProcedureBuilder provides a fluent interface for building procedures
 * 
 * @author Elliot Levin <elliot@aanet.com.au>
 */
class ProcedureBuilder extends CriterionBuilder {
    private $ProcedureFunction;
    
    public function __construct(
            Object\IEntityMap $EntityMap, 
            FunctionToASTConverter $ClosureToASTConverter,
            callable $ProcedureFunction) {
        parent::__construct($EntityMap, $ClosureToASTConverter);
        
        $this->ProcedureFunction = $ProcedureFunction;
    }
    
    /**
     * Builds the procedure from specified parameters
     * 
     * @return Procedure
     */
    final public function BuildProcedure() {
        return new Procedure(
            $this->FunctionToExpandedAST($this->ProcedureFunction), 
            $this->BuildCriterion());
    }
}

?>