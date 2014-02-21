<?php

namespace Storm\Drivers\Constant\Helpers;

use \Storm\Core\Containers\Registrar;

trait PropertyReflection {
    final public function LoadRegistrarFromProperties(Registrar $Registrar) {
        $RegisterableType = $Registrar->GetRegisterableType();
        foreach($this->GetPublicPropertyValues() as $Value) {
            if($Value instanceof $RegisterableType) {
                $Registrar->Register($Value);
            }
        }
    }
    
    final protected function GetPublicPropertyValues($LateBound = true) {
        $Values = [];
        foreach($this->GetPublicProperties($LateBound) as $Property) {
            $Values[] = $Property->getValue($this);
        }
        
        return $Values;
    }
    final protected function GetPublicProperties($LateBound = true) {
        $Reflection = new \ReflectionClass($this);
        $Properties = $Reflection->getProperties(\ReflectionProperty::IS_PUBLIC);
        
        $LateBoundClass = get_class($this);
        foreach($Properties as $Key => $Property) {
            if($Property->isStatic()
                    || ($Property->getDeclaringClass()->getName() !== $LateBoundClass && $LateBound)) {
                unset($Properties[$Key]);
            }
        }
        
        return $Properties;
    }
}

?>
