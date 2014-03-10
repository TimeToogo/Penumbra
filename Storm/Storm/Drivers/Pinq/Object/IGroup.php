<?php

namespace Storm\Drivers\Pinq\Object;

interface IGroup {        
    /**
     * @return callable
     */
    public function GetValueFunction();
    
    /**
     * @return boolean
     */
    public function HasGroupByFunction();
    
    /**
     * @return callable|null
     */
    public function GetGroupByFunction();
    
    /**
     * @return bool
     */
    public function HasIntoVariableName();
    
    /**
     * @return string|null
     */
    public function GetIntoVariableName();
}

?>