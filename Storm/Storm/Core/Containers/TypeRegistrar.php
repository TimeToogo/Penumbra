<?php

namespace Storm\Core\Containers;

class TypeRegistrar {
    private $RegistrableType;
    private $Types = array();
    
    function __construct($RegistrableType = null) {
        $this->RegistrableType = $RegistrableType;
    }

    public function GetRegistrableType() {
        return $this->RegistrableType;
    }

    public function Register(&$Type) {
        if($this->RegistrableType !== null)
            if(!is_subclass_of($Type, $this->RegistrableType))
                throw new \InvalidArgumentException
                    ('$Type must be a subclass of ' . $this->GetRegistrableType());
        
        $this->Types[] = &$Type;
    }

    public function getIterator() {
        return new \ArrayIterator($this->Types);
    }
}

?>
