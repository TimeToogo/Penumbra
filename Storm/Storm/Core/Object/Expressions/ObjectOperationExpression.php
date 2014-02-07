<?php

namespace Storm\Core\Object\Expressions;

/**
 * @author Elliot Levin <elliot@aanet.com.au>
 */
abstract class ObjectOperationExpression extends Expression {
    private $IsStatic;
    private $ClassType;
    private $ObjectOrNewExpression;
    
    public function __construct(Expression $ObjectOrNewExpression) {
        $IsObjectExpression = $ObjectOrNewExpression instanceof ObjectExpression;
        if(!$IsObjectExpression && !($ObjectOrNewExpression instanceof NewExpression)) {
            throw new \Storm\Core\Object\ObjectException(
                    'The supplied expression must be of the type %s or %s: %s given',
                    ObjectExpression::GetType(),
                    NewExpression::GetType(),
                    get_class($ObjectOrNewExpression));
        }
        
        $this->ObjectOrNewExpression = $ObjectOrNewExpression;
        $this->IsStatic = $IsObjectExpression ?
                !$ObjectOrNewExpression->HasInstance() : false;
        $this->ClassType = $IsObjectExpression ?
                $ObjectOrNewExpression->GetClassType() : $ObjectOrNewExpression->GetClassType();
    }

    /**
     * @return boolean
     */
    public function IsStatic() {
        return $this->IsStatic;
    }
    
    /**
     * @return string
     */
    public function GetClassType() {
        return $this->ClassType;
    }
    
    /**
     * @return ObjectExpression|NewExpression
     */
    public function GetObjectExpression() {
        return $this->ObjectOrNewExpression;
    }
}

?>