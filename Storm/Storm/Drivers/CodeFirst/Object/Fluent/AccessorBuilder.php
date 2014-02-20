<?php

namespace Storm\Drivers\CodeFirst\Object\Fluent;

use \Storm\Drivers\Base\Object\Properties\Accessors;

class AccessorBuilder {
    /**
     * @var Accessors\Accessor
     */
    private $Accessor;
    
    /**
     * @return Accessors\Accessor|null
     */
    public function BuildAccessor() {
        if($this->Accessor === null) {
            throw new \Storm\Core\Object\ObjectException('Accessor has not been defined');
        }
        
        return $this->Accessor;
    }
    
    private function AddAccessor(Accessors\Accessor $NewAccessor) {
        if($this->Accessor === null) {
            $this->Accessor = $NewAccessor;
        }
        else if (!($this->Accessor instanceof Accessors\Traversing)) {
            $this->Accessor = new Accessors\Traversing([$this->Accessor, $NewAccessor]);
        }
        else {
            $this->Accessor = new Accessors\Traversing(
                    array_merge($this->Accessor->GetNestedAccessors(), [$NewAccessor]));
        }
    }
    
    /**
     * Defines the property to be accessed as a field
     * 
     * @param string $Name
     */
    public function Field($Name) {
        $this->AddAccessor(new Accessors\Field($Name));
        return $this;
    }
    
    /**
     * Defines the property to be accessed by getter/setter methods
     * 
     * @param string $GetterName
     * @param string $SetterName
     */
    public function Methods($GetterName, $SetterName) {
        $this->AddAccessor(new Accessors\MethodPair($GetterName, $SetterName));
        return $this;
    }
    
    /**
     * Defines the property to be accessed by a standard getter/setter methods
     * 
     * @param string $MethodName The suffix of the getter/setter methods
     */
    public function MethodPair($MethodName) {
        $this->AddAccessor(new Accessors\MethodPair('get' . $MethodName, 'set' . $MethodName));
        return $this;
    }
    
    /**
     * Defines the property to be accessed as an indexor
     * 
     * @param mixed $Index
     */
    public function Indexer($Index) {
        $this->AddAccessor(new Accessors\Indexer($Index));
        return $this;
    }
    
    /**
     * Defines the property to be accessed as an invocation
     * 
     * @param array $Arguments
     */
    public function Invocation(array $Arguments = array()) {
        $this->AddAccessor(new Accessors\Invocation($Arguments));
        return $this;
    }
    
    /**
     * Defines the property to be accessed by a custom accesor
     * 
     * @param Accessors\Accessor $Accesor
     */
    public function Custom(Accessors\Accessor $Accesor) {
        $this->AddAccessor($Accesor);
        return $this;
    }
}