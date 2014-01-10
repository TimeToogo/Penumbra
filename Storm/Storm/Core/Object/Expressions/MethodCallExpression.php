<?php

namespace Storm\Core\Object\Expressions;

class MethodCallExpression extends FunctionCallExpression {
    private $IsStatic;
    private $ClassType;
    private $ObjectOrNewExpression;
    public function __construct(Expression $ObjectOrNewExpression, $Name, array $ArgumentValueExpressions = array()) {
        parent::__construct($Name, $ArgumentValueExpressions);
        
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
    
    public function IsStatic() {
        return $this->IsStatic;
    }
    
    public function GetClassType() {
        return $this->ClassType;
    }
    
    /**
     * @return Expression
     */
    public function GetObjectExpression() {
        return $this->ObjectOrNewExpression;
    }
}

?>