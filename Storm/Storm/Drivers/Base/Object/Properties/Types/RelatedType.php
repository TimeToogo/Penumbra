<?php

namespace Storm\Drivers\Base\Object\Properties\Types;

use \Storm\Core\Object\Domain;
use \Storm\Core\Object\RevivalData;

class RelatedType extends PropertyType {
    private $EntityType;
    
    public function __construct($EntityType) {
        $this->EntityType = $EntityType;
    }

    final public function GetEntityType() {
        return $this->EntityType;
    }
    
    final public function ReviveValue(Domain $Domain, $Entity, $PropertyRevivalValue) {
        if($PropertyRevivalValue === null) {
            return $this->ReviveNull($Domain, $Entity);
        }
        if($PropertyRevivalValue instanceof RevivalData) {
            return $this->ReviveRevivalData($Domain, $Entity, $PropertyRevivalValue);
        }
        else if(is_callable($PropertyRevivalValue)) {
            return $this->ReviveCallable($Domain, $Entity, $PropertyRevivalValue);
        }
        else if(is_array($PropertyRevivalValue)) {
            if(count(array_filter($PropertyRevivalValue, function ($Value) { return $Value instanceof Object\RevivalData; })) === count($PropertyRevivalValue)) {
                return $this->ReviveRevivalData($Domain, $Entity, $PropertyRevivalValue);
            }
            else if(count(array_filter($PropertyRevivalValue, function ($Value) { return is_callable($Value); })) === count($PropertyRevivalValue)) {
                return $this->ReviveArrayOfCallables($Domain, $Entity, $PropertyRevivalValue);
            }
        }
        
        throw new Exception;//TODO:error message
    }
    
    protected function ReviveNull(Domain $Domain, $Entity) {
        throw new Exception;//TODO:error message
    }
    
    protected function ReviveRevivalData(Domain $Domain, $Entity, RevivalData $RevivalData) {
        throw new Exception;//TODO:error message
    }
    
    protected function ReviveCallable(Domain $Domain, $Entity, callable $Callback) {
        throw new Exception;//TODO:error message
    }
    
    protected function ReviveArrayOfRevivalData(Domain $Domain, $Entity, array $RevivalDataArray) {
        throw new Exception;//TODO:error message
    }
    
    protected function ReviveArrayOfCallables(Domain $Domain, $Entity, array $Callbacks) {
        throw new Exception;//TODO:error message
    }
}

?>
