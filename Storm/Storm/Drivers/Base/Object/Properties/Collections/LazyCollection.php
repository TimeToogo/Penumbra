<?php

namespace Storm\Drivers\Base\Object\Properties\Collections;

use \Storm\Core\Object\Domain;

class LazyCollection extends Collection {
    private $Domain;
    private $ArrayLoaderFunction;
    private $IsLoaded = false;
    
    public function __construct(Domain $Domain, $EntityType, callable $ArrayLoaderFunction) {
        parent::__construct($EntityType, array());
        $this->ArrayLoaderFunction = $ArrayLoaderFunction;
        $this->Domain = $Domain;
    }
    private function Load() {
        if($this->IsLoaded) {
            return;
        }
        else {
            $this->IsLoaded = true;
        }
        
        $Loader = $this->ArrayLoaderFunction;
        $RevivalData = $Loader();
        $Entities = $this->Domain->ReviveEntities($this->GetEntityType(), $RevivalData);
        $this->exchangeArray($Entities);
        $this->SetIsAltered(false);
        $this->OriginalEntities = $Entities;
    }
    
    final public function __IsLoaded() {
        return $this->IsLoaded;
    }
    
    public function count() {
        $this->Load();
        return parent::count();
    }
    
    public function ToArray() {
        $this->Load();
        return parent::ToArray();
    }

    public function getIterator() {
        $this->Load();
        return parent::getIterator();
    }

    public function offsetExists($index) {
        $this->Load();
        return parent::offsetExists($index);
    }

    public function offsetGet($index) {
        $this->Load();
        return parent::offsetGet($index);
    }

    public function offsetSet($index, $newval) {
        $this->Load();
        return parent::offsetSet($index, $newval);
    }

    public function offsetUnset($index) {
        $this->Load();
        return parent::offsetUnset($index);
    }

}

?>
