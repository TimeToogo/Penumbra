<?php

namespace Storm\Api\Base\Fluent;

use \Storm\Core\Object;
use \Storm\Drivers\Intelligent\Object\Pinq\Procedure;

class ProcedureBuilder extends CriterionBuilder {
    private $EntityMap;
    private $ProcedureClosure;
    
    public function __construct(Object\EntityMap $EntityMap, \Closure $ProcedureClosure) {
        parent::__construct($EntityMap);
        $this->EntityMap = $EntityMap;
        $this->ProcedureClosure = $ProcedureClosure;
    }
    
    final public function BuildProcedure() {
        return new Procedure(
            $this->EntityMap, 
            $this->ProcedureClosure, 
            $this->BuildCriterion());
    }
}

?>