<?php

namespace Storm\Drivers\Base\Object;

use \Storm\Core\Object;
use \Storm\Core\Object\Expressions\AssignmentExpression;

class Procedure implements Object\IProcedure {
    private $EntityType;
    private $AssignmentExpressions;
    /**
     * @var Object\ICriteria
     */
    private $Criteria;
    
    public function __construct($EntityOrType, array $AssignmentExpressions, Object\ICriteria $Criteria = null) {
        if(count($AssignmentExpressions) === 0) {
            throw new Object\ObjectException(
                    'Procedure must contain atleast one assignment expression: none given');
        }
        
        $this->EntityType = is_object($EntityOrType) ? get_class($EntityOrType) : $EntityOrType;
        $this->AssignmentExpressions = $AssignmentExpressions;
        $this->Criteria = $Criteria ?: new Criteria($this->EntityType);
        
        if($this->Criteria->GetEntityType() !== $this->EntityType) {
            throw new Object\TypeMismatchException(
                    'The supplied criteria must be for %s, %s given',
                    $this->EntityType,
                    $this->Criteria->GetEntityType());
        }
    }
    
    final public function GetEntityType() {
        return $this->EntityType;
    }
    
    final public function GetExpressions() {
        return $this->AssignmentExpressions;
    }

    public function GetCriteria() {
        return $this->Criteria;
    }
}

?>