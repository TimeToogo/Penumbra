<?php

namespace Storm\Drivers\Base\Object\Properties\Collections;

use \Storm\Core\Object\Domain;

class Collection extends \ArrayObject implements ICollection {
    private $EntityType;
    private $IsAltered = false;
    protected $OriginalEntities = array();
    private $RemovedEntities = array();
    
    public function __construct($EntityType, $Entities) {        
        parent::__construct($Entities);
        
        $this->EntityType = $EntityType;
        $this->OriginalEntities = $Entities;
    }
    
    final public function GetEntityType() {
        return $this->EntityType;
    }
    
    private function VerifyEntity($Entity) {
        if(!($Entity instanceof $this->EntityType)) {
            throw new \InvalidArgumentException('$Entity must be a valid instance of ' . $this->EntityType);
        }
    }

    final public function __GetRemovedEntities() {
        return $this->RemovedEntities;
    }

    public function __IsAltered() {
        return $this->IsAltered;
    }
    
    final public function __GetOriginalEntities() {
        return $this->OriginalEntities;
    }
    
    public function append($Entity) {
        $this->VerifyEntity($Entity);
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
        $this->VerifyEntity($Entity);
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
        $this->RemovedEntities[] = $this[$Index];
        return parent::offsetUnset($Index);
    }
}

?>
