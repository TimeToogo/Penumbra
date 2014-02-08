<?php

namespace Storm\Core\Object\Expressions;

/**
 * Expression representing a class type or an instance value.
 * 
 * @author Elliot Levin <elliot@aanet.com.au>
 */
class ObjectExpression extends Expression {
    private $ClassType;
    private $HasInstance;
    private $Instance;
    
    public function __construct($InstanceOrType) {
        if(!is_object($InstanceOrType) && !class_exists($InstanceOrType)) {
            throw new \Storm\Core\Object\ObjectException(
                    'The supplied value must be either an object or a valid class name: %s given',
                    is_string($InstanceOrType) ? '\'' . $InstanceOrType . '\'': \Storm\Core\Utilities::GetTypeOrClass($InstanceOrType));
        }
        $this->HasInstance = is_object($InstanceOrType);
        $this->ClassType = $this->HasInstance ? get_class($InstanceOrType) : $InstanceOrType;
        $this->Instance = $this->HasInstance ? $InstanceOrType : null;
    }
    
    /**
     * @return string
     */
    public function GetClassType() {
        return $this->ClassType;
    }

    /**
     * @return boolean
     */
    public function HasInstance() {
        return $this->HasInstance;
    }
    
    /**
     * @return object|null
     */
    public function GetInstance() {
        return $this->Instance;
    }
}

?>