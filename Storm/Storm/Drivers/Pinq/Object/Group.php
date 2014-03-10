<?php

namespace Storm\Drivers\Pinq\Object;


class Group implements IGroup {  
    private $ValueFunction;
    private $ValueFunction;
    private $IntoVariableName;
    
    public function __construct(callable $ValueFunction) {
        $this->ValueFunction = $ValueFunction;
    }
    
    /**
     * @return Expression
     */
    public function GetValueFunction() {
        return $this->ValueFunction;
    }
    
    /**
     * @return boolean
     */
    public function HasGroupByFunction() {
        return $this->ValueFunction !== null;
    }
    
    /**
     * @return callable|null
     */
    public function GetGroupByFunction() {
        return $this->ValueFunction;
    }
    
    public function SetGroupByExpression(callable $ValueFunction) {
        $this->ValueFunction = $ValueFunction;
    }
    
    /**
     * @return bool
     */
    public function HasIntoVariableName() {
        return $this->IntoVariableName !== null;
    }
    
    /**
     * @return string|null
     */
    public function GetIntoVariableName() {
        return $this->IntoVariableName;
    }

    public function SetIntoVariableName($IntoVariableName) {
        $this->IntoVariableName = $IntoVariableName;
    }
}

?>