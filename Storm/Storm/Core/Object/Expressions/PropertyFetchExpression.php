<?php

namespace Storm\Core\Object\Expressions;

/**
 * @author Elliot Levin <elliot@aanet.com.au>
 */
class PropertyFetchExpression extends Expression {
    private $Name;
    private $IsStatic;
    private $ClassType;
    private $ObjectOrNewExpression;
    
    public function __construct(Expression $ObjectOrNewExpression, $Name) {
        $this->Name = $Name;
        
        $IsObjectExpression = $ObjectOrNewExpression instanceof ObjectExpression;
        if(!$IsObjectExpression && !($ObjectOrNewExpression instanceof NewExpression)) {
            throw new \Exception();
        }
        
        $this->ObjectOrNewExpression = $ObjectOrNewExpression;
        $this->IsStatic = $IsObjectExpression ?
                !$ObjectOrNewExpression->HasInstance() : false;
        $this->ClassType = $IsObjectExpression ?
                $ObjectOrNewExpression->GetClassType() : $ObjectOrNewExpression->GetClassType();
    }
    
    /**
     * @return string
     */
    public function GetName() {
        return $this->Name;
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