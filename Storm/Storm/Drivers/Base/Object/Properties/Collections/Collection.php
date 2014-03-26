<?php

namespace Storm\Drivers\Base\Object\Properties\Collections;

use \Storm\Core\Object;

class Collection extends \ArrayObject implements ICollection {
    private $EntityType;
    private $IsAltered = false;
    
    public function __construct($EntityType, array $Entities = []) {        
        parent::__construct($Entities);
        
        $this->EntityType = $EntityType;
    }
    
    final public function GetEntityType() {
        return $this->EntityType;
    }

    final public function __GetRemovedEntities() {
        return $this->RemovedEntities;
    }

    final public function __IsAltered() {
        return $this->IsAltered;
    }
    final protected function SetIsAltered($IsAltered) {
        $this->IsAltered = $IsAltered;
    }
    
    public function append($Entity) {
        $this->IsAltered = true;
        return parent::append($Entity);
    }
    
    public function ToArray() {
        return $this->getArrayCopy();
    }

    public function exchangeArray($Input) {
        $this->IsAltered = true;
        parent::exchangeArray($Input);
    }
    
    public function offsetExists($Index) {
        if($Index === null) {
            return false;
        }
        return parent::offsetExists($Index);
    }

    public function offsetGet($Index) {
        return parent::offsetGet($Index);
    }

    public function offsetSet($Index, $Entity) {
        if(isset($this[$Index])) {
            if($this[$Index] === $Entity) {
                return;
            }
        }
        $this->IsAltered = true;
        return parent::offsetSet($Index, $Entity);
    }

    public function offsetUnset($Index) {
        if(!isset($this[$Index]))
            return;
        
        $this->IsAltered = true;
        return parent::offsetUnset($Index);
    }
}

?>