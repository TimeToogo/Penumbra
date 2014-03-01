<?php

namespace Storm\Core\Object\Expressions;

/**
 * @author Elliot Levin <elliot@aanet.com.au>
 */
abstract class ObjectOperationExpression extends MemberExpression {
    private $IsStatic;
    private $ClassType;
    
    public function __construct(Expression $ObjectValueExpression) {
        parent::__construct($ObjectValueExpression);
        $this->ObjectValueExpression = $ObjectValueExpression;
        $this->IsStatic = $IsObjectExpression ?
                !$ObjectValueExpression->HasInstance() : false;
        $this->ClassType = $IsObjectExpression ?
                $ObjectValueExpression->GetClassType() : $ObjectValueExpression->GetClassType();
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
}

?>