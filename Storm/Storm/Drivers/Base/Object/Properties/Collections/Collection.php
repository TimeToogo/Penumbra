<?php

namespace Storm\Drivers\Base\Object\Properties\Collections;

use \Storm\Core\Object;

class Collection extends \ArrayObject implements ICollection {
    private $EntityType;
    private $IsAltered = false;
    protected $OriginalEntities = [];
    private $AddedEntities = [];
    private $RemovedEntities = [];
    
    public function __construct($EntityType, array $Entities = []) {        
        parent::__construct($Entities);
        
        $this->EntityType = $EntityType;
        $this->OriginalEntities = $Entities;
    }
    
    final public function GetEntityType() {
        return $this->EntityType;
    }
    
    private function VerifyEntity($Method, $Entity) {
        if(!($Entity instanceof $this->EntityType)) {
            throw new Object\TypeMismatchException(
                    'Supplied entity to %s must be of type %s: %s given',
                    $Method,
                    $this->EntityType,
                    \Storm\Core\Utilities::GetTypeOrClass($Entity));
        }
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
    
    final public function __GetOriginalEntities() {
        return $this->OriginalEntities;
    }
    final public function __GetNewEntities() {
        return $this->AddedEntities;
    }
    
    public function append($Entity) {
        $this->VerifyEntity(__METHOD__, $Entity);
        $this->IsAltered = true;
        return parent::append($Entity);
    }
    
    public function ToArray() {
        return $this->getArrayCopy();
    }

    public function exchangeArray($Input) {
        $this->IsAltered = true;
        $this->RemovedEntities = array_merge($this->RemovedEntities, $this->getArrayCopy());
        $this->AddedEntities = array_merge($this->AddedEntities, $Input);
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
        $this->VerifyEntity(__METHOD__, $Entity);
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
