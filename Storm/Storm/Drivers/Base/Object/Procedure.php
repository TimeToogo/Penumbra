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
            throw new Object\ObjectException(
                    'Procedure must contain atleast one assignment expression: none given');
        }
        
        $this->EntityType = is_object($EntityOrType) ? get_class($EntityOrType) : $EntityOrType;
        $this->AssignmentExpressions = $AssignmentExpressions;
        $this->Criterion = $Criterion ?: new Criterion($this->EntityType);
        
        if($this->Criterion->GetEntityType() !== $this->EntityType) {
            throw new Object\TypeMismatchException(
                    'The supplied criterion must be for %s, %s given',
                    $this->EntityType,
                    $this->Criterion->GetEntityType());
        }
    }
    
    final public function GetEntityType() {
        return $this->EntityType;
    }
    
    final public function GetExpressions() {
        return $this->AssignmentExpressions;
    }

    public function GetCriterion() {
        return $this->Criterion;
    }
}

?>