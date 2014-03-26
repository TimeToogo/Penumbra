<?php

namespace Penumbra\Core\Containers;

/**
 * This class provides simple interface to a type safe and write-only collection.
 * 
 * @author Elliot Levin <elliot@aanet.com.au>
 */
final class Registrar {
    private $RegistrableType;
    private $Instances = [];
    
    public function __construct($RegistrableType = null) {
        $this->RegistrableType = $RegistrableType;
    }

    public function GetRegisterableType() {
        return $this->RegistrableType;
    }

    public function Register($Instance) {
        if($this->RegistrableType !== null) {
            if(!($Instance instanceof $this->RegistrableType)) {
                throw new \Penumbra\Core\Object\TypeMismatchException
                        ('Registered type does not match, expecting %s, supplied %s', 
                        $this->RegistrableType, \Penumbra\Utilities\Type::GetTypeOrClass($Instance));
            }
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
