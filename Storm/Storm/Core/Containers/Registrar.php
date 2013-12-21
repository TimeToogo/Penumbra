<?php

namespace Storm\Core\Containers;

final class Registrar {
    private $RegistrableType;
    private $Instances = array();
    
    public function __construct($RegistrableType = null) {
        $this->RegistrableType = $RegistrableType;
    }

    public function GetRegisterableType() {
        return $this->RegistrableType;
    }

    public function Register($Instance) {
        if($this->RegistrableType !== null) {
            if(!($Instance instanceof $this->RegistrableType))
                throw new \InvalidArgumentException
                    ('$Instance must be an instance of ' . $this->RegistrableType);
        }
        
        $this->Instances[] = $Instance;
    }

    public function RegisterAll(array $Instances) {
        foreach($Instances as $Instance) {
            $this->Register($Instance);
        }
    }
    
    public function GetRegistered() {
        return $this->Instances;
    }
}

?>
