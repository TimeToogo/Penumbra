<?php

namespace Storm\Drivers\Base\Object;

use \Storm\Core\Object;
use \Storm\Core\Object\Expressions\AssignmentExpression;

class Procedure implements Object\IProcedure {
    private $EntityType;
    private $AssignmentExpressions;
    /**
     * @var Object\ICriterion
     */
    private $Criterion;
    
    public function __construct($EntityOrType, array $AssignmentExpressions, Object\ICriterion $Criterion = null) {
        if(count($AssignmentExpressions) === 0) {
            throw new \Exception('Must have atleast one assignment expression');
        }
        
        $this->EntityType = $EntityOrType;
        $this->AssignmentExpressions = $AssignmentExpressions;
        $this->Criterion = $Criterion ?: new Criterion();
    }
    
    final public function GetEntityType() {
        return $this->EntityType;;
    }
    
    final public function GetExpressions() {
        return $this->AssignmentExpressions;
    }

    public function GetCriterion() {
        return $this->Criterion;
    }
}

?>