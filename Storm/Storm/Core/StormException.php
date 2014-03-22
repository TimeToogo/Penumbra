<?php

namespace Storm\Core;

class StormException extends \Exception {
    private static $Reflection = null;
    
    /**
     * @var \Exception 
     */
    private $InnerException;
    
    /**
     * @param \Exception $InnerException
     * @param type $MessageFormat
     * @param mixed ... The values to interpole the message with
     */
    public function __construct($MessageFormat = '', $_ = null) {
        if(func_num_args() === 1) {
            $Message = $MessageFormat;
        }
        else {
            $Message = call_user_func_array('sprintf', array_merge([$MessageFormat], array_slice(func_get_args(), 1)));
        }
        
        parent::__construct($Message, null, null);
    }
    
    final public static function Construct(array $Arguments) {
        $Message = call_user_func_array('sprintf', $Arguments);
        return new static($Message);
    }
    
    final public function GetInnerException() {
        return $this->InnerException;
    }
    
    final public function SetInnerException(\Exception $InnerException) {
        $this->InnerException = $InnerException;
    }
}

?>
