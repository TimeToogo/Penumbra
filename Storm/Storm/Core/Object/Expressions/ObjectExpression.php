<?php

namespace Storm\Core\Object\Expressions;

class ObjectExpression extends Expression {
    private $ClassType;
    private $HasInstance;
    private $Instance;
    
    public function __construct($InstanceOrType) {
        if(!is_object($InstanceOrType) && !class_exists($InstanceOrType)) {
            throw new \Exception();
        }
        $this->HasInstance = is_object($InstanceOrType);
        $this->ClassType = $this->HasInstance ? get_class($InstanceOrType) : $InstanceOrType;
        $this->Instance = $this->HasInstance ? $InstanceOrType : null;
    }
    
    public function GetClassType() {
        return $this->ClassType;
    }

    public function HasInstance() {
        return $this->HasInstance;
    }
    
    public function GetInstance() {
        return $this->Instance;
    }
}

?>