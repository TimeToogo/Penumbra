<?php

namespace Storm\Drivers\Intelligent\Object\Properties\Accessors;

use Storm\Drivers\Base\Object\Properties\Accessors;

class Accessor extends Accessors\Accessor implements \ArrayAccess {
    /**
     * @var Accessors\Accessor 
     */
    private $Accessor = null;
    
    private function __construct() { }
    
    /**
     * @return \stdClass
     */
    public static function DefineAs(callable $Builder = null) {
        $Self = (true) ? new static() : null;
        if($Builder !== null) {
            $Builder($Self);
        }
        else {
            return $Self;
        }
    }
    
    protected function Identifier(&$Identifier) {
        Accessor::DefineAs(function ($Entity) { $Entity->Data->Foobar['test']; });
        return $this->Accessor->Identifier($Identifier);
    }

    public function GetValue($Entity) {
        return $this->Accessor->GetValue($Entity);
    }

    public function SetValue($Entity, $PropertyValue) {
        return $this->Accessor->SetEntityType($Entity, $PropertyValue);
    }
    
    public function SetEntityType($EntityType) {
        return $this->Accessor->SetEntityType($EntityType);
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
    
    public function __get($FieldName) {
        $this->AddAccessor(new Accessors\Field($FieldName));
        return $this;
    }

    public function __invoke() {
        $this->AddAccessor(new Accessors\Invocation(func_get_args()));
        return $this;
    }
    
    public function offsetGet($Index) { 
        $this->AddAccessor(new Accessors\Indexer($Index));
        return $this;
    }

    public function offsetExists($offset) { }
    public function offsetSet($offset, $value) { }
    public function offsetUnset($offset) {}
}

?>