<?php

namespace Storm\Api\Base\Fluent;

use \Storm\Core\Object;
use \Storm\Drivers\Fluent\Object\Procedure;
use \Storm\Drivers\Fluent\Object\Closure;

class ProcedureBuilder extends CriterionBuilder {
    private $ProcedureClosure;
    
    public function __construct(
            Object\EntityMap $EntityMap, 
            Closure\ClosureToASTConverter $ClosureToASTConverter,
            \Closure $ProcedureClosure) {
        parent::__construct($EntityMap, $ClosureToASTConverter);
        
        $this->ProcedureClosure = $ProcedureClosure;
    }
    
    final public function BuildProcedure() {
        return new Procedure(
            $this->ClosureToExpandedAST($this->ProcedureClosure), 
            $this->BuildCriterion());
    }
}

?>