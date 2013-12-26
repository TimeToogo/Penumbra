<?php

namespace Storm\Drivers\Base\Object\Properties;

use \Storm\Core\Object;
use \Storm\Core\Object\IProperty;
use \Storm\Core\Object\EntityMap;

abstract class Property implements IProperty {
    private $Identifier;
    private $Accessor;
    private $EntityMap;
    
    public function __construct(Accessors\Accessor $Accessor) {
        $this->Identifier = $Accessor->GetIdentifier();
        $this->Accessor = $Accessor;
    }
    
    final public function GetIdentifier() {
        return $this->Identifier;
    }
    
    /**
     * @return Accessors\Accessor
     */
    final public function GetAccessor() {
        return $this->Identifier;
    }
    
    final public function GetEntityMap() {
        return $this->EntityMap;
    }
    
    final public function HasEntityMap() {
        return $this->EntityMap !== null;
    }
    
    final public function SetEntityMap(EntityMap $EntityMap = null) {
        $this->EntityMap = $EntityMap;
        if($EntityMap !== null) {
            $this->Accessor->SetEntityType($EntityMap->GetEntityType());
            $this->Identifier = $this->Accessor->GetIdentifier();
        }
    }
}

?>
