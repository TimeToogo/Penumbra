<?php

namespace Storm\Drivers\Base\Mapping\Collections;

class LazyCollection extends Collection {
    private $ArrayLoaderFunction;
    private $IsLoaded = false;
    
    public function __construct(callable $ArrayLoaderFunction, $EntityType) {
        parent::__construct(array(), $EntityType);
        $this->ArrayLoaderFunction = $ArrayLoaderFunction;
    }
    private function Load() {
        if($this->IsLoaded)
            return;
        else
            $this->IsLoaded = true;
        
        $Loader = $this->ArrayLoaderFunction;
        $Array = $Loader();
        $this->exchangeArray($Array);
        $this->OriginalEntities = $Array;
    }
    
    final public function __IsLoaded() {
        return $this->IsLoaded;
    }
    public function __IsAltered() {
        if(!$this->IsLoaded)
            return false;
        else
            return parent::__IsAltered();
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
