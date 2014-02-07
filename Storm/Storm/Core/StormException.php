<?php

namespace Storm\Core;

class StormException extends \Exception {
    /**
     * @var \Exception 
     */
    private $InnerException;
    
    /**
     * @param \Exception $InnerException
     * @param type $MessageFormat
     * @param mixed ... The values to interpole the message with
     */
    public function __construct($MessageFormat, $_) {
        $Message = call_user_func_array('sprintf', array_merge([$MessageFormat], array_slice(func_get_args(), 3)));
        
        parent::__construct($Message, null, null);
    }
    
    final public function Forward(array $Arguments) {
        return call_user_func_array(['self', '__construct'], $Arguments);
    }
    
    final public function GetInnerException() {
        return $this->InnerException;
    }
    
    final public function SetInnerException(\Exception $InnerException) {
        $this->InnerException = $InnerException;
    }
}

?>
