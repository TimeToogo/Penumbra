<?php

namespace Storm\Drivers\Base\Relational\Syncing\Traits;

final class TraitMethodManager {
    private $AppenderFunctions = array();
    
    final public function Register($TraitType, callable $AppendDefinitionFunction) {
        $this->AppenderFunctions[$TraitType] = $AppendDefinitionFunction;
    }

    final public function GetRegisteredFunction($Trait) {
        $TraitType = $Trait->GetType();
        $AppenderFunction = null;
        if(!isset($this->AppenderFunctions[$TraitType])) {
            foreach(array_keys($this->AppenderFunctions) as $Type){
                if($Trait instanceof $Type)
                    $AppenderFunction = $this->AppenderFunctions[$Type];
            }
            if($AppenderFunction === null)
                throw new \BadMethodCallException('The supplied Trait type is unsupported');
        }
        else {
            $AppenderFunction = $this->AppenderFunctions[$TraitType];
        }
        
        return $AppenderFunction;
    }
}

?>